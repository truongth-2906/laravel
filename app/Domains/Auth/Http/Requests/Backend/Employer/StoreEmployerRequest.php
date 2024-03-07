<?php

namespace App\Domains\Auth\Http\Requests\Backend\Employer;

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

/**
 * Class StoreUserRequest.
 */
class StoreEmployerRequest extends FormRequest
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
            'firstname' => ['bail', 'required', 'max:100'],
            'lastname' => ['bail', 'required', 'max:100'],
            'type' => [Rule::in([User::TYPE_ADMIN, User::TYPE_FREELANCER, User::TYPE_EMPLOYER])],
            'email' => ['bail', 'required', 'max:255', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'timezone_id' => ['bail', 'nullable', Rule::exists('timezones', 'id')->where('id', $this->timezone_id)],
            'country_id' => ['bail', 'required', Rule::exists('countries', 'id')],
            'sector_id' => ['bail', 'nullable', Rule::exists('sectors', 'id')->where('id', $this->sector_id)],
            'password' => ['bail', 'max:100', PasswordRules::register($this->email), 'regex:' . config('regex.password')],
            'bio' => ['bail', 'nullable', 'max:1000'],
            'company_id' => ['bail', 'required', Rule::exists('companies', 'id')->where('id', $this->company_id)],
            'logo' => ['bail', 'sometimes', 'file', 'max:' . config('upload.user_avatar_max'), 'image', 'mimetypes:' . config('upload.user_avatar_mimes')],
            'roles.*' => [Rule::exists('roles', 'id')->where('type', $this->type)],
            'permissions' => ['bail', 'sometimes', 'array'],
            'permissions.*' => ['bail', Rule::exists('permissions', 'id')->where('type', $this->type)],
            'file_upload' => ['sometimes'],
            'file_upload.*' => ['bail', 'file', 'max:' . config('upload.user_image_max'), 'mimetypes:' . config('upload.user_image')],
            'active' => ['sometimes'],
            'file_name.*' => ['bail', 'required', 'max:255'],
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
            'roles.*.exists' => __('One or more roles were not found or are not allowed to be associated with this user type.'),
            'permissions.*.exists' => __('One or more permissions were not found or are not allowed to be associated with this user type.'),
            'timezone_id.required' => __('The Timezone field is required.'),
            'country_id.required' => __('The Country field is required.'),
            'sector_id.required' => __('The Business sector field is required.'),
            'file_upload.*' => __('Invalid upload file format.'),
            'file_name.*.max' => __('The file name must not be greater than 255 characters.'),
            'file_name.*.required' => __('The file name field is required.'),
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'phone_number' => __('Phone Number'),
        ];
    }
}
