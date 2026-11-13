<?php

use App\Models\User;
use App\Services\KeycloakUserDirectory;
use Illuminate\Support\Facades\Http;

it('sorts the full directory before pagination', function ($sort, $firstId) {
    $records = [];
    for ($i = 1; $i <= 12; $i++) {
        $records[] = ['id' => 'id'.$i, 'username' => 'user'.$i, 'firstName' => sprintf('User %02d', 13 - $i)];
    }
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('allUsers')->once()->with('')->andReturn($records);
    $directory->shouldReceive('role')->andReturnUsing(fn ($id) => $id === 'id1' ? 'Super Admin' : 'Anggota');
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/users?sort='.$sort)
        ->assertOk()->assertViewHas('users', fn ($users) => $users->total() === 12
            && $users->count() === 10 && $users->items()[0]['id'] === $firstId);
})->with([['name_asc', 'id12'], ['name_desc', 'id1'], ['role', 'id1']]);

it('combines role filtering with sorting and preserves the query in pagination links', function () {
    $records = [];
    for ($i = 1; $i <= 12; $i++) $records[] = ['id' => 'id'.$i, 'firstName' => sprintf('User %02d', 13 - $i)];
    $directory = Mockery::mock(KeycloakUserDirectory::class);
    $directory->shouldReceive('allUsers')->with('test')->andReturn($records);
    $directory->shouldReceive('role')->andReturnUsing(fn ($id) => $id === 'id1' ? 'Super Admin' : 'Anggota');
    $this->app->instance(KeycloakUserDirectory::class, $directory);
    $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/users?q=test&role=Anggota&sort=name_asc')
        ->assertOk()->assertViewHas('users', fn ($users) => $users->total() === 11
            && $users->items()[0]['id'] === 'id12' && str_contains($users->url(2), 'role=Anggota')
            && str_contains($users->url(2), 'sort=name_asc'));
});

it('fetches every remote batch for global sorting', function () {
    config(['services.keycloak.base_url' => 'https://sso.test', 'services.keycloak.realms' => 'demo',
        'services.keycloak.client_id' => 'app', 'services.keycloak.client_secret' => 'test']);
    Http::preventStrayRequests();
    Http::fake(function ($request) {
        $path = parse_url($request->url(), PHP_URL_PATH);
        if (str_ends_with($path, '/token')) return Http::response(['access_token' => 'test']);
        if (str_ends_with($path, '/count')) return Http::response(120);
        $start = (int) ($request['first'] ?? 0);
        return Http::response(array_map(fn ($i) => ['id' => 'id'.$i], range($start, min($start + 99, 119))));
    });
    expect(app(KeycloakUserDirectory::class)->allUsers(''))->toHaveCount(120);
});
