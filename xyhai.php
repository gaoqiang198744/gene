<?php
/**
 * @copyright 	Copyright (c) 2014-2014 0871k.com All rights reserved.
 * @license 	http://www.0871k.com/helpcms/4.html 
 * @link        http://www.xyhcms.com
 * @author 		gosea <gosea199@gmail.com> 
 */  

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('BIND_MODULE', 'Manage');
define('APP_DEBUG',false);
define('APP_PATH', "./App/");
define('THINK_PATH', "./Include/");
require THINK_PATH.'ThinkPHP.php';

?>