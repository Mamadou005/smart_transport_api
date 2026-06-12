<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTransport extends Model {
    protected $table    = 'notifications_transport';
    protected $fillable = [
        'user_id', 'message', 'type', 'lue'
    ];

    protected $casts = ['lue' => 'boolean'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
