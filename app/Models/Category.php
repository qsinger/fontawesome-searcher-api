<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public function icons(){
        return $this->belongsToMany('App\Models\Icon')->orderBy('en_label');
    }
}
