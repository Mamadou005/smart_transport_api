<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voyage extends Model {
    protected $fillable = [
        'origine', 'destination', 'date_depart', 'date_arrivee',
        'type_transport', 'statut', 'capacite', 'prix', 'devise',
    ];

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    public function getCapaciteDisponibleAttribute() {
        return $this->capacite - $this->reservations()->count();
    }
}
