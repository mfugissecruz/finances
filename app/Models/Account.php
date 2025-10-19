<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property float $balance
 * @property float $current_balance
 * @property boolean $is_active
 */
class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'balance',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    #[Scope]
    public function withBalance($query)
    {
        return $query->with(['transactions.category']);
    }

    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn (int $value): int|float => $value / 100,
            set: fn (float $value): int => (int) ($value * 100),
        );
    }

    protected function currentBalance(): Attribute
    {
        return Attribute::make(
            get: fn (): int|float => ($this->attributes['balance'] / 100) + $this->transactions->sum(fn($transaction) => $transaction->category->type === 'income'
                ? $transaction->amount
                : -$transaction->amount),
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value, array $attributes) => $value ?? Str::slug($attributes['title']),
        );
    }
}
