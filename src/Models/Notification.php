<?php

namespace NadzorServera\Skijasi\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = $prefix.'notifications';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'receiver_user_id',
        'type',
        'title',
        'content',
        'is_read',
        'sender_user_id',
    ];

    public function sender_users()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiver_users()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }
}
