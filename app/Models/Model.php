<?php

namespace App\Models;

use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use IsCacheable;
}