<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class KnowledgeRequest extends FormRequest
{
    protected const AUDIO_EXTENSIONS = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'mp4', 'webm', 'mpga'];

    abstract protected function isUpdate(): bool;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare inputs for validation by sanitizing rich text HTML.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('deskripsi')) {
            $merge['deskripsi'] = \App\Support\HtmlSanitizer::clean($this->input('deskripsi'));
        }
        if ($this->has('detail')) {
            $merge['detail'] = \App\Support\HtmlSanitizer::clean($this->input('detail'));
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Jika hanya aksi batalkan pengajuan
        if ($this->isUpdate() && $this->has('batal_ajukan')) {
            return [];
        }

        $knowledge = $this->isUpdate() ? $this->route('knowledge') : null;

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
            'status'         => $this->isUpdate()
                ? 'nullable|string|in:Draft,Diajukan'
                : 'nullable|string|in:Draft,Diajukan,Disetujui',
        ];

        if ($this->input('tipe') === 'Gambar') {
            $rules['file_upload'] = ($knowledge && $knowledge->file_path)
                ? 'nullable|image|max:2048'
                : 'required|image|max:2048';
        } elseif ($this->input('tipe') === 'Audio') {
            $hasAudio = false;
            if ($knowledge) {
                $hasAudio = !empty($knowledge->url_teks) || (
                    !empty($knowledge->file_path) &&
                    in_array(strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION)), self::AUDIO_EXTENSIONS)
                );
            }

            $rules['audio_file'] = [
                $hasAudio ? 'nullable' : 'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = self::AUDIO_EXTENSIONS;
                        if (!in_array($ext, $allowed)) {
                            $fail('File audio harus berformat MP3, WAV, OGG, M4A, AAC, atau FLAC.');
                        }
                    }
                }
            ];
            $rules['file_upload'] = 'nullable|image|max:2048';
        } else {
            $rules['file_upload'] = 'nullable|file|max:2048';
            $rules['url_teks']    = 'nullable|url|max:255';
        }

        $rules['document_file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:5120';

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
            'file_upload.max'      => 'Ukuran file thumbnail / lampiran tidak boleh lebih dari 2MB.',
            'document_file.max'    => 'Ukuran file dokumen tidak boleh lebih dari 5MB.',
            'document_file.mimes'  => 'Format dokumen harus berupa PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, atau TXT.',
        ];
    }
}
