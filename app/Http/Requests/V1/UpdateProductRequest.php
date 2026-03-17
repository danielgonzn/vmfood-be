<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'title' => ['sometimes', 'required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products', 'slug')->ignore($productId)],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'origin' => ['nullable', 'string', 'max:120'],
            'condition' => ['sometimes', 'required', 'in:Nueva,Usada'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['string', 'max:2048'],
            'capacity' => ['nullable', 'string', 'max:120'],
            'voltage' => ['nullable', 'string', 'max:120'],
            'power' => ['nullable', 'string', 'max:120'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'available' => ['boolean'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
