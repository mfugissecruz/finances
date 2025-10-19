<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $account_id
 * @property int $category_id
 * @property int $amount
 * @property string $description
 * @property string $transaction_date
 */
class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'amount',
        'description',
        'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'integer',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (int $value): int|float => $value / 100,
            set: fn (float $value): int => (int) ($value * 100),
        );
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category->type
        );
    }
}
