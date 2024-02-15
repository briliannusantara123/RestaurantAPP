<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    protected $table = 'order_details';
    protected $fillable = [
        'order_id_order',
        'id_masakan',
        'qty',
        'subtotal',
        'keterangan',
        'status'
    ];

}
