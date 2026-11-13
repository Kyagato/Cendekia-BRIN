<?php

use App\Models\AuditLog;
use App\Models\KeycloakUserSync;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    config(['services.keycloak.base_url' => 'https://sso.example.test',
        'services.keycloak.realms' => 'TestRealm', 'services.keycloak.client_id' => 'mojopedia',
        'services.keycloak.client_secret' => 'test-secret']);
    Http::preventStrayRequests();
    $this->remote = ['id' => 'remote-1', 'username' => 'reader', 'firstName' => 'Old', 'lastName' => 'Reader',
        'email' => 'old@example.test', 'emailVerified' => true, 'enabled' => true,
        'attributes' => ['instansi' => ['BRIN']], 'requiredActions' => []];
    $this->realmRoles = [];
    $this->clientRoles = [];
    $this->deleteStatus = 204;
    $this->writeStatus = 204;
    $this->passwordStatus = 204;
    $this->passwordTimeout = false;
    $this->ignoreUsername = false;
    $this->directoryStatus = 200;
    Http::fake(function (Request $request) {
        $path = parse_url($request->url(), PHP_URL_PATH);
        if (str_ends_with($path, '/token')) {
            return Http::response(['access_token' => 'test-admin-token']);
        }
        if (str_ends_with($path, '/roles')) return Http::response([
            ['id' => 'member-role', 'name' => 'Anggota'], ['id' => 'mod-role', 'name' => 'Moderator'],
        ]);
        if (str_ends_with($path, '/role-mappings/realm')) {
            if ($request->method() === 'DELETE') {
                if ($this->writeStatus !== 204) return Http::response([], $this->writeStatus);
                $this->realmRoles = [];
                return Http::response(null, 204);
            }
            if ($request->method() === 'POST') { $this->realmRoles = $request->data(); return Http::response(null, 204); }
            return Http::response($this->realmRoles);
        }
        if (str_ends_with($path, '/role-mappings/clients/app-client')) return Http::response([]);
        if (str_ends_with($path, '/clients')) {
            return Http::response([['id' => 'app-client', 'clientId' => 'mojopedia']]);
        }
        if (str_ends_with($path, '/role-mappings/realm/composite')) {
            return Http::response($this->realmRoles, $this->directoryStatus);
        }
        if (str_ends_with($path, '/role-mappings/clients/app-client/composite')) {
            return Http::response($this->clientRoles, $this->directoryStatus);
        }
        if (str_ends_with($path, '/users/count')) {
            return Http::response(21, $this->directoryStatus);
        }
        if (str_ends_with($path, '/users')) {
            return Http::response([$this->remote], $this->directoryStatus);
        }
        if (str_ends_with($path, '/users/remote-1/reset-password')) {
            if ($this->passwordTimeout) {
                throw new ConnectionException('Simulated timeout');
            }

            return Http::response(null, $this->passwordStatus);
        }
        if (str_ends_with($path, '/users/remote-1')) {
            if ($request->method() === 'DELETE') {
                return Http::response(null, $this->deleteStatus);
            }
            if ($request->method() === 'PUT') {
                if ($this->writeStatus === 204) {
                    $oldUsername = $this->remote['username'];
                    $this->remote = array_merge($this->remote, $request->data());
                    if ($this->ignoreUsername) {
                        $this->remote['username'] = $oldUsername;
                    }
                }

                return Http::response(null, $this->writeStatus);
            }

            return Http::response($this->remote, $this->directoryStatus);
        }

        return Http::response([], 404);
    });
    $this->fields = ['username' => 'reader', 'firstName' => 'New', 'lastName' => 'Name', 'email' => 'new@example.test'];
    $this->updateUrl = '/admin/users/keycloak/remote-1';
});

it('shows remote users search and pagination to all three admins without creating local accounts', function ($role) {
    $actor = User::factory()->create(['role' => $role]);
    $this->actingAs($actor)->get('/admin/users?q=reader&page=2')->assertOk()
        ->assertSee('old@example.test')->assertSee('reader')->assertSee('Edit');
    expect(User::count())->toBe(1);
    Http::assertSent(fn (Request $request) => str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/users')
        && $request['search'] === 'reader' && $request['first'] === 10 && $request['max'] === 10);
})->with(['Super Admin', 'Admin Pusat', 'Admin']);

