<?php
namespace Manage\Controller;
use Common\Lib\Category;

class SpecialController extends CommonController {
	
	public function index() {

		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字	
		$where = array('special.status' => 0);		
		
		if (!empty($keyword)) {
			$where['special.title'] = array('LIKE', "%{$keyword}%");
		}	
		
		
		$count = D('SpecialView')->where($where)->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D('SpecialView')->nofield('content')->where($where)->order('id DESC')->limit($limit)->select();

		$this->assign('keyword', $keyword);
		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('type', '专题列表');
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

		$cate = get_category(2);
		$cate = Category::toLevel($cate);
		$_styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Special_*');

		$styleShowList = array();
		foreach ($_styleShowList as $v) {
			if (strpos($v, 'Special_index') === false) {
				$styleShowList[] = $v;
			}
		}

		$this->assign('cate', $cate);
		$this->assign('styleShowList', $styleShowList);
		$this->assign('flagtypelist', get_item('flagtype'));
		$this->assign('type', '添加专题');
		$this->display();
	}

	//
	public function addPost() {

		$data = I('post.');
		$data['cid'] = I('cid', 0, 'intval');		
		$data['title'] = I('title', '', 'htmlspecialchars,rtrim');
		$data['shorttitle'] = I('shorttitle', '', 'htmlspecialchars,rtrim');
		$data['keywords'] = trim($data['keywords']);
		$data['content'] = I('content', '', '');
		$data['publishtime'] = I('publishtime', time(),'strtotime');
		$data['updatetime'] = time();
		$data['click'] = rand(10,95);
		$data['status'] = 0;
		$data['aid'] = session(C('USER_AUTH_KEY'));

		$flags = I('flags', array(),'intval');
		$pic = $data['litpic'];

	

		$pic = I('litpic', '', 'htmlspecialchars,trim');
		if (empty($data['title'])) {
			$this->error('专题名称不能为空');
		}

		if (empty($data['template'])) {
			$this->error('请选择专题模板');
		}


		//图片标志
		if (!empty($pic) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		$data['flag'] = 0;
		foreach ($flags as $v) {
			$data['flag'] += $v;
		}
	

		//获取属于分类信息,得到modelid		
		//$selfCate = Category::getSelf(get_category(0), $cid);//当前栏目信息
		//$modelid = $selfCate['modelid'];

			
		if($id = M('special')->add($data)) {

			//更新上传附件表
			if (!empty($pic)) {
				$attid = get_att_attachment($pic,true);				
				insert_att_index($attid,$id,0,'special');//attachmentindex入库
			}	

			//更新静态缓存
			del_cache_html('Special/index', false, 'special:index');	
			del_cache_html('Index_index', false, 'index:index');		


			$this->success('添加成功',U('Special/index'));
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


		$cate = get_category(2);
		$cate = Category::toLevel($cate);
		$_styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Special_*');
		$styleShowList = array();		
		foreach ($_styleShowList as $v) {
			if (strpos($v, 'Special_index') === false) {
				$styleShowList[] = $v;
			}
		}

		$vo = M($actionName)->find($id);
		$vo['content'] = htmlspecialchars($vo['content']);//ueditor

		$this->assign('cate', $cate);
		$this->assign('styleShowList', $styleShowList);
		$this->assign('vo', $vo);
		$this->assign('flagtypelist', get_item('flagtype'));//文档属性
		$this->assign('type', '修改专题');
		$this->display();
	}


	//修改处理
	public function editPost() {

		$data = I('post.');
		$id = $data['id'] = I('id', 0, 'intval');
		$data['cid'] = I('cid', 0, 'intval');		
		$data['title'] = I('title', '', 'htmlspecialchars,rtrim');
		$data['shorttitle'] = I('shorttitle', '', 'htmlspecialchars,rtrim');
		$data['keywords'] = trim($data['keywords']);
		$data['content'] = I('content', '', '');
		$data['publishtime'] = I('publishtime', time(),'strtotime');
		$data['updatetime'] = time();

		$flags = I('flags', array(),'intval');
		$pic = $data['litpic'];

		if (empty($data['title'])) {
			$this->error('专题名称不能为空');
		}
		
		if (empty($data['template'])) {
			$this->error('请选择专题模板');
		}


		//图片标志
		if (!empty($pic) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		$data['flag'] = 0;
		foreach ($flags as $v) {
			$data['flag'] += $v;
		}


		//获取属于分类信息,得到modelid		
		//$selfCate = Category::getSelf(get_category(0), $data['cid']);//当前栏目信息
		//$modelid = $selfCate['modelid'];

	
		if (false !== M('special')->save($data)) {
			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'special'))->delete();
			
			//更新上传附件表
			if (!empty($pic)) {
				$attid = get_att_attachment($pic,true);				
				insert_att_index($attid,$id,0,'special');//attachmentindex入库
			}

			//更新静态缓存
			del_cache_html('Special/index', false, 'special:index');
			del_cache_html('Special/shows_'.$id, false, 'special:shows');


			$this->success('修改成功', U('Special/index'));
		}else {

			$this->error('修改失败');
		}
		
	}


	//回收站列表
	public function trach() {
		
		$where = array('special.status' => 1);
		$count = D('SpecialView')->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D('SpecialView')->nofield('content')->where($where)->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('subcate', '');
		$this->assign('type', '回收站');
		$this->display('index');
	}

	//删除到回收站
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}


		if (false !== M('special')->where(array('id' => $id))->setField('status', 1)) {
			
			//更新静态缓存
			del_cache_html('Special/index', false, 'special:index');
			del_cache_html('Special/shows_'.$id, false, 'special:shows');

			$this->success('删除成功', U('Special/index'));
			
		}else {
			$this->error('删除失败');
		}
	}

	//批量删除到回收站
	public function delBatch() {

		$idArr = I('key',0 , 'intval');

		if (!is_array($idArr)) {
			$this->error('请选择要删除的项');
		}

		if (false !== M('special')->where(array('id' => array('in', $idArr)))->setField('status', 1)) {
			
			//getlastsql();
			//更新静态缓存
			del_cache_html('Special/index', false, 'special:index');
			foreach ($idArr as $v) {
				del_cache_html('Special/shows_'.$v, false, 'special:shows');
			}

			
			$this->success('批量删除成功', U('Special/index'));
			
		}else {
			$this->error('批量删除文失败');
		}
	}

	//还原
	public function restore() {
		
		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->restoreBatch();
			return;
		}

		if (false !== M('special')->where(array('id' => $id))->setField('status', 0)) {
			
			$this->success('还原成功', U('Special/trach'));
			
		}else {
			$this->error('还原失败');
		}
	}

	//批量还原
	public function restoreBatch() {
		
		$idArr = I('key',0 , 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要还原的项');
		}

		if (false !== M('special')->where(array('id' => array('in', $idArr)))->setField('status', 0)) {
			
			$this->success('还原成功', U('Special/trach'));
			
		}else {
			$this->error('还原失败');
		}
	}

	//彻底删除
	public function clear() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->clearBatch();
			return;
		}

		if (M('special')->delete($id)) {
			// delete picture index
			
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'special'))->delete();
			
			$this->success('彻底删除成功', U('Special/trach'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除
	public function clearBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		if (M('special')->where($where)->delete()) {
			// delete picture index
			M('attachmentindex')->where(array('arcid' => array('in', $idArr), 'modelid' => 0, 'desc' => 'special'))->delete();
			$this->success('彻底删除成功', U('Special/trach'));
		}else {
			$this->error('彻底删除失败');
		}
	}

	
}



?>