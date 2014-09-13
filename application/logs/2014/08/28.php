<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-08-28 23:09:02 --- CRITICAL: Session_Exception [ 1 ]: Error reading session data. ~ SYSPATH\classes\Kohana\Session.php [ 324 ] in C:\xampp\htdocs\grge\system\classes\Kohana\Session.php:125
2014-08-28 23:09:02 --- DEBUG: #0 C:\xampp\htdocs\grge\system\classes\Kohana\Session.php(125): Kohana_Session->read(NULL)
#1 C:\xampp\htdocs\grge\system\classes\Kohana\Session.php(54): Kohana_Session->__construct(NULL, NULL)
#2 C:\xampp\htdocs\grge\application\classes\Controller.php(33): Kohana_Session::instance()
#3 C:\xampp\htdocs\grge\system\classes\Kohana\Controller.php(69): Controller->before()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Web))
#6 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\xampp\htdocs\grge\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\xampp\htdocs\grge\index.php(120): Kohana_Request->execute()
#9 {main} in C:\xampp\htdocs\grge\system\classes\Kohana\Session.php:125
2014-08-28 23:09:50 --- CRITICAL: Database_Exception [ 8192 ]: mysql_connect(): The mysql extension is deprecated and will be removed in the future: use mysqli or PDO instead ~ MODPATH\database\classes\Kohana\Database\MySQL.php [ 67 ] in C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\MySQL.php:431
2014-08-28 23:09:50 --- DEBUG: #0 C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\MySQL.php(431): Kohana_Database_MySQL->connect()
#1 C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database.php(478): Kohana_Database_MySQL->escape('de')
#2 C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\Query\Builder.php(116): Kohana_Database->quote('de')
#3 C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\Query\Builder\Select.php(372): Kohana_Database_Query_Builder->_compile_conditions(Object(Database_MySQL), Array)
#4 C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\Query.php(234): Kohana_Database_Query_Builder_Select->compile(Object(Database_MySQL))
#5 C:\xampp\htdocs\grge\application\classes\Model\User.php(60): Kohana_Database_Query->execute()
#6 C:\xampp\htdocs\grge\application\classes\Controller\Account.php(77): Model_User::mt2gr(31883, 'de')
#7 C:\xampp\htdocs\grge\application\classes\Controller.php(46): Controller_Account->japi_login()
#8 C:\xampp\htdocs\grge\system\classes\Kohana\Controller.php(84): Controller->action_japi()
#9 [internal function]: Kohana_Controller->execute()
#10 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Account))
#11 C:\xampp\htdocs\grge\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 C:\xampp\htdocs\grge\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 C:\xampp\htdocs\grge\index.php(120): Kohana_Request->execute()
#14 {main} in C:\xampp\htdocs\grge\modules\database\classes\Kohana\Database\MySQL.php:431