it('blocks non-admins and guests without contacting Keycloak', function ($role) {
    if ($role) {
        $this->actingAs(User::factory()->create(['role' => $role]));
    }
    $this->getJson('/admin/users')->assertStatus($role ? 403 : 401);
    $this->putJson($this->updateUrl, $this->fields)->assertStatus($role ? 403 : 401);
    Http::assertNothingSent();
})->with([null, 'Anggota', 'Moderator', 'Analisis Pengetahuan']);

it('updates the remote identity and linked local user with an audit record', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1', 'name' => 'Old Reader', 'email' => 'old@example.test']);
    $hash = $local->password;
    $this->actingAs($actor)->put($this->updateUrl, $this->fields)->assertSessionHasNoErrors()->assertRedirect('/admin/users');
    expect($local->fresh()->name)->toBe('New Name')->and($local->fresh()->email)->toBe('new@example.test')
        ->and($local->fresh()->email_verified_at)->toBeNull()->and($local->fresh()->password)->toBe($hash);
    Http::assertSent(fn (Request $request) => $request->method() === 'PUT' && $request['emailVerified'] === false
        && $request['attributes'] === ['instansi' => ['BRIN']] && $request['username'] === 'reader'
        && ! isset($request['credentials']) && ! isset($request['realmRoles']));
    expect(KeycloakUserSync::sole()->status)->toBe('synced');
    $audit = AuditLog::where('action', 'KEYCLOAK_USER_UPDATE')->sole();
    expect($audit->user_id)->toBe($actor->id)->and($audit->properties['after']['email'])->toBe('new@example.test');
});

it('edits an unlinked remote user without silently linking a local email match', function () {
    $actor = User::factory()->create(['role' => 'Admin Pusat']);
    $sameEmail = User::factory()->create(['name' => 'Unlinked', 'email' => 'old@example.test']);
    $this->actingAs($actor)->get($this->updateUrl.'/edit')->assertOk()->assertSee('old@example.test');
    $this->put($this->updateUrl, $this->fields)->assertSessionHasNoErrors();
    expect(User::count())->toBe(2)->and($sameEmail->fresh()->name)->toBe('Unlinked')->and($sameEmail->fresh()->keycloak_id)->toBeNull();
});

it('enforces effective remote roles and the admin hierarchy', function ($actorRole, $targetRole, $allowed) {
    $this->realmRoles = [['name' => $targetRole]];
    $actor = User::factory()->create(['role' => $actorRole]);
    $response = $this->actingAs($actor)->put($this->updateUrl, $this->fields);
    if ($allowed) {
        $response->assertSessionHasNoErrors()->assertRedirect('/admin/users');
    } else {
        $response->assertForbidden();
        Http::assertNotSent(fn (Request $request) => $request->method() === 'PUT');
    }
})->with([
    ['Admin', 'Admin', false], ['Admin', 'Admin Pusat', false], ['Admin Pusat', 'Admin Pusat', false],
    ['Admin Pusat', 'Super Admin', false], ['Admin Pusat', 'Admin', true], ['Super Admin', 'Super Admin', true],
]);

it('blocks self editing and linked local higher roles', function ($scenario) {
    $actor = User::factory()->create(['role' => 'Admin', 'keycloak_id' => $scenario === 'self' ? 'remote-1' : 'actor']);
    if ($scenario === 'local higher') {
        User::factory()->create(['role' => 'Super Admin', 'keycloak_id' => 'remote-1']);
    }
    if ($scenario === 'client higher') {
        $this->clientRoles = [['name' => 'admin_pusat']];
    }
    if ($scenario === 'service account') {
        $this->remote['username'] = 'service-account-automation';
    }
    $this->actingAs($actor)->put($this->updateUrl, $this->fields)->assertForbidden();
    Http::assertNotSent(fn (Request $request) => $request->method() === 'PUT');
})->with(['self', 'local higher', 'client higher', 'service account']);

