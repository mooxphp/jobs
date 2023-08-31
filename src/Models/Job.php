<?php

namespace Adrolli\FilamentJobManager\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    /*
     *--------------------------------------------------------------------------
     * Mutators
     *--------------------------------------------------------------------------
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: function () {

                return 'waiting';
            },
        );
    }

    /*
     *--------------------------------------------------------------------------
     * Methods
     *--------------------------------------------------------------------------
     */

    public function getDisplayNameAttribute()
    {
        $payload = json_decode($this->attributes['payload'], true);

        return $payload['displayName'] ?? null;
    }
}
