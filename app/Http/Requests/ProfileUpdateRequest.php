<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan,L,P'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'remove_foto_profil' => ['nullable'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'max:50'],
        ];
    }
}
