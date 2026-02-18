<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['super-admin', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $staffId = $this->route('staff') ? $this->route('staff')->id : null;
        $businessId = $this->input('business_id');

        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('staff')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })->ignore($staffId)
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('staff')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })->ignore($staffId)
            ],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.unique' => 'The employee ID must be unique within the selected business.',
            'email.unique' => 'The email must be unique within the selected business.',
            'business_id.required' => 'Please select a business.',
            'business_id.exists' => 'The selected business is invalid.',
            'role.required' => 'Please select a role.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'business_id' => 'business',
            'employee_id' => 'employee ID',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone' => 'phone number',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure is_active is boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active')
            ]);
        }
    }
}
