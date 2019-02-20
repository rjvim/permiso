<?php

namespace Betalectic\Permiso;

class Permiso
{

    public function soBuild($user)
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
