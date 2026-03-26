<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use LegionHQ\LaravelPayrex\Enums\PaymentMethod;

class StoreCheckoutSessionRequest extends FormRequest
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
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.product_id' => ['required', 'integer'],
            'line_items.*.name' => ['required', 'string'],
            'line_items.*.amount' => ['required', 'numeric', 'min:1'],
            'line_items.*.quantity' => ['required', 'integer', 'min:1'],
            'line_items.*.description' => ['nullable', 'string'],
            'line_items.*.image' => ['nullable', 'url'],
            'description' => ['nullable', 'string', 'max:255'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'submit_type' => ['nullable', 'string', Rule::in(['pay', 'donate'])],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                /** @var list<array{product_id: int}> $lineItems */
                $lineItems = $this->input('line_items', []);

                $productIds = collect($lineItems)
                    ->pluck('product_id')
                    ->filter()
                    ->unique()
                    ->values();

                if ($productIds->isEmpty()) {
                    return;
                }

                $existingCount = Product::query()
                    ->whereIn('id', $productIds)
                    ->count();

                if ($existingCount !== $productIds->count()) {
                    $validator->errors()->add('line_items', 'One or more selected products do not exist.');
                }
            },
        ];
    }
}
