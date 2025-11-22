<?php

namespace BaWe\ProsCons\Models;

use Illuminate\Database\Eloquent\Model;

class ProsCon extends Model
{
    protected $table = 'product_pros_cons';

    protected $fillable = [
        'product_id',
        'type',
        'text',
        'position',
    ];

    public $timestamps = true;
}
