<?php
namespace Manage\Controller;
use Common\Lib\Category;

class SoftController extends CommonContentController {
	
	public function index() {

		$pid = I('pid', 0, 'intval');//类别ID
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字

		//所有子栏目列表
		$cate = get_category();
		$subcate = Category::toLayer(Category::clearCate(Category::getChilds($cate, $pid),'type'),'child',$pid);//子类,多维
		$poscate = Category::getParents($cate, $pid);
		
		
		if ($pid) {
			$idarr = Category::getChildsId($cate, $pid, 1);//所有子类ID
			//$where = array('soft.status' => 0, 'cid' => $pid);
			$where = array('soft.status' => 0, 'cid' => array('in', $idarr));
		}else {
			$where = array('soft.status' => 0);
		}
		
		if (!empty($keyword)) {
			$where['soft.title'] = array('LIKE', "%{$keyword}%");
		}
		
		
		
		$count = D2('ArcView','soft')->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D2('ArcView','soft')->nofield('content,pictureurls,updatelog,downlink')->where($where)->order('soft.id DESC')->limit($limit)->select();


		$this->assign('pid', $pid);
		$this->assign('subcate', $subcate);
		$this->assign('poscate', $poscate);
		$this->assign('keyword', $keyword);
		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('type', '软件下载列表');
		$this->display();
	}
	//添加
	public function add() {

		
		//当前控制器名称		
		$actionName = strtolower(CONTROLLER_NAME);
		$pid = I('pid', 0, 'intval');

		if (IS_POST) {
			$this->addPost();
			exit();
		}
		$cate = get_category(2);
		$cate = get_category_access(Category::getLevelOfModel(Category::toLevel($cate), $actionName),'add');
	
		$this->assign('pid', $pid);
		$this->assign('softtypelist', get_item('softtype'));
		$this->assign('softlanguage', get_item('softlanguage'));
		$this->assign('cate', $cate);
		$this->assign('flagtypelist', get_item('flagtype'));//文档属性
		$this->assign('type', '添加软件下载');
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
		$data['updatelog'] = str_replace("\n", '<br />', I('updatelog', '', ''));
		$data['os'] = implode(' ', I('os', array()));
		$data['downlink'] = str_replace("\n", '|||', I('downlink', ''));
		$data['softtype'] = I('softtype', 0, 'intval');
		$data['language'] = I('language', 0, 'intval');
		$data['publishtime'] = I('publishtime', time(),'strtotime');
		$data['updatetime'] = I('updatetime', time(), 'strtotime');
		$data['click'] = rand(10,95);
		$data['status'] = 0;
		$data['aid'] = session(C('USER_AUTH_KEY'));


		$pid = intval($data['pid']);
		$flags = I('flags', array(),'intval');
		$pic = $data['litpic'];

		

		if (empty($data['title'])) {
			$this->error('名称不能为空');
		}
		if (!$data['cid']) {
			$this->error('请选择栏目');
		}
		$pid = $data['cid'];//转到自己的栏目		

		if (empty($data['description'])) {			
			$data['description'] = str2sub(strip_tags($data['content']), 120);
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
		$selfCate = Category::getSelf(get_category(0), $data['cid']);//当前栏目信息
		$modelid = $selfCate['modelid'];


		if($id = M('soft')->add($data)) {

			$firstpic = '';
			$attid_arr = get_att_content($data['content'],$firstpic, empty($pic));//内容中的图片				

			//更新上传附件表
			if (!empty($pic)) {				
				$attid_arr = array_merge($attid_arr, get_att_attachment($pic,true));				
			}else if (!empty($firstpic)) {
				//更新表字段
                $updata = array('id' => $id, 'litpic' => $firstpic);
                if (!in_array(B_PIC, $flags)) {
					$updata['flag'] = array('exp','flag+'.B_PIC);
				}                
				M('soft')->save($updata);
			}

			//attachment index入库
			insert_att_index($attid_arr,$id,$modelid);

			
			//更新静态缓存
			del_cache_html('List/index_'.$data['cid'], false, 'list:index');	
			del_cache_html('Index_index', false, 'index:index');

			$this->success('添加成功',U('Soft/index', array('pid' => $pid)));
		}else {
			$this->error('添加失败');
		}
	}

	//编辑文章
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower(CONTROLLER_NAME);
		$pid = I('pid', 0, 'intval');

		if (IS_POST) {
			$this->editPost();
			exit();
		}

		$cate = get_category(2);
		$cate = get_category_access(Category::getLevelOfModel(Category::toLevel($cate), $actionName),'edit');

		$vo = M($actionName)->find($id);
		$vo['downlink'] = str_replace('|||', "\n", $vo['downlink']);		
		$vo['updatelog'] = htmlspecialchars(str_replace('<br />', "\n", $vo['updatelog']));
		$vo['content'] = htmlspecialchars($vo['content']);
		$vo['os'] = explode(' ', $vo['os']);

		$this->assign('pid', $pid);
		$this->assign('vo', $vo);
		$this->assign('softtypelist', get_item('softtype'));
		$this->assign('softlanguage', get_item('softlanguage'));
		$this->assign('cate', $cate);
		$this->assign('flagtypelist', get_item('flagtype'));//文档属性
		$this->assign('type', '修改软件下载');
		$this->display();
	}


