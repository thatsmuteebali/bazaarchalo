<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    private const MIN_IMAGES = 2;
    private const MAX_IMAGES = 8;

    public function authorize(): bool
    {
        $product = $this->route('product');

        // On update the product must belong to the logged-in seller
        return ! $product instanceof Product
            || (int) $product->seller_id === (int) auth()->id();
    }

    private function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH');
    }

    public function rules(): array
    {
        $isUpdate = $this->isUpdate();
        $product  = $this->route('product');
        $sellerId = auth()->id();

        // has_variants can only be chosen when creating; afterwards it is fixed
        $hasVariants = $isUpdate
            ? (bool) $product->has_variants
            : $this->boolean('has_variants');

        $rules = [
            'name'              => ['required', 'string', 'max:255'],
            'category_id'       => ['required', Rule::exists('categories', 'id')->where('status', 'active')],
            'shop_id'           => ['required', Rule::exists('shops', 'id')->where('seller_id', $sellerId)->where('status', 'active')],
            'collection_id'     => ['nullable', Rule::exists('collections', 'id')->where('seller_id', $sellerId)->where('status', 'active')],
            'short_description' => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'compare_price'     => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'gt:price'],
            'status'            => ['required', Rule::in(['active', 'draft', 'inactive'])],
            'is_featured'       => ['boolean'],

            'images'            => ['nullable', 'array', 'max:' . self::MAX_IMAGES],
            'images.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_images'     => ['nullable', 'array'],
            'remove_images.*'   => ['integer'],
        ];

        if (! $isUpdate) {
            $rules['has_variants'] = ['boolean'];
        }

        if (! $hasVariants) {
            // Stock is typed only when creating. On edit it is changed through the Inventory section.
            if (! $isUpdate) {
                $rules['stock'] = ['required', 'integer', 'min:0', 'max:1000000'];
            }

            return $rules;
        }

        $rules += [
            'options'                    => ['required', 'array', 'min:1'],
            'options.*.name'             => ['required', 'string', 'max:50', 'distinct:ignore_case'],
            'options.*.values'           => ['required', 'array', 'min:1'],
            'options.*.values.*'         => ['required', 'string', 'max:50'],

            'variants'                   => ['required', 'array', 'min:1'],
            'variants.*.title'           => ['required', 'string', 'max:255', 'distinct'],
            'variants.*.option_values'   => ['required', 'array', 'min:1'],
            'variants.*.option_values.*' => ['required', 'string', 'max:50'],
            'variants.*.price'           => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.compare_price'   => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            // existing variants do not send stock; new variants send their starting stock
            'variants.*.stock'           => [$isUpdate ? 'nullable' : 'required', 'integer', 'min:0', 'max:1000000'],
        ];

        // SKU must be unique, but a variant may keep its own SKU when editing
        $existing = $isUpdate ? $product->variants()->pluck('id', 'title') : collect();

        foreach (array_keys((array) $this->input('variants', [])) as $i) {
            $unique = Rule::unique('product_variants', 'sku');
            $title  = $this->input("variants.$i.title");

            if (is_string($title) && $existing->has($title)) {
                $unique->ignore($existing[$title]);
            }

            $rules["variants.$i.sku"] = ['nullable', 'string', 'max:100', 'distinct', $unique];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $product  = $this->route('product');
            $existing = $product instanceof Product ? $product->images()->count() : 0;
            $removing = $product instanceof Product
                ? $product->images()->whereIn('id', Arr::wrap($this->input('remove_images', [])))->count()
                : 0;
            $new   = count(Arr::wrap($this->file('images')));
            $total = $existing - $removing + $new;

            if ($total < self::MIN_IMAGES) {
                $v->errors()->add('images', 'A product needs at least ' . self::MIN_IMAGES . ' images.');
            } elseif ($total > self::MAX_IMAGES) {
                $v->errors()->add('images', 'A product can have at most ' . self::MAX_IMAGES . ' images.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'category_id'              => 'category',
            'shop_id'                  => 'shop',
            'collection_id'            => 'collection',
            'images.*'                 => 'image',
            'options.*.name'           => 'option name',
            'options.*.values'         => 'option values',
            'options.*.values.*'       => 'option value',
            'variants.*.title'         => 'variant',
            'variants.*.sku'           => 'SKU',
            'variants.*.price'         => 'variant price',
            'variants.*.compare_price' => 'variant compare price',
            'variants.*.stock'         => 'variant stock',
        ];
    }
}
