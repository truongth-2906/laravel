<?php

namespace App\Domains\Review\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreReviewRequest.
 */
class StoreReviewRequest extends FormRequest
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
            'star' => [Rule::in(POINT_REVIEW_ARRAY)],
            'description' => [
                'max:500'
            ]
        ];
    }

    public function messages()
    {
        return [
            'description.max' => __(
                'The description must not be greater than 500 characters.'
            )
        ];
    }
}
