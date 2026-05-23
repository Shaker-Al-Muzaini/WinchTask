<?php

namespace Src\Domain\Orders\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderArchive extends Model
{
    protected $table = 'orders_archive';

    protected $guarded = [];

    public $timestamps = false;
}
