<?php

namespace App\Domains\Auth\Http\Requests\Backend\Freelancer;

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

/**
 * Class StoreFreelancerRequest
 * @package App\Domains\Auth\Http\Requests\Backend\User
 */
class StoreFreelancerRequest extends FormRequest
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
            'password' => ['bail', 'max:100', PasswordRules::register($this->email), 'regex:' . config('regex.password')],
            'roles.*' => 'bail', ['bail', Rule::exists('roles', 'id')->where('type', $this->type)],
            'permissions' => ['bail', 'sometimes', 'array'],
            'permissions.*' => ['bail', Rule::exists('permissions', 'id')->where('type', $this->type)],
            'bio' => ['bail', 'nullable', 'max:1000'],
            'experience_id' => ['bail', 'nullable', Rule::exists('experiences', 'id')->where('id', $this->experience_id)],
            'categories' => ['bail', 'nullable', 'array', Rule::exists('categories', 'id')->where('id', $this->categories)],
            'avatar' => ['bail', 'sometimes', 'file', 'max:' . config('upload.user_avatar_max'), 'image', 'mimetypes:' . config('upload.user_avatar_mimes')],
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
            ],
            'calling_code' => [
                'bail',
                'required',
                Rule::exists('countries', 'id')
            ],
            'tag_line' => 'bail|required|string|max:255',
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
            'file_upload.*' => __('Invalid upload file format.'),
            'timezone_id.required' => __('The Timezone field is required.'),
            'country_id.required' => __('The Country field is required.'),
            'experience_id.required' => __('The RPA Experience field is required.'),
            'categories.required' => __('The RPA Software field is required.'),
            'file_name.*.max' => __('The file name must not be greater than 255 characters'),
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
