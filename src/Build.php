<?php

namespace Betalectic\Permiso;

class Build
{
    public $permissions;
    public $userId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->permissions = [];
    }

    public function make()
    {

        $globalPermissions = Permission::whereNull('entity')->get();

        foreach($globalPermissions as $permission)
        {
            $this->permissions[$permission] = false;
        }

        $entityPermissions = Permission::whereNotNull('entity')->get();

        foreach($entityPermissions as $permission)
        {
            $this->permissions[$permission->value] = [];
        }

        $userPermissions = UserPermission::where('user_id',$this->user->id)->get();

        foreach($userPermissions as $userPermission)
        {
            $permission = $userPermission->permission;

            if(is_null($permission->entity)){
                $this->permissions[$permission->value] = true;
            }
            else{
                if(is_null($permission->entity_id)){
                    $this->permissions[$permission->value] = true;
                }else{
                    array_push($this->permissions[$permission->value], $permission->entity_id);
                }
            }
        }

        // Fetch users groups
        $userGroups = $user->groups;

        foreach($userGroups as $userGroup)
        {
            foreach($userGroup->permissions as $permission)
            {
                if(is_null($permission->entity)){
                    $this->permissions[$permission->value] = true;
                }
                else{
                    $entities = $userGroup->entities;

                    foreach($entities as $entity)
                    {
                        // If there is no entity, does user get to approve all entities?
                        if($entity->entity == $permission->entity)
                        {
                            array_push($this->permissions[$permission->value], $entity->entity_id);
                        }
                    }
                }
            }
        }

        return $this->permissions;
    }

}
