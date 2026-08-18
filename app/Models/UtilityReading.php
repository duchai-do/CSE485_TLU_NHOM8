<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UtilityReading extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['recorded_at' => 'datetime']; }
    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
    public function getElectricityUsedAttribute(): float { return (float) $this->current_electricity - (float) $this->previous_electricity; }
    public function getWaterUsedAttribute(): float { return (float) $this->current_water - (float) $this->previous_water; }
    public function getElectricityAmountAttribute(): float { return round($this->electricity_used * (float) $this->electricity_unit_price, 2); }
    public function getWaterAmountAttribute(): float { return round($this->water_used * (float) $this->water_unit_price, 2); }
}
