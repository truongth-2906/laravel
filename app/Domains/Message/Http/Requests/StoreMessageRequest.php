<?php

namespace App\Domains\Message\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreMessageRequest.
 */
class StoreMessageRequest extends FormRequest
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
            'message' => ['nullable', 'max:10000'],
            'file' => ['file', 'max:' . config('upload.file_chat_upload')]
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'message.max' => __('The content message must not be greater than 10000 characters.'),
            'file' => __('File upload must not greater than 5MB.'),
        ];
    }
}
