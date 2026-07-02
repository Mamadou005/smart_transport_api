<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'lue',
    ];

    protected $casts = [
        'lue'        => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relation : appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scope : non lues
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    // ── Scope : par utilisateur
    public function scopePourUtilisateur($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
