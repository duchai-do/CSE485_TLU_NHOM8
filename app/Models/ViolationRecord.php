<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ViolationRecord extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['violation_date' => 'date', 'resolved_at' => 'datetime', 'penalty_amount' => 'decimal:2']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
