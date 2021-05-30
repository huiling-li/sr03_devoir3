<?php
ini_set('display_errors', 0);//修改php.ini配置文件 改变参数
ini_set('session.cookie_httponly', 1); // interdire la lecture du cookie de session avec un script
ini_set('session.use_only_cookies', 1); // interdire le cookie de session via l'URL (uniquement par cookie)
?>