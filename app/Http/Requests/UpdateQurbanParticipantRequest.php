<?php

namespace App\Http\Requests;

use App\Models\QurbanParticipantItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQurbanParticipantRequest extends FormRequest
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
            'full_name' => 'required|string|max:100',
            'nik' => 'nullable|string|max:100',
            'contact_number' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'total_coupon' => 'required|integer|min:1',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'note' => 'nullable|string',
            'status' => ['nullable', Rule::in(['pending', 'taken', 'rejected'])],
            // 'qurban_items' => 'required|array|min:1',
            // 'qurban_items.*' => ['required', Rule::in(QurbanParticipantItem::QURBAN_TYPES)],
            // 'total_coupon' => 'required|array',
            // 'total_coupon.*' => 'required|integer|min:1',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'pending'),
        ]);
    }
}
