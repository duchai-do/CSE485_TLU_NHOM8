<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocation_id',
        'contract_code',
        'start_date',
        'end_date',
        'monthly_price',
        'deposit_amount',
        'status',
        'signed_at',
        'terminated_at',
        'termination_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'signed_at' => 'datetime',
            'terminated_at' => 'datetime',
        ];
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(Allocation::class);
    }

    public function violationRecords(): HasMany
    {
        return $this->hasMany(ViolationRecord::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
