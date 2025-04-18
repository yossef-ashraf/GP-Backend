<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order_note extends Model
{
    use SoftDeletes;

    protected $table = 'order_notes';

    protected $fillable = [
        'order_id',
        'notes'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
