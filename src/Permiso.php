<?php

namespace Betalectic\Permiso;

use Exception;
use DB;

use Betalectic\Permiso\Models\Permission;
use Betalectic\Permiso\Models\Entity;
use Betalectic\Permiso\Models\Group;
use Betalectic\Permiso\Models\UserPermission;

class Permiso
{
    public function cleanUp()
    {
        DB::table('permiso_entities')->truncate();
        DB::table('permiso_permissions')->truncate();
        DB::table('permiso_groups')->truncate();
        DB::table('permiso_groups_permissions')->truncate();
        DB::table('permiso_groups_entities')->truncate();
        DB::table('permiso_user_permissions')->truncate();
        DB::table('permiso_entity_parents')->truncate();
    }

    public function getPermissions($entities = [])
    {
        if(count($entities)){
            $permissions = Permission::whereIn('entity_type',$entities)->get();
        }else{
            $permissions = Permission::get();
        }

        return $permissions;
    }

    public function registerGroup($groupName, $permissions = [], $displayName = "")
    {
        $group = Group::firstOrCreate(['name' => $groupName]);
        $group->display_name = $displayName != "" ? $displayName : $groupName;
        $group->save();

        if(count($permissions))
        {
            $permissionIds = Permission::whereIn('value',$permissions)->pluck('id')->toArray();
            $group->permissions()->sync($permissionIds);
        }

        return $group;
    }

    public function setParent($child, $parent)
    {
        $parentEntity = Entity::firstOrCreate([
            'type' => get_class($parent),
            'value' => $parent->getKey(),
        ]);

        $childEntity = Entity::firstOrCreate([
            'type' => get_class($child),
            'value' => $child->getKey(),
        ]);

        $parentEntity->children()->save($childEntity);
    }

    public function deregisterEntity($entity)
    {
        $entity = Entity::where([
            'type' => get_class($entity),
            'value' => $entity->getKey(),
        ])->first();

        $entity->children()->update(['pid' => NULL]);

        $entity->delete();
    }


    public function registerEntity($entity)
    {

        if($entity instanceof \Illuminate\Database\Eloquent\Collection || is_array($entity)) {

            foreach($entity as $item)
            {
                Entity::firstOrCreate([
                    'type' => get_class($item),
                    'value' => $item->getKey()
                ]);
            }

        }else {

            Entity::firstOrCreate([
                'type' => get_class($entity),
                'value' => $entity->getKey()
            ]);
        }

    }

    public function denyOnGroupAndEntity($user, $group, $entity)
    {
        $denier = new PermissionDenier($user);
        $denier->group($group);
        $denier->entity($entity);
        $denier->commit();
    }

    public function denyOnGroup($user, $group)
    {
        $denier = new PermissionDenier($user);
        $denier->group($group);
        $denier->commit();
    }

    public function grantOnGroupAndEntity($user, $group, $entity, $uniqueness = false)
    {
        $grantor = new PermissionGrantor($user);
        $grantor->group($group);
        $grantor->entity($entity);
        $grantor->setUniqueness($uniqueness);
        $grantor->commit();
    }

    public function grantOnGroup($user, $group)
    {
        $grantor = new PermissionGrantor($user);
        $grantor->group($group);
        $grantor->commit();
    }

    public function grantOnEntity($user, $entity)
    {
        $grantor = new PermissionGrantor($user);
        $grantor->entity($entity);
        $grantor->commit();
    }

    public function denyPermissionOnEntity($user, $permission, $entity)
    {
        $grantor = new PermissionDenier($user);
        $grantor->permission($permission);
        $grantor->entity($entity);
        $grantor->commit();
    }

    public function grantPermissionOnEntity($user, $permission, $entity, $children = NULL)
    {
        $grantor = new PermissionGrantor($user);
        $grantor->permission($permission);
        $grantor->entity($entity);

        if(!is_null($children))
        {
            $grantor->children($children);
        }

        $grantor->commit();
    }


