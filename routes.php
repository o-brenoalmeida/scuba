<?php

$page = ($_GET['page']);
$controller = new Controller($page);
switch ($page) {
    case 'register':
        $controller->doRegister();
        break;
    case 'not_found':
        $controller->doNotFound();
        break;
    default:
        $controller = new Controller('login');
        $controller->doLogin();
        break;
}
