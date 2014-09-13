<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-09-09 13:12:21 --- CRITICAL: ErrorException [ 8 ]: Undefined index: season ~ APPPATH\classes\Controller\Ranking.php [ 71 ] in C:\xampp\htdocs\grge\application\classes\Controller\Ranking.php:71
2014-09-09 13:12:21 --- DEBUG: #0 C:\xampp\htdocs\grge\application\classes\Controller\Ranking.php(71): Kohana_Core::error_handler(8, 'Undefined index...', 'C:\\xampp\\htdocs...', 71, Array)
#1 [internal function]: Controller_Ranking->{closure}(Array)
#2 C:\xampp\htdocs\grge\application\classes\Controller\Ranking.php(85): array_map(Object(Closure), Array)
#3 C:\xampp\htdocs\grge\application\classes\Controller\Ranking.php(119): Controller_Ranking->convert_data(Array)
#4 C:\xampp\htdocs\grge\application\classes\Controller.php(67): Controller_Ranking->japi_multi()
#5 C:\xampp\htdocs\grge\system\classes\Kohana\Controller.php(84): Controller->action_japi()
#6 [internal function]: Kohana_Controller->execute()
#7 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Ranking))
#8 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 C:\xampp\htdocs\grge\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#10 C:\xampp\htdocs\grge\index.php(120): Kohana_Request->execute()
#11 {main} in C:\xampp\htdocs\grge\application\classes\Controller\Ranking.php:71