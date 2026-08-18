<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'contract_id',
        'recorded_by',
        'violation_date',
        'violation_type',
        'description',
        'penalty_amount',
        'status',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
            'penalty_amount' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
