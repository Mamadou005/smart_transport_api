<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bagage extends Model {
    protected $fillable = [
        'reservation_id', 'description',
        'poids', 'code_qr', 'statut'
    ];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }

    public function localisations() {
        return $this->hasMany(Localisation::class);
    }

    public function derniereLocalisation() {
        return $this->hasOne(Localisation::class)
            ->latestOfMany();
    }
}
