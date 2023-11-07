<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageView extends Model
{
    use HasFactory;

    protected $table = 'message_views';

    protected $fillable = [
        'message_id',
        'member_id',
        'is_seen',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

   public function member()
   {
       return $this->belongsTo(Member::class);
   }
}
