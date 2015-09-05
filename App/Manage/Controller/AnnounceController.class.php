<?php
namespace Manage\Controller;

class AnnounceController extends CommonController {
	
	public function index() {
		
		$count = M('announce')->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('announce')->order('starttime desc')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('type', '公告列表');

		$this->display();
	}
	//添加
	public function add() {
		//当前控制器名称		
		$actionName = strtolower(CONTROLLER_NAME);
		if (IS_POST) {
			$this->addPost();
			exit();
		}
		$this->display();
	}

	//
	public function addPost() {

		//M验证
		$data = I('post.');
		$data['content'] = I('content', '', '');
		$data['starttime'] = I('starttime', time(), 'strtotime');
		$data['endtime'] = I('endtime', time(), 'strtotime');
		$data['posttime'] = time();
		$data['status'] = 1;

		$content = I('content', '', '');
		$validate = array(
			array('title','require','公告标题不能为空！'), 
			array('content','require','公告内容不能为空！'), 
		);


		$db = M('announce');
		if (!$db->validate($validate)->create()) {
			$this->error($db->getError());
		}


		if($id = $db->add($data)) {
			$attid = get_att_content($content);//内容中的图片			
			insert_att_index($attid,$id,0,'announce');//attachmentindex入库
			$this->success('添加成功',U('Announce/index'));
		}else {
			$this->error('添加失败');
		}
	}

	//编辑文章
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);
		if (IS_POST) {
			$this->editPost();
			exit();
		}
		
		$this->assign('vo', M($actionName)->find($id));
		$this->display();
	}


	//修改文章处理
	public function editPost() {

		//M验证
		$data = I('post.');
		$id = $data['id'] = I('id', 0, 'intval');
		$data['content'] = I('content', '', '');
		$data['starttime'] = I('starttime', time(), 'strtotime');
		$data['endtime'] = I('endtime', time(), 'strtotime');

		$validate = array(
			array('title','require','公告标题不能为空！'), 
			array('content','require','公告内容不能为空！'), 
		);


		$db = M('announce');
		if (!$db->validate($validate)->create()) {
			$this->error($db->getError());
		}

		

		if (false !== M('announce')->save($data)) {

			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'announce'))->delete();
			$attid = get_att_content($content);//内容中的图片			
			insert_att_index($attid,$id,0,'announce');//attachmentindex入库
			$this->success('修改成功', U('Announce/index'));
		}else {

			$this->error('修改失败');
		}
		
	}



	//彻底删除文章
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}
		
		if (M('announce')->delete($id)) {
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'announce'))->delete();
			$this->success('彻底删除成功', U('Announce/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除文章
	public function delBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));

		if (M('announce')->where($where)->delete()) {
			M('attachmentindex')->where(array('arcid' => array('in', $idArr), 'modelid' => 0, 'desc' => 'announce'))->delete();
			$this->success('彻底删除成功', U('Announce/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}




}



?>