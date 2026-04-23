<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['queue_id','action','timestamp'];

    protected function casts(): array
    {
        return ['timestamp' => 'datetime'];
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}