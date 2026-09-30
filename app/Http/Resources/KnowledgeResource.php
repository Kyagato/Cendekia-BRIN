<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KnowledgeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'tipe' => $this->tipe,
            'deskripsi' => $this->deskripsi,
            'detail' => $this->detail,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'gambar_sampul' => $this->gambar_sampul ? asset('storage/' . $this->gambar_sampul) : null,
            'penulis' => $this->penulis,
            'kolaborator' => $this->kolaborator,
            'status' => $this->status,
            'status_akses' => $this->status_akses,
            'unggulan' => (bool) $this->unggulan,
            'views_count' => (int) $this->views_count,
            'tanggal_terbit' => $this->tanggal_terbit?->format('Y-m-d'),
            'catatan_penolakan' => $this->catatan_penolakan,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(fn($t) => [
                    'id' => $t->id,
                    'nama_label' => $t->nama_label,
                ]);
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
