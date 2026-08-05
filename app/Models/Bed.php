<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Bed extends Model {
    protected $guarded = [];
    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function allocations(): HasMany { return $this->hasMany(Allocation::class); }
}
