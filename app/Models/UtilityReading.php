<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UtilityReading extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['recorded_at' => 'datetime']; }
    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
