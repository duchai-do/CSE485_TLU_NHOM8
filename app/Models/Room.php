<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'room_number', 'type', 'capacity', 'price', 'status'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}