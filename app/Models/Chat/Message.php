<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'room_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function views()
    {
        return $this->hasMany(MessageView::class);
    }

    public function unseenCount()
    {
        return $this->views()->where(['is_seen' => false])->count();
    }
}
