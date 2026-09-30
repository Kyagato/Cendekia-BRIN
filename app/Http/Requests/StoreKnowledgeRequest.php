<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKnowledgeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'judul'          => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'tipe'           => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi'      => 'nullable|string',
            'detail'         => 'nullable|string',
            'tanggal_terbit' => 'nullable|date',
            'status_akses'   => 'nullable|string|in:public,private',
            'tags'           => 'nullable|string',
            'penulis'        => 'nullable|string|max:255',
            'kolaborator'    => 'nullable|string|max:255',
            'unggulan'       => 'nullable',
            'status'         => 'nullable|string|in:Draft,Diajukan,Disetujui',
        ];

        if ($this->input('tipe') === 'Gambar') {
            $rules['file_upload'] = 'required|image|max:5120';
        } elseif ($this->input('tipe') === 'Audio') {
            $rules['audio_file'] = [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'mp4', 'webm', 'mpga'];
                        if (!in_array($ext, $allowed)) {
                            $fail('File audio harus berformat MP3, WAV, OGG, M4A, AAC, atau FLAC.');
                        }
                    }
                }
            ];
            $rules['file_upload'] = 'nullable|image|max:5120';
        } else {
            $rules['file_upload'] = 'nullable|file|max:51200';
            $rules['url_teks']    = 'nullable|url|max:255';
        }

        return $rules;
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'audio_file.required'  => 'File audio wajib diupload untuk format Audio.',
            'file_upload.required' => 'File gambar wajib diupload untuk format Gambar.',
            'file_upload.image'    => 'Thumbnail harus berupa gambar (PNG, JPG, GIF, WEBP).',
            'audio_file.max'       => 'Ukuran file audio tidak boleh lebih dari 10MB.',
            'file_upload.max'      => 'Ukuran thumbnail gambar tidak boleh lebih dari 5MB.',
        ];
    }
}
