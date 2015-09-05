<?php
namespace Mobile\Controller;
use Think\Controller;

//公共验证控制器
class MobileCommonController extends Controller {
	
	// 空操作，404页面
	public function _empty(){		
		header("HTTP/1.1 404 Not Found");  
		header("Status: 404 Not Found");
		$this->display(get_tpl('404.html'));
	}


    protected function _initialize(){
        if (C('CFG_WEBSITE_CLOSE') == 1) {
			exit_msg(C('CFG_WEBSITE_CLOSE_INFO'));
		}
    }

}


?>