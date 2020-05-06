<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model {

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'card_created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'card_updated_at';

    protected $primaryKey = 'card_id';
    
    // Tabla que usa este modelo
    protected $table = 'cards';

    // Relaciones
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    
}
