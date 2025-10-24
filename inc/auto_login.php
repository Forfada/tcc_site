<?php
if (!isset($_SESSION)) session_start();
require_once("cookie_handler.php");

function checkAutoLogin() {
    // Se já está logado, não precisa verificar
    if (isset($_SESSION['id'])) return;
    
    // Verifica se existem os cookies de login
    $userId = CookieHandler::getCookie('user_id');
    $userToken = CookieHandler::getCookie('user_token');
    
    if ($userId && $userToken) {
        try {
            $bd = open_database();
            $stmt = $bd->prepare("SELECT id, u_user, u_num, foto, auth_token FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica se o token corresponde
            if ($user && hash_equals($user['auth_token'], $userToken)) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['nome'] = $user['u_user'];
                $_SESSION['user'] = $user['u_num'];
                $_SESSION['foto'] = $user['foto'];
                
                // Atualiza o token para maior segurança
                $newToken = bin2hex(random_bytes(32));
                $stmt = $bd->prepare("UPDATE usuarios SET auth_token = :token WHERE id = :id");
                $stmt->execute([':token' => $newToken, ':id' => $userId]);
                
                // Atualiza o cookie com o novo token
                CookieHandler::setLoginCookie($userId, $newToken);
            } else {
                // Token inválido, remove os cookies
                CookieHandler::removeLoginCookies();
            }
        } catch (Exception $e) {
            // Em caso de erro, remove os cookies
            CookieHandler::removeLoginCookies();
        }
    }
}