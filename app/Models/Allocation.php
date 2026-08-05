<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasOne};
class Allocation extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date']; }
    public function registration(): BelongsTo { return $this->belongsTo(RoomRegistration::class, 'registration_id'); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function bed(): BelongsTo { return $this->belongsTo(Bed::class); }
    public function allocator(): BelongsTo { return $this->belongsTo(User::class, 'allocated_by'); }
    public function contract(): HasOne { return $this->hasOne(Contract::class); }
}
