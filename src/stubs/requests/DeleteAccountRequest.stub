<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    /**
     * The default named error bag for failed validation. The settings
     * view scopes its `@error` blocks to this bag so delete-account
     * errors surface inside the Danger zone tab without colliding with
     * the Update password form.
     */
    protected $errorBag = 'deleteAccount';

    /**
     * Redirect target on validation failure.
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}
