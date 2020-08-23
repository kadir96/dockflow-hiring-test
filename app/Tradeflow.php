<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tradeflow extends Model
{
    protected $guarded = [];

    public function containers(): BelongsToMany
    {
        return $this->belongsToMany(Container::class, 'tradeflow_container');
    }
}
