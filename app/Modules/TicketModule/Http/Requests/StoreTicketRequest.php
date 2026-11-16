<?php

namespace App\Modules\TicketModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama jenis tiket wajib diisi (contoh: VIP / Reguler).',
            'price.required' => 'Harga tiket wajib diisi.',
            'price.min' => 'Harga tiket tidak boleh negatif.',
            'quota.required' => 'Kuota tiket wajib diisi.',
            'quota.min' => 'Kuota tiket minimal 1.',
        ];
    }
}