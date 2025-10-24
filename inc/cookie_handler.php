<?php
if (!isset($_SESSION)) session_start();

class CookieHandler {
    const COOKIE_DURATION = 30; // dias
    const COOKIE_PATH = '/';
    const SECURE = true; // true para HTTPS
    const HTTPONLY = true;
    const SAMESITE = 'Strict';

    // Configura um cookie com opções seguras
    public static function setCookie($name, $value, $days = null) {
        if ($days === null) $days = self::COOKIE_DURATION;
        
        $options = array(
            'expires' => time() + (86400 * $days),
            'path' => self::COOKIE_PATH,
            'secure' => self::SECURE,
            'httponly' => self::HTTPONLY,
            'samesite' => self::SAMESITE
        );

        setcookie($name, $value, $options);
    }

    // Recupera o valor de um cookie
    public static function getCookie($name) {
        return $_COOKIE[$name] ?? null;
    }

    // Remove um cookie
    public static function removeCookie($name) {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', [
                'expires' => time() - 3600,
                'path' => self::COOKIE_PATH,
                'secure' => self::SECURE,
                'httponly' => self::HTTPONLY,
                'samesite' => self::SAMESITE
            ]);
            unset($_COOKIE[$name]);
        }
    }

    // Define o cookie de login
    public static function setLoginCookie($userId, $userToken) {
        self::setCookie('user_id', $userId, 30);
        self::setCookie('user_token', $userToken, 30);
    }

    // Remove os cookies de login
    public static function removeLoginCookies() {
        self::removeCookie('user_id');
        self::removeCookie('user_token');
    }
}
