<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceResponse;

class SearchTerm extends ResourceResponse
{
    use SoftDeletes;
}
