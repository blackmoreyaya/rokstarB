<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'address_created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'address_updated_at';

    protected $primaryKey = 'address_id';
    
    // Tabla que usa este modelo
    protected $table = 'addresses';

    // Relaciones
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

}
