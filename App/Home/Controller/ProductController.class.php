<?php

namespace Home\Controller;

class ProductController extends HomeCommonController{
	//方法：index
	public function index(){
		//go_mobile();
		$this->assign('title', C('CFG_WEBNAME'));
                $this->assign('mid',2);
		$this->display();

	}
}

?>