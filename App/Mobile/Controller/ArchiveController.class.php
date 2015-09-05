<?php
//归档
namespace Mobile\Controller;

class ArchiveController extends MobileCommonController{
	//方法：index
	public function index(){

		$cid = I('cid', 0,'intval');
		$modelid = I('modelid', 1,'intval');
		$year = I('year', 0,'intval');
		$month = I('month', 0,'intval');
		$orderby = 'publishtime desc';


		$modelname = M('model')->where(array('id' => $modelid))->getField('name');
		$modelname = empty($modelname)? '文档' : str_replace('模型', '', $modelname);
		$title = $modelname.'存档列表';
	

		$this->assign('title', $title);
		$this->assign('cid', $cid);
		$this->assign('modelid', $modelid);	
		$this->assign('modelname', $modelname);	
		$this->assign('year', $year);		
		$this->assign('month', $month);
		$this->assign('page', '');
		$this->assign('purl', U('Archive/index', array('modelid' => $modelid)));
		$this->display();

	}


}

?>