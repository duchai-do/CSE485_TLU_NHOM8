<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'room_number', 'type', 'capacity', 'price', 'status'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function beds(): HasMany            
    {
        return $this->hasMany(Bed::class);
    }
}

