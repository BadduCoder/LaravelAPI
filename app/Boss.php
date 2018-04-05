<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boss extends Model
{
    public function worker()
    {
        return $this->hasOne('App\Workers','boss_id','id');
    }
}
