<?php

namespace Betalectic\Permiso;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $guarded = [];
    public $table = 'permiso_permissions';
}
