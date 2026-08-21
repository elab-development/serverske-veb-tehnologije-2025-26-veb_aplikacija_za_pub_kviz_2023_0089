<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['season_id', 'name', 'event_date'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}