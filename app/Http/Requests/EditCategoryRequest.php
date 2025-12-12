<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');
        
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // Validasi unik berdasarkan user_id, kecuali kategori ini
                Rule::unique('categories')->where(function ($query) use ($categoryId) {
                    return $query->where('user_id', auth()->id())
                                 ->where('id', '!=', $categoryId);
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 50 karakter.',
            'name.unique' => 'Kategori dengan nama ini sudah ada.',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
        ]);
    }
}