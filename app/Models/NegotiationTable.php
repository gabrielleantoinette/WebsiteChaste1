<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegotiationTable extends Model
{
    protected $table = 'negotiation_tables';

    // allow mass‐assignment on these columns:
    protected $fillable = [
        'user_id','product_id','status','final_price',
        'cust_nego_1','seller_nego_1',
        'cust_nego_2','seller_nego_2',
        'cust_nego_3','seller_nego_3',
      ];
}
