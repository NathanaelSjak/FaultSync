<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',
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

    protected $appends = [
        'type_label',
        'badge_color',
        'is_active',
    ];

    protected $attributes = [
        'status' => true,
        'color' => '#6c757d',
        'icon' => 'fas fa-folder',
    ];

    protected static function boot()
    {
        parent::boot();

        // Set default color and icon based on type
        static::creating(function ($category) {
            if (empty($category->color)) {
                $category->color = match($category->type) {
                    'income' => '#00ff00',
                    'expense' => '#ff0000',
                    'transfer' => '#0000ff',
                    default => '#6c757d',
                };
            }

            if (empty($category->icon)) {
                $category->icon = match($category->type) {
                    'income' => 'fas fa-money-bill-wave',
                    'expense' => 'fas fa-shopping-cart',
                    'transfer' => 'fas fa-exchange-alt',
                    default => 'fas fa-folder',
                };
            }
        });

        // Generate slug from name if not provided
        static::saving(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeByUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'income' => 'Pendapatan',
                'expense' => 'Pengeluaran',
                'transfer' => 'Transfer',
                default => ucfirst($this->type),
            }
        );
    }

    protected function badgeColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->type) {
                'income' => 'success',
                'expense' => 'danger',
                'transfer' => 'info',
                default => 'secondary',
            }
        );
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool) $this->status
        );
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d M Y H:i');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at->format('d M Y H:i');
    }

    public function hasTransactions(): bool
    {
        return $this->transactions()->exists();
    }

    public function getTotalAmountAttribute()
    {
        if (!$this->relationLoaded('transactions')) {
            $this->load('transactions');
        }

        return $this->transactions->sum('amount');
    }

    public function getMonthlyTotal($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return $this->transactions()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');
    }
}