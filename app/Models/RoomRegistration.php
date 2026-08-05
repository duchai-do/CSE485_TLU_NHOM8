<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasOne};
class RoomRegistration extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['reviewed_at' => 'datetime']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function allocation(): HasOne { return $this->hasOne(Allocation::class, 'registration_id'); }
}
