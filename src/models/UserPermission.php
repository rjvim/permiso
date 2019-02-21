<?php namespace Betalectic\Permiso\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model {

    protected $table = "permiso_user_permissions";

    public $guarded = [];

    public function of()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class,'entity_id');
    }

}
