<?php

namespace App\Domains\Voucher\Http\Request\Backend;

use App\Domains\Auth\Models\User;
use App\Domains\Voucher\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
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
            'name' => ['bail', 'required', 'string', 'max:255'],
            'description' => ['bail', 'nullable', 'string', 'max:' . Voucher::MAX_DESCRIPTION],
            'discount_type' => ['bail', 'required', Rule::in(Voucher::getTypes())],
            'discount' => [
                'bail',
                'required',
                'numeric',
                'min:0',
                Rule::when($this->discount_type === Voucher::TYPE_PERCENTAGE, 'max:100', 'max:999999999999999,99')
            ],
            'max_discount' => [
                'bail',
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999999,99'
            ],
            'count' => ['bail', 'nullable', 'integer', Rule::when(!is_null($this->count), 'min:0')],
            'expired_date' => ['bail', 'nullable', Rule::when(!is_null($this->expired_date), ['date_format:d-m-Y', 'after:today'])],
            'users_specify' => [
                'bail',
                Rule::when($this->boolean('scope'), [
                    'required',
                    'array',
                    'min:1'
                ], 'nullable'),
                Rule::when($this->boolean('scope') && !is_null($this->count), 'max:' . $this->count)
            ],
            'users_specify.*' => [
                'bail',
                Rule::when(
                    $this->boolean('scope'),
                    [
                        'required',
                        Rule::exists('users', 'id')->where('is_hidden', false)->whereNotNull('email_verified_at'),
                    ],
                    'nullable'
                )
                ],
                'number_times_used_value' => [
                    'bail',
                    'required',
                    'integer',
                    'min:1',
                    'max:4294967295'
                ]
        ];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('voucher name'),
            'count' => __('number of vouchers'),
            'users_specify' => __('users'),
            'users_specify.*' => __('user'),
            'number_times_used_value' => __('Number of times used value')
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        $messages = [
            'max_discount.max' => __('The :attribute entered is over the allowed limit.'),
            'number_times_used_value.max' => __('The :attribute entered is over the allowed limit.'),
        ];

        if ($this->discount_type === Voucher::TYPE_NUMERIC) {
            $messages['discount.max'] = __('The :attribute entered is over the allowed limit.');
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->boolean('scope') && count($this->users_specify ?? [])) {
            $usersSelected = User::query()->select('id', 'name AS text')
                ->whereIn('id', $this->users_specify)
                ->whereIn('type', [User::TYPE_EMPLOYER, User::TYPE_FREELANCER])
                ->where('is_hidden', false)
                ->whereNotNull('email_verified_at')
                ->pluck('text', 'id');

            session()->flash('users_selected', $usersSelected);
        }
    }
}
