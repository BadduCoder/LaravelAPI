<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workers extends Model
{
    public function boss()
    {
      return $this->belongsTo('App\Boss','boss_id','id');
    }
}
