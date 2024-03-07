<?php

namespace App\Domains\Auth\Http\Requests\Frontend\Freelancer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SettingAvailableRequest.
 */
class SettingAvailableRequest extends FormRequest
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
            'hours' => ['required', 'numeric', 'between:1,168'],
            'rate_per_hours' => ['required', 'numeric', 'min:15', 'max:9999999999'],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'hours.required' => __('The hours per project field is required.'),
            'rate_per_hours.required' => __('The rate per hours field is required.'),
            'hours.between' => __('The hours per project must be between 1 and 168 hours.'),
            'rate_per_hours.min' => __('The rate per hours must be min 15$.'),
            'rate_per_hours.max' => __('The rate per hours must be less than 10 digits.'),
        ];
    }
}
