<?php
namespace Manage\Controller;
use Common\Lib\Category;

class PictureController extends CommonContentController {
	
	public function index() {

		$pid = I('pid', 0, 'intval');//类别ID
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字

		//所有子栏目列表
		$cate = get_category();//全部分类
		$subcate = Category::toLayer(Category::clearCate(Category::getChilds($cate, $pid),'type'),'child',$pid);//子类,多维
		$poscate = Category::getParents($cate, $pid);

		
		if ($pid) {			
			$idarr = Category::getChildsId($cate, $pid, 1);//所有子类ID
			$where = array('picture.status' => 0, 'cid' => array('in', $idarr));
		}else {
			$where = array('picture.status' => 0);
		}
		
		if (!empty($keyword)) {
			$where['picture.title'] = array('LIKE', "%{$keyword}%");
		}		
		
		
		$count = D2('ArcView','picture')->where($where)->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D2('ArcView','picture')->nofield('content,pictureurls')->where($where)->order('picture.id DESC')->limit($limit)->select();

		$this->assign('pid', $pid);
		$this->assign('subcate', $subcate);
		$this->assign('poscate', $poscate);
		$this->assign('keyword', $keyword);
		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('type', '图片集列表');
		$this->display();
	}
	//添加文章
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
		$this->assign('cate', $cate);
		$this->assign('flagtypelist', get_item('flagtype'));
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

		$pid = intval($data['pid']);
		$flags = I('flags', array(),'intval');
	

		if (empty($data['title'])) {
			$this->error('图片名称不能为空');
		}
		if (!$data['cid']) {
			$this->error('请选择栏目');
		}
		$pid = $data['cid'];//转到自己的栏目

		if (empty($data['description'])) {			
			$data['description'] = str2sub(strip_tags($data['content']), 120);
		}

		
		//获取属于分类信息,得到modelid			
		$selfCate = Category::getSelf(get_category(0),$data['cid']);//当前栏目信息
		$modelid = $selfCate['modelid'];

		$pictureurls_arr  = array();

		$imgPostUrls = isset($data['pictureurls']) ? $data['pictureurls'] : '';
		if (is_array($imgPostUrls)) {
			foreach ($imgPostUrls as $k => $v) {
				$pictureurls_arr[] = $v.'$$$'.'$$$';
				//缩略图
				if ($k == 0) {
					$imgtbSize = explode(',', C('CFG_IMGTHUMB_SIZE'));//配置缩略图第一个参数
                	$imgTSize = explode('X', $imgtbSize[0]);
					if (!empty($imgTSize)) {
						$pic = get_picture($v, $imgTSize[0], $imgTSize[1]);
					}else {
						$pic = $v;
					}
				}
			}
		}
		$pictureurls = $data['pictureurls'] = join('|||',$pictureurls_arr);
		$data['litpic'] = isset($pic) ? $pic : '';

