<?php
/**
 * @copyright 	Copyright (c) 2014-2014 0871k.com All rights reserved.
 * @license 	http://www.0871k.com/helpcms/4.html 
 * @link        http://www.xyhcms.com
 * @author 		gosea <gosea199@gmail.com> 
 */  

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',false);//是否调试//部署阶段注释或者设为false
define('APP_PATH', "./App/");//项目路径
define('THINK_PATH', "./Include/");


//判断是否安装
if(!file_exists(APP_PATH.'Common/Conf/db.php'))
{
    header('Location:Install/index.php');
    exit();
}

require THINK_PATH.'ThinkPHP.php';//加载ThinkPHP框架


?>