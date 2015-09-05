<?php
namespace Manage\Controller;

class LinkController extends CommonController {
	
	public function index() {
					
		
		
		$count = M('link')->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('link')->order('sort')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('type', '友情连接列表');
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
		$data = I('post.','');
		$data['name'] = trim($data['name']);
		$data['url'] = trim($data['url']);
		$data['ischeck'] = I('ischeck', 0, 'intval');
		$data['sort'] = I('sort', 0, 'intval');
		if (empty($data['name']) || empty($data['url'])) {
			$this->error('网站名称或网址不能为空');
		}
		$data['posttime'] = time();


		if($id = M('link')->add($data)) {
			//更新上传附件表
			if (!empty($data['logo'])) {
				$attid = get_att_attachment($data['logo'],true);//内容中的图片			
				insert_att_index($attid,$id,0,'link');//attachmentindex入库
			}

			$this->success('添加成功',U('Link/index'));
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
		$vo = M($actionName)->find($id);
		$this->assign('vo', $vo);
		$this->display();
	}


	//修改文章处理
	public function editPost() {

		$data = I('post.');
		$data['name'] =  trim($data['name']);
		$data['url'] =  trim($data['url']);
		$id = $data['id'] = I('id', 0, 'intval');
		if (empty($data['name']) || empty($data['url'])) {
			$this->error('网站名称或网址不能为空');
		}
		

		if (false !== M('link')->save($_POST)) {
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'link'))->delete();
			//更新上传附件表
			if (!empty($data['logo'])) {
				$attid = get_att_attachment($data['logo'],true);//内容中的图片			
				insert_att_index($attid,$id,0,'link');//attachmentindex入库
			}

			$this->success('修改成功', U('Link/index'));
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
		
		if (M('link')->delete($id)) {			
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'link'))->delete();
			$this->success('彻底删除成功', U('Link/index'));
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

		if (M('link')->where($where)->delete()) {
			M('attachmentindex')->where(array('arcid' => array('in', $idArr), 'modelid' => 0, 'desc' => 'link'))->delete();
			$this->success('彻底删除成功', U('Link/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}




}



?>