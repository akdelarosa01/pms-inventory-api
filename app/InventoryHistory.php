<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $fillable = [
        'inv_id',
        'transaction_type',
        'remarks',
        'create_user'
    ];
}
