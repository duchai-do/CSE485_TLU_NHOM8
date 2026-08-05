<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Contract extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date', 'signed_at' => 'datetime', 'terminated_at' => 'datetime', 'monthly_price' => 'decimal:2', 'deposit_amount' => 'decimal:2']; }
    public function allocation(): BelongsTo { return $this->belongsTo(Allocation::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function violationRecords(): HasMany { return $this->hasMany(ViolationRecord::class); }
}
