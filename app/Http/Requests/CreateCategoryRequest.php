<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anda bisa ganti dengan auth check jika perlu
        // Contoh: return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // Validasi unik berdasarkan user_id dan type
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                                 ->where('type', $this->type);
                })
            ],
            'type' => [
                'required',
                'string',
                'in:income,expense,transfer' // tambahkan 'transfer' jika perlu
            ],
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'status' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 50 karakter.',
            'name.unique' => 'Kategori dengan nama ini sudah ada untuk tipe ini.',
            'type.required' => 'Tipe kategori wajib dipilih.',
            'type.in' => 'Tipe kategori harus income atau expense.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'color.max' => 'Warna maksimal 20 karakter.',
            'icon.max' => 'Icon maksimal 50 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'type' => 'tipe kategori',
            'description' => 'deskripsi',
            'color' => 'warna',
            'icon' => 'icon',
            'status' => 'status',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Trim whitespace dari input
        $this->merge([
            'name' => trim($this->name),
            'description' => $this->description ? trim($this->description) : null,
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        
        // Tambahkan user_id ke data yang divalidasi
        $validated['user_id'] = auth()->id();
        
        // Set default values jika tidak diisi
        $validated['color'] = $validated['color'] ?? $this->getDefaultColor();
        $validated['icon'] = $validated['icon'] ?? $this->getDefaultIcon();
        $validated['status'] = $validated['status'] ?? true;
        
        return $validated;
    }

    private function getDefaultColor(): string
    {
        return match($this->type) {
            'income' => '#00ff00', // Green
            'expense' => '#ff0000', // Red
            'transfer' => '#0000ff', // Blue
            default => '#6c757d', // Gray
        };
    }

    private function getDefaultIcon(): string
    {
        return match($this->type) {
            'income' => 'fas fa-money-bill-wave',
            'expense' => 'fas fa-shopping-cart',
            'transfer' => 'fas fa-exchange-alt',
            default => 'fas fa-folder',
        };
    }
}