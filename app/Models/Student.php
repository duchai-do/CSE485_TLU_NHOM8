<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Student extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['date_of_birth' => 'date']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function registrations(): HasMany { return $this->hasMany(RoomRegistration::class); }
    public function allocations(): HasMany { return $this->hasMany(Allocation::class); }
    public function violationRecords(): HasMany { return $this->hasMany(ViolationRecord::class); }
}
