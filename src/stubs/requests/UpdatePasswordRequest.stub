<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * The default named error bag for failed validation. The settings
     * view scopes its `@error` blocks to this bag so password errors
     * surface inside the Password tab without colliding with the
     * Delete account form.
     */
    protected $errorBag = 'updatePassword';

    /**
     * Redirect target on validation failure — preserve the active tab
     * by appending `?tab=password`.
     */
    protected function redirectRoute(): string
    {
        return 'settings';
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
