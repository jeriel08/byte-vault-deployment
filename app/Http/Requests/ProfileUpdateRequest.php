<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        // Base rules for all fields
        $rules = [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'phoneNumber' => ['nullable', 'string', 'max:11', 'regex:/^(0\d{10}|\+63\d{10})$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ];

        // Only apply the unique rule if the email is different from the current user's email
        if ($this->input('email') !== $user->email) {
            $rules['email'][] = Rule::unique('employees')->ignore($user->id);
        }

        // Apply unique rule for phoneNumber only if it has changed
        if ($this->input('phoneNumber') !== $user->phoneNumber) {
            $rules['phoneNumber'][] = Rule::unique('employees')->ignore($user->id);
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If the email is not being updated, remove the unique rule
        if ($this->email === $this->user()->email) {
            $this->merge([
                'email' => $this->user()->email,
            ]);
        }

        if (!$this->has('phoneNumber')) {
            $this->merge([
                'phoneNumber' => $this->user()->phoneNumber,
            ]);
        }
    }
}