	//修改文章处理
	public function editPost() {

		$data = I('post.');
		$id = $data['id'] = I('id', 0, 'intval');
		$data['cid'] = I('cid', 0, 'intval');		
		$data['title'] = I('title', '', 'htmlspecialchars,rtrim');
		$data['shorttitle'] = I('shorttitle', '', 'htmlspecialchars,rtrim');
		$data['keywords'] = trim($data['keywords']);
		$data['content'] = I('content', '', '');		
		$data['updatelog'] = str_replace("\n", '<br />', I('updatelog', '', ''));	
		$data['os'] = implode(' ', I('os', array()));
		$data['downlink'] = str_replace("\n", '|||', I('downlink',''));
		$data['softtype'] = I('softtype', 0, 'intval');
		$data['language'] = I('language', 0, 'intval');
		$data['publishtime'] = I('publishtime', time(),'strtotime');
		$data['updatetime'] = I('updatetime', time(), 'strtotime');


		$pid = intval($data['pid']);
		$flags = I('flags', array(),'intval');
		$pic = $data['litpic'];


		if (empty($data['title'])) {
			$this->error('标题不能为空');
		}
		if (!$data['cid']) {
			$this->error('请选择栏目');
		}		
		$pid = $data['cid'];//转到自己的栏目

		if (empty($data['description'])) {			
			$data['description'] = str2sub(strip_tags($data['content']), 120);
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
		$selfCate = Category::getSelf(get_category(0), $data['cid']);//当前栏目信息
		$modelid = $selfCate['modelid'];


		if (false !== M('soft')->save($data)) {
			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => $modelid))->delete();
			
			$firstpic = '';
			$attid_arr = get_att_content($data['content'],$firstpic, empty($pic));//内容中的图片				

			//更新上传附件表
			if (!empty($pic)) {				
				$attid_arr = array_merge($attid_arr, get_att_attachment($pic,true));				
			}else if (!empty($firstpic)) {
				//更新表字段
                $updata = array('id' => $id, 'litpic' => $firstpic);
                if (!in_array(B_PIC, $flags)) {
					$updata['flag'] = array('exp','flag+'.B_PIC);
				}                
				M('soft')->save($updata);
			}

			//attachment index入库
			insert_att_index($attid_arr,$id,$modelid);


			//更新静态缓存
			del_cache_html('List/index_'.$data['cid'].'_', false, 'list:index');
			del_cache_html('List/index_'.$selfCate['ename'], false, 'list:index');//还有只有名称
			del_cache_html('Show/index_*_'. $id, false, 'show:index');//不太精确，会删除其他模块同id文档		

			$this->success('修改成功', U('Soft/index', array('pid' => $pid)));
		}else {

			$this->error('修改失败');
		}
		
	}

	//移动
	public function move() {
		//当前控制器名称
		$id = I('key', 0);
		$actionName = strtolower(CONTROLLER_NAME);
		$pid = I('pid', 0, 'intval');

		if (IS_POST) {
			$id = I('id', 0);
			$cid = I('cid', 0, 'intval');
			if (empty($id)) {
				$this->error('请选择要移动的文档');
			}

			if (!$cid) {
				$this->error('请选择栏目');
			}

			if (false !== M($actionName)->where(array('id'=> array('in', $id)))->setField('cid',$cid)) {
				$this->success('移动成功', U('Soft/index', array('pid' => $pid)));
			}else {
				$this->error('移动失败');
			}			
			exit();
		}

		if (empty($id)) {
			$this->error('请选择要移动的文档');
		}
	
		$cate = get_category(2);
		$cate = get_category_access(Category::getLevelOfModel(Category::toLevel($cate), $actionName),'move');

		$this->assign('id', $id);
		$this->assign('pid', $pid);
		$this->assign('cate', $cate);
		$this->assign('type', '移动文档');
		$this->display();
	}


	//回收站文章列表
	public function trach() {
		
		$where = array('soft.status' => 1);
		$count = D2('ArcView','soft')->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D2('ArcView','soft')->nofield('content,pictureurls,updatelog,downlink')->where($where)->limit($limit)->select();

		$pid = I('pid', 0, 'intval');

		$this->assign('pid', $pid);
		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('subcate', '');
		$this->assign('type', '软件回收站');
		$this->display('index');
	}

	//删除文章到回收站
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}

		$pid = I('pid',0 , 'intval');//单纯的GET没问题

		if (false !== M('soft')->where(array('id' => $id))->setField('status', 1)) {

			del_cache_html('Show/index_*_'. $id.'.', false, 'show:index');	
			$this->success('删除成功', U('Soft/index', array('pid' => $pid)));
			
		}else {
			$this->error('删除失败');
		}
	}

	//批量删除到回收站
	public function delBatch() {

		$idArr = I('key',0 , 'intval');
		$pid = I('get.pid', 0, 'intval');

		if (!is_array($idArr)) {
			$this->error('请选择要删除的项');
		}

		if (false !== M('soft')->where(array('id' => array('in', $idArr)))->setField('status', 1)) {
			
			//更新静态缓存
			foreach ($idArr as $v) {
				del_cache_html('Show/index_*_'. $v.'.', false, 'show:index');	
			}
			$this->success('批量删除成功', U('Soft/index', array('pid' => $pid)));
			
		}else {
			$this->error('批量删除文失败');
		}
	}

	//还原文章
	public function restore() {
		
		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->restoreBatch();
			return;
		}

		$pid = I('get.pid', 0, 'intval');

		if (false !== M('soft')->where(array('id' => $id))->setField('status', 0)) {
			
			$this->success('还原成功', U('Soft/trach', array('pid' => $pid)));
			
		}else {
			$this->error('还原失败');
		}
	}

	//批量还原文章
	public function restoreBatch() {
		
		$idArr = I('key',0 , 'intval');
		$pid = I('get.pid', 0, 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要还原的项');
		}

		if (false !== M('soft')->where(array('id' => array('in', $idArr)))->setField('status', 0)) {
			
			$this->success('还原成功', U('Soft/trach', array('pid' => $pid)));
			
		}else {
			$this->error('还原失败');
		}
	}

	//彻底删除文章
	public function clear() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->clearBatch();
			return;
		}

		$pid = I('get.pid', 0, 'intval');
		$modelid = D2('ArcView','soft')->where(array('id' => $id))->getField('modelid');

		if (M('soft')->delete($id)) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => $id , 'modelid' => $modelid ))->delete();//test
			}
			$this->success('彻底删除成功', U('Soft/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除文章
	public function clearBatch() {

		$idArr = I('key',0 , 'intval');		
		$pid = I('get.pid', 0, 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		$modelid = D2('ArcView','soft')->where(array('id' => $idArr[0]))->getField('modelid');//

		if (M('soft')->where($where)->delete()) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => array('in', $idArr) , 'modelid' => $modelid ))->delete();
			}
			$this->success('彻底删除成功', U('Soft/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}

	
}



?>