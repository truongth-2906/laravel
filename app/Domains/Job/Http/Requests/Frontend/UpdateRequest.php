<?php

namespace App\Domains\Job\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'timezone_id' => ['required', Rule::exists('timezones', 'id')->where('id', $this->timezone_id)],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'experience_id' => ['required', Rule::exists('experiences', 'id')->where('id', $this->experience_id)],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('id', $this->category_id)],
            'name' => ['required', 'max:30'],
            'status' => ['bail', 'required', 'boolean'],
            'description' => ['bail', 'required', 'string', 'max:1000'],
            'wage' => ['required', 'regex:' . config('regex.wage'), 'numeric', 'between:1,999999999.99'],
            'file_upload' => ['bail', 'nullable', 'array'],
            'file_upload.*' => [
                'bail',
                'file',
                'mimes:doc,DOC,docx,DOCX,pdf,PDF',
                'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword',
                'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
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
