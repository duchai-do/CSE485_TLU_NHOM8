<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Room extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['monthly_price' => 'decimal:2']; }
    public function building(): BelongsTo { return $this->belongsTo(Building::class); }
    public function beds(): HasMany { return $this->hasMany(Bed::class); }
    public function utilityReadings(): HasMany { return $this->hasMany(UtilityReading::class); }
}