it('rejects unsupported edits and local email collisions before any remote update', function ($scenario) {
    $actor = User::factory()->create(['role' => 'Super Admin']);
    if ($scenario === 'email') {
        User::factory()->create(['email' => 'new@example.test']);
    }
    $payload = $this->fields + ($scenario === 'role' ? ['enabled' => true] : []);
    $this->actingAs($actor)->putJson($this->updateUrl, $payload)->assertUnprocessable();
    Http::assertNotSent(fn (Request $request) => $request->method() === 'PUT');
})->with(['email', 'role']);

it('does not update local data or report success when Keycloak rejects an edit', function ($status) {
    $this->writeStatus = $status;
    $local = User::factory()->create(['keycloak_id' => 'remote-1', 'name' => 'Old Reader', 'email' => 'old@example.test']);
    $actor = User::factory()->create(['role' => 'Admin']);
    $this->actingAs($actor)->from($this->updateUrl.'/edit')->put($this->updateUrl, $this->fields)
        ->assertSessionHasErrors('keycloak')->assertSessionMissing('success');
    expect($local->fresh()->email)->toBe('old@example.test')
        ->and(KeycloakUserSync::sole()->status)->toBe($status >= 500 ? 'reconciliation_required' : 'remote_failed');
})->with([400, 403, 409, 500]);

it('displays a useful unavailable state when the service cannot read users', function () {
    $this->directoryStatus = 403;
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/users')
        ->assertStatus(503)->assertSee('Coba lagi')->assertDontSee('test-secret')->assertDontSee('test-admin-token');
});

it('records local sync failures and can safely reconcile them later', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1', 'name' => 'Old Reader', 'email' => 'old@example.test']);
    $event = 'eloquent.updating: '.User::class;
    Event::listen($event, function (User $user) use ($local) {
        if ($user->id === $local->id) {
            throw new RuntimeException('Simulated local failure');
        }
    });
    try {
        $this->actingAs($actor)->put($this->updateUrl, $this->fields)->assertSessionHasErrors('keycloak')->assertSessionMissing('success');
    } finally {
        Event::forget($event);
    }
    expect(KeycloakUserSync::sole()->status)->toBe('reconciliation_required')->and($local->fresh()->email)->toBe('old@example.test');
    $this->artisan('keycloak:reconcile-users')->assertSuccessful();
    expect(KeycloakUserSync::sole()->status)->toBe('synced')->and($local->fresh()->email)->toBe('new@example.test');
});

it('does not overwrite a newer remote edit during reconciliation', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $sync = KeycloakUserSync::create(['keycloak_user_id' => 'remote-1', 'actor_id' => $actor->id,
        'before' => [], 'after' => $this->fields + ['emailVerified' => false], 'status' => 'reconciliation_required']);
    $this->artisan('keycloak:reconcile-users')->assertFailed();
    expect($sync->fresh()->status)->toBe('requires_review');
    Http::assertNotSent(fn (Request $request) => $request->method() === 'PUT');
});

