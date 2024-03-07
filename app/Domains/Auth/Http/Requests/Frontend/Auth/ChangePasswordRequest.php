<?php

namespace App\Domains\Auth\Http\Requests\Frontend\Auth;

use App\Domains\Auth\Rules\MatchOldPassword;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePasswordRequest.
 */
class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => [
                'required',
                new MatchOldPassword(),
            ],
            'password' => [
                'required',
                'max:100',
                'regex:' . config('regex.password')
            ],
            'confirm_password' => 'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => __(
                'The password must be more than 8 characters long, should contain at-least 1 Uppercase, 1 Lowercase and 1 Numeric.'
            )
        ];
    }
}
