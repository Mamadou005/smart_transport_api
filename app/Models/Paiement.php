<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model {
    protected $fillable = [
        'reservation_id', 'user_id', 'montant', 'devise',
        'methode', 'statut', 'reference',
        'telephone_paiement', 'confirme_at',
    ];

    protected $casts = ['confirme_at' => 'datetime'];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