		//图片标志
		if (!empty($data['litpic']) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		
		$data['flag'] = 0;
		foreach ($flags as $v) {
			$data['flag'] += $v;
		}


		if($id = M('picture')->add($data)) {
			//更新图片集
		
			$attid_arr = get_att_content($data['content']);//内容中的图片
			$attid_arr = array_merge($attid_arr, get_att_attachment($imgPostUrls));//图片数组
			insert_att_index($attid_arr,$id,$modelid);//attachmentindex入库
				
			//更新静态缓存
			del_cache_html('List/index_'.$data['cid'], false, 'list:index');	
			del_cache_html('Index_index', false, 'index:index');

			$this->success('添加成功',U('Picture/index', array('pid' => $pid)));
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

		$pid = I('pid', 0, 'intval');
		$cate = get_category(2);
		$cate = Category::toLevel($cate);
		$cate = get_category_access(Category::getLevelOfModel($cate, $actionName),'edit');
		
		$vo = M($actionName)->find($id);
		
		$pictureurls = array();
		if (!empty($vo['pictureurls'])) {	
			$temparr = explode('|||', $vo['pictureurls']);		
			foreach ($temparr as $key => $v) {
				$temparr2 = explode('$$$', $v);
				$pictureurls[] = array('url' => ''.$temparr2[0], 'alt' => ''.$temparr2[1]);
			}
		}
		
		$vo['pictureurls'] = $pictureurls;
		$vo['content'] = htmlspecialchars($vo['content']);

		$this->assign('pid', $pid);
		$this->assign('cate', $cate);
		$this->assign('vo', $vo);
		$this->assign('flagtypelist', get_item('flagtype'));//文档属性
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
		$data['publishtime'] = I('publishtime', time(),'strtotime');
		$data['updatetime'] = time();
	
		$pid = I('pid', 0, 'intval');
		$flags = I('flags', array(),'intval');

		if (empty($data['title'])) {
			$this->error('产品名称不能为空');
		}
		if (!$data['cid']) {
			$this->error('请选择栏目');
		}
		
		$pid = $data['cid'];//转到自己的栏目

		if (empty($data['description'])) {			
			$data['description'] = str2sub(strip_tags($data['content']), 120);
		}



		//获取属于分类信息,得到modelid			
		$selfCate = Category::getSelf(get_category(0), $data['cid']);//当前栏目信息
		$modelid = $selfCate['modelid'];


		$pictureurls_arr  = array();
		$imgPostUrls = isset($data['pictureurls']) ? $data['pictureurls'] : '';
		if (is_array($imgPostUrls)) {
			foreach ($imgPostUrls as $k => $v) {
				$pictureurls_arr[] = $v.'$$$'.'$$$';//array('url'=> $v ,'alt'=> '');
				if ($k == 0) {
					$imgtbSize = explode(',', C('CFG_IMGTHUMB_SIZE'));//配置缩略图第一个参数
                	$imgTSize = explode('X', $imgtbSize[0]);
					if (!empty($imgTSize)) {
						$pic = get_picture($v, $imgTSize[0], $imgTSize[1]);
					}else {
						$pic = $v;
					}
				}
			}
		}

		$data['pictureurls'] = join('|||',$pictureurls_arr);	
		$data['litpic'] = isset($pic) ? $pic : '';


		//图片标志
		if (!empty($data['litpic']) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		$data['flag'] = 0;
		foreach ($flags as $v) {
			$data['flag'] += $v;
		}

		
		if (false !== M('picture')->save($data)) {	
			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => $modelid))->delete();
			
			$attid_arr = get_att_content($data['content']);//内容中的图片
			$attid_arr = array_merge($attid_arr, get_att_attachment($imgPostUrls));//图片数组
			insert_att_index($attid_arr,$id,$modelid);//attachmentindex入库

			//更新静态缓存
			del_cache_html('List/index_'.$data['cid'].'_', false, 'list:index');
			del_cache_html('List/index_'.$selfCate['ename'], false, 'list:index');//还有只有名称
			del_cache_html('Show/index_*_'. $id, false, 'show:index');//不太精确，会删除其他模块同id文档	

			$this->success('修改成功', U('Picture/index', array('pid' => $pid)));
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
				$this->success('移动成功', U('Picture/index', array('pid' => $pid)));
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

	//回收站
	public function trach() {
		
		$where = array('picture.status' => 1);
		$count = D2('ArcView','picture')->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D2('ArcView','picture')->nofield('content,pictureurls')->where($where)->order('picture.id DESC')->limit($limit)->select();
		$pid = I('pid', 0, 'intval');
	
		$this->assign('pid', $pid);
		$this->assign('page', $page->show());
		$this->assign('vlist', $art);
		$this->assign('subcate', '');
		$this->assign('type', '图片集回收站');
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

		if (false !== M('picture')->where(array('id' => $id))->setField('status', 1)) {

			del_cache_html('Show/index_*_'. $id.'.', false, 'show:index');	
			$this->success('删除成功', U('Picture/index', array('pid' => $pid)));
			
		}else {
			$this->error('删除失败');
		}
	}

	//批量删除到回收站
	public function delBatch() {

		$idArr = I('key', 0, 'intval');
		$pid = I('get.pid', 0, 'intval');

		if (!is_array($idArr)) {
			$this->error('请选择要删除的项');
		}

		if (false !== M('picture')->where(array('id' => array('in', $idArr)))->setField('status', 1)) {
			
			//更新静态缓存
			foreach ($idArr as $v) {
				del_cache_html('Show/index_*_'. $v.'.', false, 'show:index');	
			}
			$this->success('批量删除成功', U('Picture/index', array('pid' => $pid)));
			
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
		
		$pid = I('get.pid', 0, 'intval');

		if (false !== M('picture')->where(array('id' => $id))->setField('status', 0)) {
			
			$this->success('还原成功', U('Picture/trach', array('pid' => $pid)));
			
		}else {
			$this->error('还原失败');
		}
	}

	//批量还原
	public function restoreBatch() {
		
		$idArr = I('key',0 , 'intval');
		$pid = I('get.pid', 0, 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要还原的项');
		}

		if (false !== M('picture')->where(array('id' => array('in', $idArr)))->setField('status', 0)) {
			
			$this->success('还原成功', U('Picture/trach', array('pid' => $pid)));
			
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
		
		$pid = I('get.pid', 0, 'intval');
		$modelid = D2('ArcView','picture')->where(array('id' => $id))->getField('modelid');

		if (M('picture')->delete($id)) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => $id , 'modelid' => $modelid ))->delete();
			}
			
			$this->success('彻底删除成功', U('Picture/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除
	public function clearBatch() {

		$idArr = I('key',0 , 'intval');		
		$pid = I('get.pid', 0, 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		$modelid = D2('ArcView','picture')->where(array('id' => $idArr[0]))->getField('modelid');//for delete picture index,use

		if (M('picture')->where($where)->delete()) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => array('in', $idArr) , 'modelid' => $modelid ))->delete();
			}
			$this->success('彻底删除成功', U('Picture/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}

	


}



?>