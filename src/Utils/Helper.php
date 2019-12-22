<?php

namespace Utils;

use Model\Entities\User;

class Helper {
    public static function getSessionUser(){
        global $app;
        if (!isset($app)) return null;
        if (null === $user = $app['session']->get('user')) {
            return null;
        }
        return User::find($user['id']);
    }

    public static function rememberPage($page) {
        global $app;
        if (!isset($app)) return;
        $app['session']->set('page', $page);
    }

    public static function restorePage(&$currentPage, &$perPage) {
        global $app;
        if (!isset($app)) return;
        $page = $app['session']->get('page');
        if (!$currentPage) {
          if ($page) {
            $currentPage = $page['pageNumber'];
          } else {
            $currentPage = 1;
          }
        }
        if (!$perPage) {
          if ($page) {
            $perPage = $page['perPage'];
          } else {
            $perPage = $app['config']['page']['perpage'];
          }
        }

    }
}
