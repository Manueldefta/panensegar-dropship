<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'min:10'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'shipping_address.min' => 'Alamat pengiriman terlalu singkat, tuliskan minimal 10 karakter.',
        ];
    }
}