it('blocks the old local update and delete endpoints for linked identities', function () {
    $actor = User::factory()->create(['role' => 'Super Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $this->actingAs($actor)->get('/admin/users/'.$local->id.'/edit')->assertRedirect($this->updateUrl.'/edit');
    $this->put('/admin/users/'.$local->id, [])->assertForbidden();
    $this->delete('/admin/users/'.$local->id)->assertForbidden();
    Http::assertNothingSent();
});

it('updates username in Keycloak and the linked local account and verifies the remote response', function () {
    $local = User::factory()->create(['keycloak_id' => 'remote-1', 'username' => 'reader', 'email' => 'old@example.test']);
    $actor = User::factory()->create(['role' => 'Admin']);
    $fields = array_merge($this->fields, ['username' => 'new.reader']);
    $this->actingAs($actor)->put($this->updateUrl, $fields)->assertSessionHasNoErrors();
    expect($this->remote['username'])->toBe('new.reader')->and($local->fresh()->username)->toBe('new.reader');
    expect(KeycloakUserSync::sole()->after['username'])->toBe('new.reader');
});

it('does not report success if Keycloak silently ignores a username change', function () {
    $this->ignoreUsername = true;
    $actor = User::factory()->create(['role' => 'Admin']);
    $this->actingAs($actor)->put($this->updateUrl, array_merge($this->fields, ['username' => 'changed']))
        ->assertSessionHasErrors('keycloak')->assertSessionMissing('success');
    expect(KeycloakUserSync::sole()->status)->toBe('requires_review');
});

it('shows linked profile pictures with initials for unlinked users', function () {
    User::factory()->create(['keycloak_id' => 'remote-1', 'foto_profil' => 'profile_photos/avatar.png']);
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/users')
        ->assertOk()->assertSee('storage/profile_photos/avatar.png', false);
});

it('changes the Keycloak and local passwords and revokes old local credentials without logging secrets', function () {
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $local->createToken('old-token');
    $actor = User::factory()->create(['role' => 'Admin']);
    $password = 'New-Pass-123!';
    $this->actingAs($actor)->from($this->updateUrl.'/edit')->put($this->updateUrl.'/password', [
        'password' => $password, 'password_confirmation' => $password,
    ])->assertSessionHasNoErrors()->assertSessionHas('success');
    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/reset-password')
        && $request['value'] === $password && $request['type'] === 'password' && $request['temporary'] === false);
    expect(Hash::check($password, $local->fresh()->password))->toBeTrue()
        ->and($local->tokens()->count())->toBe(0);
    $sync = KeycloakUserSync::sole();
    expect($sync->operation)->toBe('password')->and($sync->status)->toBe('synced')
        ->and($sync->local_password_hash)->toBeNull()->and(json_encode($sync->toArray()))->not->toContain($password);
    expect(AuditLog::where('action', 'KEYCLOAK_PASSWORD_UPDATE')->count())->toBe(1);
    foreach (AuditLog::all() as $audit) {
        expect(json_encode($audit->toArray()))->not->toContain($password);
    }
});

it('does not change the local password when Keycloak rejects or cannot confirm the reset', function ($status) {
    $this->passwordStatus = $status;
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $hash = $local->password;
    $local->createToken('keep-token');
    $actor = User::factory()->create(['role' => 'Admin']);
    $this->actingAs($actor)->put($this->updateUrl.'/password', ['password' => 'Rejected-123!', 'password_confirmation' => 'Rejected-123!'])
        ->assertSessionHasErrorsIn('passwordUpdate')->assertSessionMissing('success');
    expect($local->fresh()->password)->toBe($hash)->and($local->tokens()->count())->toBe(1)
        ->and(KeycloakUserSync::sole()->local_password_hash)->toBeNull();
})->with([400, 403, 500]);

it('treats a password reset timeout as uncertain rather than applying a local-only change', function () {
    $this->passwordTimeout = true;
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $hash = $local->password;
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->put($this->updateUrl.'/password', [
        'password' => 'Timeout-123!', 'password_confirmation' => 'Timeout-123!',
    ])->assertSessionHasErrorsIn('passwordUpdate')->assertSessionMissing('success');
    expect($local->fresh()->password)->toBe($hash)->and(KeycloakUserSync::sole()->status)->toBe('requires_review')
        ->and(KeycloakUserSync::sole()->remote_confirmed)->toBeFalse();
});

it('validates confirmation and prevents self or higher role password resets', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $this->actingAs($actor)->put($this->updateUrl.'/password', [
        'password' => 'Mismatch-123!', 'password_confirmation' => 'different',
    ])->assertSessionHasErrorsIn('passwordUpdate');
    $this->realmRoles = [['name' => 'Admin Pusat']];
    $this->put($this->updateUrl.'/password', ['password' => 'Good-Pass-123!', 'password_confirmation' => 'Good-Pass-123!'])
        ->assertForbidden();
    Http::assertNotSent(fn (Request $request) => str_ends_with($request->url(), '/reset-password'));
});

