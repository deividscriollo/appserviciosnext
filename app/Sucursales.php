<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    protected $connection='pgsql';
    protected $table='sucursales';
    protected $fillable=array('id_sucursal','codigo','nombre_sucursal','direccion','estado','id_empresa');
}
