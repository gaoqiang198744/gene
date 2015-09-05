<?php

$config_base = array(

	'USER_AUTH_KEY'   => 'uid',			//用户认证识别号
	
	//加载自定义标签
	'TAGLIB_PRE_LOAD'=>'Common\\LibTag\\Yang,Common\\LibTag\\Other',//预加载的tag
	'TAGLIB_BUILD_IN' => 'cx', //内置标签

	//URL模式
	'URL_MODEL' =>  3,
	'URL_ROUTER_ON' => false,	

	//开启静态缓存
	'HTML_CACHE_ON' => get_meta_value('MOBILE_HTML_CACHE_ON'),
	'HTML_CACHE_RULES' => get_meta_value('HTML_CACHE_RULES_COMMON'),


	'VIEW_PATH'=>'./Public/'.MODULE_NAME .'/',
	'DEFAULT_THEME'  => get_cfg_value('CFG_MOBILE_THEMESTYLE'),//默认主题风格
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => __ROOT__. '/Public/'.MODULE_NAME. '/' . get_cfg_value('CFG_MOBILE_THEMESTYLE'),		
		'__DATA__' => __ROOT__. '/Data',
		'__AVATAR__' => __ROOT__. '/avatar',
	),

);
return array_merge(get_cfg_value(),$config_base);

?>