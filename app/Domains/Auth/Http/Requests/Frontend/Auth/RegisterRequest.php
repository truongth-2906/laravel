<?php

namespace App\Domains\Auth\Http\Requests\Frontend\Auth;

use App\Domains\Auth\Models\User;
use App\Rules\Captcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

/**
 * Class RegisterRequest.
 */
class RegisterRequest extends FormRequest
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
            'type' => ['required', Rule::in([User::TYPE_FREELANCER, User::TYPE_EMPLOYER])],
            'firstname' => ['required', 'max:100'],
            'lastname' => ['required', 'max:100'],
            'email' => ['required', 'max:255', 'email:rfc,dns', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'password' => ['max:100', PasswordRules::register($this->email), 'regex:' . config('regex.password')],
            'password_confirmation' => 'required|same:password',
            'g-recaptcha-response' => ['required_if:captcha_status,true', new Captcha],
            'agree_terms_of_use' => [
                'bail',
                'required',
                'accepted'
            ],
            'calling_code' => [
                'bail',
                'required',
                Rule::exists('countries', 'id')
            ],
            'phone_number' => [
                'bail',
                'required',
                'string',
                'regex:' . config('regex.phone_number'),
                'min:8',
                'max:30'
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'g-recaptcha-response.required_if' => __('validation.required', ['attribute' => 'captcha']),
        ];
    }
}
