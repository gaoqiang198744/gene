<?php
namespace Mobile\Controller;

class AbcController extends MobileCommonController{
	//shows
	public function shows(){
		
		$id = I('id', 0, 'intval');
		$flag = I('flag', 0, 'intval');
		if (!empty($id)) {
			echo get_abc($id,$flag);
		}else {
			echo '';
		}

	}
}

?>