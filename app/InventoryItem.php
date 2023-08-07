<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_id',
        'warehouse',
        'quantity',
        'length',
        'width',
        'heat_no',
        'lot_no',
        'sc_no',
        'supplier',
        'supplier_heat_no',
        'material_used',
        'is_excess',
        'weight_received',
        'create_user',
        'update_user'
    ];
}
