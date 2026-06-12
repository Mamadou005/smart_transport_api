<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model {
    protected $fillable = [
        'user_id', 'voyage_id', 'code_qr', 'statut'
    ];

    public function voyage() {
        return $this->belongsTo(Voyage::class);
    }

    public function bagages() {
        return $this->hasMany(Bagage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paiement() {
        return $this->hasOne(Paiement::class);
    }
}
