<?php

namespace App\Modules\EventModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul event wajib diisi.',
            'description.required' => 'Deskripsi event wajib diisi.',
            'event_date.required' => 'Tanggal & waktu event wajib diisi.',
            'location.required' => 'Lokasi event wajib diisi.',
            'categories.required' => 'Pilih minimal 1 kategori event.',
        ];
    }
}