<?php
namespace Manage\Controller;


class AreaController extends CommonController{
	
	public function index(){
		$pid = I('pid', 0,'intval');
		
		$count = M('area')->where(array('pid' => $pid))->count();
		$page = new \Common\Lib\Page($count, 50);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('area')->where(array('pid' => $pid))->order('sort,id')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('pid', $pid);
		$this->assign('type', '地区列表');

		$this->display();
	}

	public function add() {
		$this->error('禁用添加');
	}


	//编辑
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);

		if (IS_POST) {
			//M验证
			$data['id'] = I('id',  0, 'intval');
			$data['name'] = I('name', '', 'trim');
			$data['sname'] = I('sname', '', 'trim');
			$data['ename'] = I('ename', '', 'trim');
			$data['pid'] = I('pid', 0, 'intval');			
			$data['sort'] = I('sort',  0, 'intval');

			if (empty($data['name'])) {
				$this->error('名称不能为空');
			}
			if (empty($data['sname'])) {
				$this->error('简称不能为空');
			}

			if (empty($data['id'])) {
				$this->error('参数错误');
			}
			$vo = M('area')->where(array('id' => array('neq', $data['id']), 'name' => $data['name']))->find();
			if ($vo) {
				$this->error('区域名称已经存在，请重新填写');
			}


			if (false !== M('area')->save($data)) {
				$this->success('修改成功',U('Area/index', array('pid' => $data['pid'])));
			}else {

				$this->error('修改失败');
			}
			exit();
		}

		$this->assign('vo', M($actionName)->find($id));
		if ($pid) {
			$pinfo = M($actionName)->find($pid);
			$this->assign('pname', $pinfo['name']);
		}else {
			$this->assign('pname', '顶级');
		}
		$this->assign('pid', $pid);
		$this->assign('type', '修改区域信息');
		$this->display();
	}


	//批量更新排序
	public function sort() {
		$pid = I('get.pid', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);

		$sortlist = I('sortlist', array(), 'intval');
		foreach ($sortlist as $k => $v) {
			$data = array(
					'id' => $k,
					'sort' => $v,
				);
			M($actionName)->save($data);		
		}
		$this->redirect('Area/index', array('pid' => $pid));
	}

	public function del() {
		$this->error('禁用删除');
	}


	public function createJsArea(){

		if (get_js_city()) {
			$this->success('生成js成功',U('Area/index', array('pid' => 0)));
		}else {
			$this->success('生成js失败');
		}
	}





}


?>