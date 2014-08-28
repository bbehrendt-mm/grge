<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-08-24 17:22:41 --- CRITICAL: Kohana_Exception [ 0 ]: Cannot create instances of abstract Controller_Web ~ SYSPATH\classes\Kohana\Request\Client\Internal.php [ 87 ] in D:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php:114
2014-08-24 17:22:41 --- DEBUG: #0 D:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#1 D:\xampp\htdocs\grge\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#2 D:\xampp\htdocs\grge\index.php(120): Kohana_Request->execute()
#3 {main} in D:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php:114
2014-08-24 17:22:59 --- CRITICAL: ErrorException [ 2 ]: array_flip(): Can only flip STRING and INTEGER values! ~ APPPATH\classes\Error.php [ 13 ] in file:line
2014-08-24 17:22:59 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'array_flip(): C...', 'D:\xampp\htdocs...', 13, Array)
#1 D:\xampp\htdocs\grge\application\classes\Error.php(13): array_flip(Array)
#2 D:\xampp\htdocs\grge\application\classes\Error.php(35): Error::r('GRGE-0000-0000')
#3 D:\xampp\htdocs\grge\application\views\framework.php(30): Error::m('GRGE-0000-0000')
#4 D:\xampp\htdocs\grge\system\classes\Kohana\View.php(61): include('D:\xampp\htdocs...')
#5 D:\xampp\htdocs\grge\system\classes\Kohana\View.php(348): Kohana_View::capture('D:\xampp\htdocs...', Array)
#6 D:\xampp\htdocs\grge\system\classes\Kohana\View.php(228): Kohana_View->render()
#7 D:\xampp\htdocs\grge\system\classes\Kohana\Response.php(160): Kohana_View->__toString()
#8 D:\xampp\htdocs\grge\application\classes\Controller\Web.php(7): Kohana_Response->body(Object(View))
#9 D:\xampp\htdocs\grge\system\classes\Kohana\Controller.php(84): Controller_Web->action_framework()
#10 [internal function]: Kohana_Controller->execute()
#11 D:\xampp\htdocs\grge\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Web))
#12 D:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 D:\xampp\htdocs\grge\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 D:\xampp\htdocs\grge\index.php(120): Kohana_Request->execute()
#15 {main} in file:line