<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LegionHQ\LaravelPayrex\Enums\PaymentMethod;

class StorePaymentIntentRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'capture_type' => ['nullable', 'string', Rule::in(['automatic', 'manual'])],
            'user_id' => ['nullable', 'exists:users,id'],
            'statement_descriptor' => ['nullable', 'string', 'max:22'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
