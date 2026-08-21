<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}