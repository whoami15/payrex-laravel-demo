<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LegionHQ\LaravelPayrex\Enums\RefundReason;

class StoreRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['required', 'string', Rule::in(array_column(RefundReason::cases(), 'value'))],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
