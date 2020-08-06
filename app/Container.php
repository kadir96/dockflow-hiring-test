<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Container extends Model
{
    protected $guarded = [];

    public function tradeflow(): BelongsToMany
    {
        return $this->belongsToMany(Tradeflow::class, 'tradeflow_container');
    }
}
