<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Services\PasswordSyncService;
use Illuminate\Support\Facades\Hash;

beforeEach(function () { $this->withoutVite(); });

it('allows all three admin roles to view all audit activity', function ($role) {
    $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN, 'name' => 'Pelaku Super']);
    $viewer = User::factory()->create(['role' => $role]);
    AuditLog::record('KEYCLOAK_USER_UPDATE', 'Perubahan akun oleh Super Admin', ['target_user_id' => 123], $super);
    $this->actingAs($viewer)->get(route('admin.audit.index'))->assertOk()->assertSee('Pelaku Super')->assertSee('Perubahan akun oleh Super Admin');
})->with([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PUSAT, User::ROLE_ADMIN]);

it('denies the audit page to guests and other roles', function () {
    $this->get(route('admin.audit.index'))->assertRedirect();
    foreach ([User::ROLE_ANGGOTA, User::ROLE_ANALIS, User::ROLE_MODERATOR] as $role) {
        $this->actingAs(User::factory()->create(['role' => $role]))->get(route('admin.audit.index'))->assertForbidden();
    }
});

it('filters by WIB date, actor, activity and result', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Pelaku Dicari']);
    $other = User::factory()->create(['name' => 'Pelaku Lain']);
    $good = AuditLog::record('LOGIN_FAILED', 'Catatan yang sesuai', [], $admin);
    $good->forceFill(['created_at' => '2026-11-16 18:00:00'])->save(); // 17 November, 01:00 WIB
    $different = AuditLog::record('LOGIN', 'Catatan yang berbeda', [], $other);
    $different->forceFill(['created_at' => '2026-11-16 18:00:00'])->save();
    $this->actingAs($admin)->get(route('admin.audit.index', ['from' => '2026-11-17', 'to' => '2026-11-17', 'actor' => 'Dicari', 'action' => 'LOGIN_FAILED', 'result' => 'failed']))
        ->assertOk()->assertSee('Catatan yang sesuai')->assertDontSee('Catatan yang berbeda')->assertSee('17 Nov 2026, 01:00');
    $this->get(route('admin.audit.index', ['to' => '2026-11-17']))->assertOk();
});

it('redacts secret fields when recording and never displays unknown historical properties', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $log = AuditLog::record('KEYCLOAK_USER_UPDATE', 'Perubahan akun', ['password' => 'rahasia-baru', 'before' => ['access_token' => 'token-baru', 'role' => 'Anggota']], $admin);
    expect($log->properties['password'])->toBe('[REDACTED]')->and($log->properties['before']['access_token'])->toBe('[REDACTED]');
    $log->forceFill(['properties' => ['password' => 'rahasia-lama', 'before' => ['client_secret' => 'secret-lama', 'role' => 'Anggota']]])->save();
    $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk()->assertSee('Anggota')->assertDontSee('rahasia-lama')->assertDontSee('secret-lama');
});

it('records a profile password update without recording the password', function () {
    $user = User::factory()->create(['password' => Hash::make('password-lama')]);
    app(PasswordSyncService::class)->update($user, 'password-rahasia-baru');
    $log = AuditLog::where('action', 'PASSWORD_UPDATE')->latest('id')->firstOrFail();
    expect(json_encode($log->toArray()))->not->toContain('password-rahasia-baru');
});

it('does not expose audit mutation endpoints', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $this->actingAs($admin)->delete('/admin/audit-log/1')->assertNotFound();
});


it('records a verified OTP password reset without the submitted password', function () {
    $user = User::factory()->create();
    $this->withSession(['otp_verified' => true, 'reset_email' => $user->email, 'reset_otp_expires' => time() + 300])
        ->post('/forgot-password/reset', ['password' => 'rahasia-reset-123', 'password_confirmation' => 'rahasia-reset-123'])
        ->assertRedirect(route('login'));
    $log = AuditLog::where('action', 'PASSWORD_RESET_LOCAL')->latest('id')->firstOrFail();
    expect($log->properties['target_user_id'])->toBe($user->id)
        ->and(json_encode($log->toArray()))->not->toContain('rahasia-reset-123');
});


it('shows Indonesian activity labels in the table and filter while filtering by stored code', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    AuditLog::record('LOGIN_FAILED', 'Contoh masuk gagal', [], $admin);
    AuditLog::record('KNOWLEDGE_APPROVE', 'Contoh persetujuan', [], $admin);
    $this->actingAs($admin)->get(route('admin.audit.index', ['action' => 'LOGIN_FAILED']))
        ->assertOk()->assertSee('Gagal masuk')->assertSee('Menyetujui pengetahuan')
        ->assertSee('value="LOGIN_FAILED"', false)->assertSee('Contoh masuk gagal')->assertDontSee('Contoh persetujuan');
});


it('omits unidentified activities from the filter while preserving their audit records', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    AuditLog::record('CUSTOM_ACTION', 'Catatan lama tetap terlihat', [], $admin);
    $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk()
        ->assertDontSee('Aktivitas lainnya')->assertDontSee('value="CUSTOM_ACTION"', false)
        ->assertSee('Aktivitas: CUSTOM_ACTION')->assertSee('Catatan lama tetap terlihat');
    expect(AuditLog::where('action', 'CUSTOM_ACTION')->exists())->toBeTrue();
});


it('sorts the activity dropdown alphabetically by its Indonesian labels', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    AuditLog::record('LOGIN', 'Masuk sistem', [], $admin);
    AuditLog::record('LOGIN_FAILED', 'Percobaan masuk gagal', [], $admin);
    AuditLog::record('LOGOUT', 'Keluar sistem', [], $admin);
    $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk()
        ->assertSeeInOrder(['value="LOGIN_FAILED"', 'value="LOGOUT"', 'value="LOGIN"'], false);
});
