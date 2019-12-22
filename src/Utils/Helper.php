<?php

namespace Utils;

use Model\Entities\User;

class Helper {
    public static function getSessionUser($app){
        if (null === $user = $app['session']->get('user')) {
            return null;
        }
        return User::find($user['id']);
    }
}
