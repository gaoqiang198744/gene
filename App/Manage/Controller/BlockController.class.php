<?php
namespace Manage\Controller;

class BlockController extends CommonController {
	
	public function index() {
		
		$count = M('block')->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('block')->order('id desc')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('type', '自由块列表');

		$this->display();
	}
	//添加
	public function add() {

		if (IS_POST) {
			$this->addPost();
			exit();
		}

		$this->assign('type', '添加自由块');
		$this->assign('blocktypelist', get_item('blocktype'));

		$this->display();
	}

	//
	public function addPost() {	
		//当前控制器名称		
		$actionName = strtolower(CONTROLLER_NAME);

		$data['name'] = I('name', '', 'htmlspecialchars,trim');
		$data['blocktype'] = I('blocktype', 0, 'intval');
		$data['remark'] = I('remark', '');
		$content = I('content','','');


		if (empty($data['name'])) {
			$this->error('请填写名称');
		}

		if (empty($data['blocktype'])) {
			$this->error('请选择类型');
		}

		if (M('block')->where(array('name' => $data['name']))->find()) {
			$this->error('自由块名称已经存在!');
		}

		$data['content'] = $content[$data['blocktype']];



		if($id = M('block')->add($data)) {

			//更新缓存
			get_block($data['name'], 1);

			//图片类型
			if ($data['blocktype'] == 2) {				
				$attid = get_att_attachment($data['content'],true);//图片的id
				insert_att_index($attid, $id, 0, $actionName);//attachmentindex入库
			} elseif ($data['blocktype'] == 3) {
				$attid = get_att_content($data['content']);//内容中的图片
				insert_att_index($attid, $id, 0, $actionName);//attachmentindex入库
			}

			$this->success('添加成功',U('Block/index'));
		}else {
			$this->error('添加失败');
		}
	}

	//编辑
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);

		if (IS_POST) {
			$this->editPost();
			exit();
		}
		
		$vo = M($actionName)->find($id);
		//非富文本,引号的问题
		$vo['content'] = str_replace("&#39;", "'", $vo['content']);	//只针对input,textarea,ueditor切换	
		$vo['content'] = htmlspecialchars($vo['content']);

		$this->assign('type', '添加自由块');
		$this->assign('blocktypelist', get_item('blocktype'));
		$this->assign('vo', $vo);

		$this->display();
	}


	//修改处理
	public function editPost() {
		$actionName = strtolower(CONTROLLER_NAME);

		$id = $data['id'] = I('id', 0, 'intval');
		$data['name'] = I('name', '', 'htmlspecialchars,trim');
		$data['blocktype'] = I('blocktype', 0, 'intval');
		$data['remark'] = I('remark', '');
		$content = I('content','','');		


		if (empty($data['name'])) {
			$this->error('请填写名称');
		}

		if (empty($data['blocktype'])) {
			$this->error('请选择类型');
		}

		$data['content'] = $content[$data['blocktype']];
	
		
		if (M('block')->where(array('name' => $data['name'], 'id' => array('neq', $id)))->find()) {
			$this->error('自由块名称已经存在!');
		}


		if (false !== M('block')->save($data)) {

			//更新缓存
			get_block($data['name'], 1);

			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => $actionName))->delete();

			//图片类型
			if ($data['blocktype'] == 2) {				
				$attid = get_att_attachment($data['content'],true);//图片的id
				insert_att_index($attid, $id, 0, $actionName);//attachmentindex入库
			} elseif ($data['blocktype'] == 3) {
				$attid = get_att_content($data['content']);//内容中的图片
				insert_att_index($attid, $id, 0, $actionName);//attachmentindex入库
			}
			

			$this->success('修改成功', U('Block/index'));
		}else {

			$this->error('修改失败');
		}
		
	}



	//彻底删除
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}
		$name = M('block')->where(array('id' => $id))->getField('name');//清除F缓存用
		if (M('block')->delete($id)) {
			get_block($name, 1);//清除缓存(更新)
			$this->success('彻底删除成功', U('Block/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除
	public function delBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		$name = M('block')->where($where)->getField('name', true);//清除F缓存用

		if (M('block')->where($where)->delete()) {
			foreach ($name as $v) {
				get_block($v, 1);//清除缓存(更新)
			}
			$this->success('彻底删除成功', U('Block/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}




}



?>