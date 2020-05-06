<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'category_created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'category_updated_at';

    protected $primaryKey = 'category_id';

    protected $fillable = ['category_name'];
    
    // Tabla que usa este modelo
    protected $table = 'categories';

    // Relaciones

}
