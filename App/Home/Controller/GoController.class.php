<?php
namespace Home\Controller;

class GoController extends HomeCommonController{
	
	public function index(){
		
		$url = I('url', 0, '');	
		if (!empty($url)) {
			redirect($url);
		}
		
	}

	public function link(){
		
		$url = I('url', 0, '');	
		if (!empty($url)) {
			$url = base64_decode($url);
			redirect($url);
		}
		
	}
}

?>