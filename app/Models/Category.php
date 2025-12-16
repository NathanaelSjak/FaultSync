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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
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
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (for UI)
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            default => ucfirst($this->type),
        };
    }

    public function getIsActiveAttribute()
    {
        return (bool) $this->status;
    }
}