    public function denyPermission($user, $permission)
    {
        $grantor = new PermissionDenier($user);
        $grantor->permission($permission);
        $grantor->commit();
    }

    public function grantPermission($user, $permission)
    {
        $grantor = new PermissionGrantor($user);
        $grantor->permission($permission);
        $grantor->commit();
    }

    public function deregisterPermission($permission)
    {
        $permission = Permission::firstOrCreate([
            'value' => $permission
        ]);

        $permission->userPermissions()->delete();
        $permission->groups()->delete();

        $permission->delete();
    }

    public function registerPermission($permission, $entity)
    {
        $permission = Permission::firstOrCreate([
            'value' => $permission
        ]);

        if(is_null($permission->entity_type)){
            $permission->entity_type = $entity;
            $permission->save();
        }
        else{
            if($permission->entity_type != $entity)
            {
                throw new Exception("This permission is already registered with {$permission->entity_type}", 1);
            }
        }

        return $permission;
    }

    public function rajiv()
    {
        dd("rajiv");
    }

    public function build($userId)
    {
        $builder = new Build($userId);
        return $builder->make();
    }

    public function addGlobalPermission($permission)
    {
        $permission = Permission::firstOrCreate([
            'value' => $permission
        ]);

        return $permission;
    }

    public function addEntityPermission($permission,$entity)
    {
        $value = is_null($entity) ? $permission : $entity.'_'.$permission;

        $permission = Permission::firstOrCreate([
            'value' => $entity.'_'.$permission,
            'entity' => $entity
        ]);

        return $permission;
    }

    public function manageGroup($name, $permissions, $entity = NULL)
    {
        $group = Group::firstOrCreate(['name' => $name]);
        $group->associate($permissions);
    }

    public function deleteGroup($name)
    {
        $group = Group::firstOrCreate(['name' => $name]);
        $group->permissions()->detach();
        $group->users()->detach();
        $group->delete();
    }

    public function mapEntityToGroup($groupName, $entity, $entityId)
    {
        $group = Group::where(['name' => $name])->first();
        // Assume that the group has related group permissions

        GroupEntity::firstOrCreate([
            'group_id' => $group->id,
            'entity' => $entity,
            'entity_id' => $entityId
        ]);
    }

    public function soDoPermit($user, $permission, $entity = NULL, $entityId = NULL)
    {
        $permission = $this->getPermissionObject($permission,$entity,$entityId);

        UserPermission::firstOrCreate([
            'permission' => $permission->value,
            'entity' => $entity,
            'entity_id' => $entityId
        ]);
    }

    public function soDontPermit($user, $permission, $entity = NULL, $entityId = NULL)
    {
        $userPermission = $this->getUserPermissionObject($user, $permission, $entity, $entityId);

        $userPermission->delete();
    }

    public function soCan($user, $permission, $entity = NULL, $entityId = NULL)
    {
        $userPermission = $this->getUserPermissionObject($user, $permission, $entity, $entityId);

        return is_null($userPermission) ? false : true;
    }

    public function getUserPermissionObject(
        $user,
        $permission,
        $entity = NULL,
        $entityId = NULL
    )
    {
        $permission = $this->getPermissionObject($permission,$entity,$entityId);

        $userPermission = UserPermission::where([
            'user_id' => $userId,
            'permission' => $permission->value,
            'entity' => $entity,
            'entity_id' => $entityId
        ])->first();

        return $userPermission;

    }

    public function getPermissionObject($permission, $entity = NULL, $entityId = NULL)
    {
        $value = is_null($entity) ? $permission : $entity.'_'.$permission;

        $permission = Permission::where([
            'value' => $value,
            'entity' => $entity,
            'entity_id' => $entityId,
        ])->first();

        return $permission;
    }

    public function soAddGroup($userId, $group)
    {
        $group = Group::where(['name' => $name])->first();
        $group->users()->attach($userId);
    }
}
