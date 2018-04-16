<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.04.2018
 * Time: 16:05
 */

namespace app\models\authServices;


use app\models\DataBase;
use Exception;

abstract class AuthService
{
    const EMPTY_LOGIN = 'введите логин';

    const EMPTY_PASSWORD = 'введите пароль';

    const TOO_LONG = 'длина логина/пароля может быть не более 200 символов!';

    const TOO_SHORT = 'длина логина/пароля может быть не менее 5 символов!';

    /**
     * @var string
     */
    protected $serviceTemplate = null;

    /**
     * @var array
     */
    static $postData = [];

    /**
     * @var array
     */
    static $getData = [];

    /**
     * @var array
     */
    static $errors = [];

    /**
     * @var string
     */
    static $login = null;

    /**
     * @var string
     */
    static $password = null;

    /**
     * @var string
     */
    static $password2 = null;

    /**
     * @return string|null
     */
    abstract function action();

    /**
     * AuthService constructor.
     * @param string|null $serviceTemplate
     */
    public function __construct($serviceTemplate = null)
    {
        $this->serviceTemplate = $serviceTemplate;
    }

    /**
     * @return string
     */
    public function getServiceTemplate()
    {
        return $this->serviceTemplate;
    }

    /**
     * @return array
     */
    protected function getUserData()
    {
        $query = 'SELECT * FROM raffle_users WHERE user_login = ?';
        $user = DataBase::getInstance()->prepare($query);
        $user->execute([self::$login]);

        return [
            'loginMatches' => $user->rowCount(),
            'user' => $user->fetch()
        ];
    }

    protected function checkLogPass()
    {
        if (self::$login === '') self::$errors[] = self::EMPTY_LOGIN;
        if (self::$password === '') self::$errors[] = self::EMPTY_PASSWORD;
        $this->checkInputLength();
    }

    protected function checkInputLength()
    {
        $long = (mb_strlen(self::$login) > 200 or mb_strlen(self::$password) > 200);
        $short = (mb_strlen(self::$login) < 5 or mb_strlen(self::$password) < 5);

        if ($long) self::$errors[] = self::TOO_LONG;
        if ($short) self::$errors[] = self::TOO_SHORT;
    }

    /**
     * @return AuthService
     * @throws \Exception
     */
    static function check()
    {
        self::$postData = $_POST;
        self::$getData = $_GET;

        self::$login = trim(self::$postData['login']);
        self::$password = self::$postData['pass'];
        self::$password2 = self::$postData['pass2'];

        if (isset(self::$getData['action'])) {
            switch (self::$getData['action']) {
                case 'signUp':
                    return new SignUpService('signUp.html');
                    break;
                case 'login':
                    return new LoginService('login.html');
                    break;
                case 'logout':
                    return new LogoutService();
                    break;
                default:
                    throw new Exception('undefined action for ' . __METHOD__);
                    break;
            }
        }
    else
        return new NoService();
    }
}