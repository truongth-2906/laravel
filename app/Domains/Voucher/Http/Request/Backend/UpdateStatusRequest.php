<?php

namespace App\Domains\Voucher\Http\Request\Backend;

use App\Domains\Auth\Models\User;
use App\Domains\Voucher\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
            'status' => ['bail', 'required', Rule::in(Voucher::getStatuses())]
        ];
    }
}
