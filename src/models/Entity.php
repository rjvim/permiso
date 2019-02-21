<?php namespace Betalectic\Permiso\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model {

	protected $table = "permiso_entities";

    public $guarded = [];

    public function children()
    {
        return $this->hasMany(Entity::class,'pid','id');
    }

}
