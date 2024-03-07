<?php

namespace App\Domains\Escrow\Rules;

use Illuminate\Validation\Rule;

/**
 * Class CreateTransaction.
 */
class CreateTransaction
{
    /**
     * Get the validation rules that apply to the request.
     * @param int $freelancerId
     * @param int $employerId
     * @return array
     */
    static public function rules(int $freelancerId, int $employerId)
    {
        return [
            'freelancer_email' => [
                'bail',
                'required',
                'string',
                Rule::exists('users', 'escrow_email')->where('id', $freelancerId),
            ],
            'employer_email' => [
                'bail',
                'required',
                'string',
                Rule::exists('users', 'escrow_email')->where('id', $employerId),
            ],
            'currency' => [
                'bail',
                'nullable',
                'string',
                Rule::in(config('escrow.currencies_supported')),
            ],
            'job_id' => [
                'bail',
                'required',
                Rule::exists('jobs', 'id'),
            ],
            'job_name' => [
                'bail',
                'required',
                'string',
            ],
            'freelancer_name' => [
                'bail',
                'required',
                'string',
            ],
            'amount' => [
                'bail',
                'required',
                'numeric',
                'min:1'
            ]
        ];
    }
}
