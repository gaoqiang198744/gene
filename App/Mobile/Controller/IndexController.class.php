<?php

namespace Mobile\Controller;

class IndexController extends MobileCommonController{
	//方法：index
	public function index(){

		$this->assign('title', C('CFG_WEBNAME'));
		$this->display();

	}
}

?>