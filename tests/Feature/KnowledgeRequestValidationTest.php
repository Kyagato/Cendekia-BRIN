<?php

use App\Http\Requests\StoreKnowledgeRequest;
use App\Http\Requests\UpdateKnowledgeRequest;
use App\Models\Knowledge;
use Illuminate\Routing\Route;

it('preserves upload requirements when creating or editing knowledge', function ($requestClass, $type, $existing, $field, $expected) {
    $request = $requestClass::create('/', 'POST', ['tipe' => $type]);
    $route = new Route('POST', '/', fn () => null);
    $route->bind($request);
    $route->setParameter('knowledge', new Knowledge($existing));
    $request->setRouteResolver(fn () => $route);

    $rules = $request->rules()[$field];
    $requirement = is_array($rules) ? $rules[0] : explode('|', $rules)[0];

    expect($requirement)->toBe($expected);
})->with([
    'new image' => [StoreKnowledgeRequest::class, 'Gambar', [], 'file_upload', 'required'],
    'existing image' => [UpdateKnowledgeRequest::class, 'Gambar', ['file_path' => 'images/cover.png'], 'file_upload', 'nullable'],
    'missing image on update' => [UpdateKnowledgeRequest::class, 'Gambar', [], 'file_upload', 'required'],
    'new audio' => [StoreKnowledgeRequest::class, 'Audio', [], 'audio_file', 'required'],
    'existing audio URL' => [UpdateKnowledgeRequest::class, 'Audio', ['url_teks' => 'https://example.com/audio.mp3'], 'audio_file', 'nullable'],
    'existing audio file' => [UpdateKnowledgeRequest::class, 'Audio', ['file_path' => 'audio/clip.mp3'], 'audio_file', 'nullable'],
    'image is not existing audio' => [UpdateKnowledgeRequest::class, 'Audio', ['file_path' => 'images/cover.png'], 'audio_file', 'required'],
]);

it('only bypasses validation for cancelling an existing submission', function () {
    $store = StoreKnowledgeRequest::create('/', 'POST', ['batal_ajukan' => '1']);
    $update = UpdateKnowledgeRequest::create('/', 'POST', ['batal_ajukan' => '1']);

    expect($store->rules())->toHaveKey('judul')
        ->and($update->rules())->toBe([]);
});

it('preserves the different allowed statuses for create and update', function () {
    expect((new StoreKnowledgeRequest)->rules()['status'])->toBe('nullable|string|in:Draft,Diajukan,Disetujui')
        ->and((new UpdateKnowledgeRequest)->rules()['status'])->toBe('nullable|string|in:Draft,Diajukan');
});
