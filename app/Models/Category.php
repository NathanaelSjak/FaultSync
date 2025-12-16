<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',        // income | expense
        'description',
        'color',
        'icon',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $attributes = [
        'status' => true,
        'color' => '#6c757d',
        'icon' => 'fas fa-folder',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes (dipakai di controller)
    |--------------------------------------------------------------------------
    */

    public function scopeByUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'LIKE', "%{$keyword}%");
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (untuk UI)
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute()
    {
        return $this->type === 'income'
            ? 'Pemasukan'
            : 'Pengeluaran';
    }
}