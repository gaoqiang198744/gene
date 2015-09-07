<?php

namespace Home\Controller;

class AboutController extends HomeCommonController{
	//方法：index
	public function index(){
		//go_mobile();
		$this->assign('title', C('CFG_WEBNAME'));
		$this->display();

	}
}

?>