<?php

namespace Betalectic\Permiso;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Group extends Model
{
    public $guarded = [];
    public $table = 'permiso_groups';

    public function users()
    {
        return $this->belongsToMany(User::class,'permiso_groups_users');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'permiso_groups_permissions');
    }

    public function associate($permissions)
    {
        foreach($permissions as $permission)
        {
            $collect = [];

            $permission = Permission::where('value',$permission)->first();

            $collect[] = $permission->id;
        }

        $this->permissions()->sync($collect);
    }

}
