<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.04.2018
 * Time: 22:02
 */

namespace app\models\authServices;


class NoService extends AuthService
{
    function auth()
    {
        return null;
    }
}