<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localisation extends Model {
    protected $fillable = [
        'bagage_id', 'latitude',
        'longitude', 'horodatage'
    ];

    public function bagage() {
        return $this->belongsTo(Bagage::class);
    }
}
