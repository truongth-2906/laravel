<?php

namespace App\Domains\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelMessageRequest extends FormRequest
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
            'message' => [
                'bail',
                'required',
                'string',
                'max:100'
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'message' => __('Message')
        ];
    }
}
