<?php
namespace Manage\Controller;

class MetaController extends CommonController {

	public function index() {		

		$groupid = I('groupid', 0, 'intval');//类别ID
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字		

		$where  = array('id' => array('GT',0));
		if (!empty($groupid)) {
			$where['groupid'] = $groupid;
		}
		if (!empty($keyword)) {
			$where['name'] = array('LIKE', "%{$keyword}%");
		}
		
		
		$count = M("meta")->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);		
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$vlist = M("meta")->where($where)->order('id DESC')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $vlist);
		$this->assign('groupid', $groupid);
		$this->assign('keyword', $keyword);
		$this->display();
	}

	public function add() {		

		if (IS_POST) {
			$data = I('post.');
			$data['groupid'] = I('groupid', 0, 'intval');
			$data['value'] = I('value', '', 'trim');
		
			if (empty($data['name'])) {
				$this->error('请填写名称(标识)');
			}

			if(!preg_match('/^[a-zA-Z0-9_]+$/', $data['name'])) {
				$this->error('名称只能由字母、数字和"_"组成');
			}

			if (M('meta')->where(array('name'=> $data['name']))->find()) {
				$this->error('名称(标识)已经存在，请更换');
			}
			

			if (M('meta')->add($data)) {
				F('config/meta', null);
				$this->success('添加成功',U('index'));
			} else {
				$this->error('添加失败');
			}			
			
			exit();
		}
		
		$this->display();
	}

	public function edit() {		
		$id = I('id', 0, 'intval');
		if (IS_POST) {
			$data = I('post.');
			$id = $data['id'] = I('id', 0, 'intval');	
			$data['groupid'] = I('groupid', 0, 'intval');
			$data['value'] = I('value', '', 'trim');
			

			if (empty($data['name'])) {
				$this->error('请填写名称(标识)');
			}

			if(!preg_match('/^[a-zA-Z0-9_]+$/', $data['name'])) {
				$this->error('名称只能由字母、数字和"_"组成');
			}

			if (M('meta')->where(array('name'=> $data['name'], 'id' => array('neq' , $id)))->find()) {
				$this->error('名称(标识)已经存在，请更换');
			}

			if (false !== M('meta')->save($data)) {
				F('config/meta', null);
				$this->success('修改成功',U('index'));
			} else {
				$this->error('修改失败');
			}			
			
			exit();
		}
		$vo = M('meta')->find($id);
		$vo['value'] = htmlspecialchars($vo['value']);//ueditor
	
		$this->assign('vo', $vo);
		$this->display();
	}

	



}


?>