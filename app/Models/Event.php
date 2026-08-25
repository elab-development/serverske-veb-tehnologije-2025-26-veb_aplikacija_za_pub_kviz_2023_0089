<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['season_id', 'name', 'event_date', 'location', 'winner_team_id'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
        public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }
}