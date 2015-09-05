<?php
namespace Manage\Controller;
use Think\Controller;

class AbcController extends CommonController {
	
	public function index() {
		
		$count = M('abc')->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('abc')->order('id desc')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('type', '广告位列表');
		$this->display();
	}
	//添加
	public function add() {
		//当前控制器名称		
		$actionName = strtolower(CONTROLLER_NAME);
		if (IS_POST) {
			$type = I('type', 0,'intval');
			$data = I('post.', '');
			$data['name'] = trim($data['name']);
			$data['type'] = I('type', 0,'intval');
			$data['setting'] = I('setting', '','');

			if (1 == $data['type']) {
				$data['width'] = 0;
				$data['height'] = 0;
			}
			if (empty($data['name'])) {
				$this->error('广告位名称必须填写！');
			}

			if (empty($data['type'])) {
				$this->error('请选择广告类型！');
			}


			$ad = M('abc')->where(array('name' => $data['name']))->find();
			if ($ad) {
				$this->error('广告位名称已经存在！');
			}


			if($id = M('abc')->add($data)) {
				$this->success('添加成功',U('Abc/index'));
			}else {
				$this->error('添加失败');
			}
			exit();
		}
		$this->display();
	}



	//编辑文章
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);
		if (IS_POST) {
			$data = I('post.', '');
			$data['id'] = intval($data['id']);
			$data['type'] = intval($data['type']);
			$data['setting'] = I('setting', '','');
		
			$data['name'] = trim($data['name']);
			if (empty($data['name'])) {
				$this->error('广告位名称必须填写！');
			}
			if (empty($data['type'])) {
				$this->error('请选择广告类型！');
			}
			if (1 == $data['type']) {
				$data['width'] = 0;
				$data['height'] = 0;
			}
			
			if (M('abc')->where(array('name' => $data['name'], 'id' => array('neq', $id)))->find()) {
				$this->error('广告位名称已经存在！');
			}

			if (false !== M('abc')->save($data)) {
				$this->success('修改成功', U('Abc/index'));
			}else {

				$this->error('修改失败');
			}
			exit();
		}
		$vo = M($actionName)->find($id);		
		$vo['setting'] = htmlspecialchars($vo['setting']);//ueditor
		$this->assign('vo', $vo);
		$this->display();
	}




	//删除
	public function del() {

		$id = I('id',0 , 'intval');
		$Model =M();			

		//getField('id'),返回一个结果，getField('id',true),返回满足的所有(数组)
		$child= M('abcDetail')->where(array('aid' => $id))->find();
		if($child) {
			$this->error('删除失败!请先删除广告位下的广告内容!');
		}

		if (M('abc')->delete($id)) {
			$this->success('彻底删除成功', U('Abc/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}



	//广告列表
	public function detail() {
		$aid = I('aid', 0, 'intval');
		if (empty($aid)) {
			$this->error('参数错误！');
		}

		$cate = M('abc')->find($aid);
		$where['aid'] = $aid;
		
		$count = M('abcDetail')->where($where)->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('abcDetail')->where($where)->order('sort,id')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('type', $cate['name'].'-广告列表');
		$this->assign('cate', $cate);

		$this->display();
	}

		//添加
	public function addDetail() {
		$aid = I('aid', 0, 'intval');
		if (IS_POST) {
			//M验证
			$data = I('post.');
			$data['content'] = I('content', '', '');
			$data['starttime'] = I('starttime', time(),'strtotime');			
			$data['endtime'] = I('endtime', strtotime('+1 months'),'strtotime');
			switch ($data['type']) {
				case 1:
					$txt = '内容';
					break;
				case 2:
					$txt = '图片';
					break;
				case 3:
					$txt = 'flash';
					break;
			}
			if ($data['url'] == 'http://') {
				$data['url'] = '';
			}
			$db = M('abcDetail');
			if (empty($data['aid'])) {
				$this->error('广告位不存在，不能添加广告内容');
			}
			if (empty($data['title'])) {
				$this->error('标题不能为空');
			}
			if (empty($data['content'])) {
				$this->error($txt.'不能为空');
			}
			

			if($id = M('abcDetail')->add($data)) {
				M('abc')->where(array('id' => $data['aid']))->setInc('items');

				$attid = get_att_attachment($data['content']);//内容中附件id				
				insert_att_index($attid,$id,0,'abc');//attachmentindex入库

				$this->success('添加成功',U('Abc/detail', array('aid'=> $data['aid'])));
			}else {
				$this->error('添加失败');
			}
			exit();
		}
		$this->assign('cate', M('abc')->find($aid));
		$this->display();
	}

	//编辑文章
	public function editDetail() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		if (IS_POST) {
			$data = I('post.', '');
			$data['id'] = intval($data['id']);
		
			$data['content'] = I('content', '', '');
			$data['starttime'] = I('starttime', time(),'strtotime');			
			$data['endtime'] = I('endtime', strtotime('+1 months'),'strtotime');
			switch ($data['type']) {
				case 1:
					$txt = '内容';
					break;
				case 2:
					$txt = '图片';
					break;
				case 3:
					$txt = 'flash';
					break;
			}
			if ($data['url'] == 'http://') {
				$data['url'] = '';
			}
			$db = M('abcDetail');
			if (empty($data['id'])) {
				$this->error('参数错误！');
			}
			if (empty($data['aid'])) {
				$this->error('广告位不存在，不能修改对应的广告内容');
			}
			if (empty($data['title'])) {
				$this->error('标题不能为空');
			}
			if (empty($data['content'])) {
				$this->error($txt.'不能为空');
			}

			if (false !== M('abcDetail')->save($data)) {

				M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'abc'))->delete();
				$attid = get_att_attachment($data['content']);//内容中附件id				
				insert_att_index($attid,$id,0,'abc');//attachmentindex入库
				$this->success('修改成功',U('Abc/detail', array('aid'=> $data['aid'])));
			}else {

				$this->error('修改失败');
			}
			exit();
		}

		$vo = M('abcDetail')->find($id);
		$vo['content'] = htmlspecialchars($vo['content']);//ueditor
		$this->assign('cate', M('abc')->find($vo['aid']));
		$this->assign('vo', $vo);
		$this->display();
	}


	//删除
	public function delDetail() {

		$id = I('id',0 , 'intval');
		$aid = I('aid',0 , 'intval');
		
		if (M('abcDetail')->delete($id)) {			
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'abc'))->delete();
			M('abc')->where(array('id' => $aid))->setDec('items');
			$this->success('彻底删除成功', U('Abc/detail', array('aid' => $aid)));
		}else {
			$this->error('彻底删除失败');
		}
	}

	//批量更新排序
	public function sort() {
		$aid = I('aid', 0, 'intval');
		$sortlist = I('sortlist', array(), 'intval');
		if (empty($aid)) {
			$this->error('参数错误！');
		}

		foreach ($sortlist as $k => $v) {
			$data = array(
					'id' => $k,
					'sort' => $v,
				);
			M('abcDetail')->save($data);		
		}
		$this->redirect('Abc/detail', array('aid' => $aid));
	}


	//获取广告代码
	public function getcode() {
		$id = I('id', 0, 'intval');
		if (empty($id)) {
			$this->error('参数错误！');
		}
		
		$this->assign('id', $id);
		$this->display();
	}






}



?>