it('recovers a confirmed password change using an encrypted hash after a local failure', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $event = 'eloquent.updating: '.User::class;
    Event::listen($event, function (User $user) use ($local) {
        if ($user->id === $local->id) {
            throw new RuntimeException('Simulated local failure');
        }
    });
    try {
        $this->actingAs($actor)->put($this->updateUrl.'/password', ['password' => 'Recovery-123!', 'password_confirmation' => 'Recovery-123!'])
            ->assertSessionHasErrorsIn('passwordUpdate')->assertSessionMissing('success');
    } finally {
        Event::forget($event);
    }
    $sync = KeycloakUserSync::sole();
    expect($sync->status)->toBe('reconciliation_required')->and($sync->remote_confirmed)->toBeTrue()
        ->and($sync->getRawOriginal('local_password_hash'))->not->toBe($sync->local_password_hash)
        ->and($sync->getRawOriginal('local_password_hash'))->not->toContain('Recovery-123!');
    $this->artisan('keycloak:reconcile-users')->assertSuccessful();
    expect(Hash::check('Recovery-123!', $local->fresh()->password))->toBeTrue()
        ->and($sync->fresh()->local_password_hash)->toBeNull();
});

it('never recovers an older password hash over a newer confirmed password change', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $old = KeycloakUserSync::create(['keycloak_user_id' => 'remote-1', 'actor_id' => $actor->id,
        'before' => [], 'after' => [], 'operation' => 'password', 'status' => 'reconciliation_required',
        'remote_confirmed' => true, 'local_password_hash' => Hash::make('Older-123!')]);
    $this->actingAs($actor)->put($this->updateUrl.'/password', ['password' => 'Newest-123!', 'password_confirmation' => 'Newest-123!'])
        ->assertSessionHasNoErrors();
    $this->artisan('keycloak:reconcile-users')->assertFailed();
    expect($old->fresh()->status)->toBe('requires_review')
        ->and(Hash::check('Newest-123!', $local->fresh()->password))->toBeTrue();
});

it('deletes a remote account and linked local user only after confirmation', function () {
    $actor = User::factory()->create(['role' => 'Admin']);
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $local->createToken('old');
    $this->actingAs($actor)->delete($this->updateUrl, ['confirm_delete' => 1])->assertSessionHasNoErrors()->assertRedirect('/admin/users');
    expect(User::find($local->id))->toBeNull()->and(KeycloakUserSync::sole()->status)->toBe('synced');
    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE' && str_ends_with($request->url(), '/users/remote-1'));
    expect(AuditLog::where('action', 'KEYCLOAK_USER_DELETE')->count())->toBe(1);
});

it('preserves the local account if Keycloak rejects deletion', function () {
    $this->deleteStatus = 403;
    $local = User::factory()->create(['keycloak_id' => 'remote-1']);
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->delete($this->updateUrl, ['confirm_delete' => 1])->assertSessionHasErrors('keycloak');
    expect(User::find($local->id))->not->toBeNull()->and(KeycloakUserSync::sole()->status)->toBe('remote_failed');
});

it('blocks self deletion and deletion without confirmation', function () {
    $actor = User::factory()->create(['role' => 'Super Admin', 'keycloak_id' => 'remote-1']);
    $this->actingAs($actor)->deleteJson($this->updateUrl, [])->assertUnprocessable();
    $this->delete($this->updateUrl, ['confirm_delete' => 1])->assertForbidden();
    Http::assertNotSent(fn (Request $request) => $request->method() === 'DELETE');
});


it('changes the role in Keycloak and locally and confirms it by reading effective roles', function () {
    $local = User::factory()->create(['keycloak_id' => 'remote-1', 'role' => 'Anggota']);
    $this->realmRoles = [['id' => 'member-role', 'name' => 'Anggota']];
    $actor = User::factory()->create(['role' => 'Admin']);
    $this->actingAs($actor)->put($this->updateUrl, $this->fields + ['role' => 'Moderator'])
        ->assertSessionHasNoErrors()->assertRedirect('/admin/users');
    expect($local->fresh()->role)->toBe('Moderator')
        ->and(KeycloakUserSync::sole()->after['role'])->toBe('Moderator');
    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && str_ends_with($request->url(), '/role-mappings/realm') && $request->data()[0]['name'] === 'Moderator');
});

it('blocks promotion above an admins own assignment authority', function () {
    $this->actingAs(User::factory()->create(['role' => 'Admin']))
        ->putJson($this->updateUrl, $this->fields + ['role' => 'Super Admin'])->assertUnprocessable();
    Http::assertNotSent(fn (Request $request) => $request->method() === 'PUT');
});
