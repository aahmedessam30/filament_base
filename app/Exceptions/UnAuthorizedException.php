<?php

namespace App\Exceptions;

use App\Http\Resources\Api\Shared\ErrorResource;
use Exception;

class UnAuthorizedException extends Exception
{
    public static function forRoles(array $roles): ErrorResource
    {
        $message = __('messages.user_does_not_have_the_right_roles');

        if (config('permission.display_role_in_exception')) {
            $message .= __('messages.necessary_roles_are', ['roles' => implode(', ', $roles)]);
        }

        return ErrorResource::make($message, 403);
    }

    public static function forPermissions(array $permissions): ErrorResource
    {
        $message = __('messages.user_does_not_have_the_right_permissions');

        if (config('permission.display_permission_in_exception')) {
            $message .= __('messages.necessary_permissions_are', ['permissions' => implode(', ', $permissions)]);
        }

        return ErrorResource::make($message, 403);
    }

    public static function forRolesOrPermissions(array $rolesOrPermissions): ErrorResource
    {
        $message = __('messages.user_does_not_have_any_of_the_necessary_access_rights');

        if (config('permission.display_permission_in_exception') && config('permission.display_role_in_exception')) {
            $message .= __('messages.necessary_roles_or_permissions_are', ['rolesOrPermissions' => implode(', ', $rolesOrPermissions)]);
        }

        return ErrorResource::make($message, 403);
    }

    public static function notLoggedIn(): ErrorResource
    {
        return ErrorResource::make(__('messages.unauthorized'), 401);
    }
}
