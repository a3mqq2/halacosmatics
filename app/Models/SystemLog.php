<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SystemLog extends Model
{
    protected $fillable = ['description'];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
