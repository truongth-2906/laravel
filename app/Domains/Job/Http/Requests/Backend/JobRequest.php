<?php

namespace App\Domains\Job\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', Rule::exists('companies', 'id')->where('id', $this->company_id)],
            'user_id' => ['required', Rule::exists('users', 'id')->where('id', $this->user_id)],
            'timezone_id' => ['required', Rule::exists('timezones', 'id')->where('id', $this->timezone_id)],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'experience_id' => ['required', Rule::exists('experiences', 'id')->where('id', $this->experience_id)],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('id', $this->category_id)],
            'status' => ['bail', 'required', 'boolean'],
            'wage' => ['required', 'regex:' . config('regex.wage'), 'numeric', 'between:1,999999999.99'],
            'name' => ['required', 'max:30'],
            'file_upload' => ['sometimes'],
            'file_upload.*' => ['file', 'max:' . config('upload.user_image_max'), 'mimetypes:' . config('upload.user_image')],
            'file_name.*' => ['required', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => __('Company'),
            'user_id' => __('Employer'),
            'name' => __('Job title'),
            'country_id' => __('Country'),
            'experience_id' => __('RPA Experience'),
            'category_id' => __('RPA Software'),
            'status' => __('Job status'),
            'description' => __('Job Description'),
            'wage' => __('Amount'),
            'timezone_id' => __('Timezone for working'),
            'file_upload' => __('Job documents'),
            'file_name.*.max' => __('The file name must not be greater than 255 characters.'),
            'file_name.*.required' => __('The file name field is required.'),
        ];
    }
}
