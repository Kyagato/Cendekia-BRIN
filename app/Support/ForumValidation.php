<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class ForumValidation
{
    public static function threadRules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'knowledge_id' => [
                'nullable',
                Rule::exists('knowledge', 'id')->where(fn ($query) => $query
                    ->where('status', 'Disetujui')
                    ->where('status_akses', 'public')
                    ->whereNull('deleted_at')),
            ],
        ];
    }
}
