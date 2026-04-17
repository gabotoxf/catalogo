<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $primaryKey = 'id_banner';
    protected $fillable = ['titulo_banner', 'descripcion_banner', 'imagen_banner'];

    public function getImagenBannerAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return url('assets/img/Banners/' . basename($value));
    }
}
