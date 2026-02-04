<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_phone' => 'required|string|max:20',
            'amount' => 'required|integer|min:1000',
            'donation_type' => 'required|string',
            'note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'donor_name.required' => 'Nama donatur wajib diisi',
            'donor_email.required' => 'Email wajib diisi',
            'donor_email.email' => 'Format email tidak valid',
            'donor_phone.required' => 'Nomor HP wajib diisi',
            'amount.required' => 'Nominal donasi wajib diisi',
            'amount.min' => 'Minimal donasi adalah Rp 1.000',
            'donation_type.required' => 'Jenis donasi wajib dipilih',
        ];
    }
}
