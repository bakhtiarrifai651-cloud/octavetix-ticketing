<?php

namespace App\Modules\RegistrationModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Tiket yang dipilih tidak valid.',
            'ticket_id.exists' => 'Jenis tiket tidak ditemukan.',
            'quantity.required' => 'Jumlah tiket wajib diisi.',
            'quantity.min' => 'Minimal pemesanan adalah 1 tiket.',
        ];
    }
}
