<?php

namespace App\Domains\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddEscrowAccountRequest extends FormRequest
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
            'email' => [
                'bail',
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'escrow_email')->where(function ($e) {
                    $e->where('id', '!=', $this->user()->id)->whereNull('deleted_at');
                })
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'email' => __('Escrow email')
        ];
    }
}
