<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBillingStatementRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.product_id' => ['required', 'integer'],
            'line_items.*.description' => ['required', 'string'],
            'line_items.*.unit_price' => ['required', 'numeric', 'min:1'],
            'line_items.*.quantity' => ['required', 'integer', 'min:1'],
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
