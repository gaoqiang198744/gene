<?php
namespace Manage\Controller;

class SystemController extends CommonController {

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
		
		
		$count = M("config")->where($where)->count();

		$page = new \Common\Lib\Page($count, 10);		
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$vlist = M("config")->where($where)->order('sort,id DESC')->limit($limit)->select();

		$this->assign('groupid', $groupid);
		$this->assign('keyword', $keyword);
		$this->assign('page', $page->show());
		$this->assign('vlist', $vlist);
		$this->assign('configgroup', get_item('configgroup'));
		$this->display();
	}

	public function add() {		

		if (IS_POST) {
			$data = I('post.');
			$data['groupid'] = I('groupid', 0, 'intval');
			$data['typeid'] = I('typeid', 0, 'intval');
			$data['sort'] = I('sort', 0, 'intval');
		
			if (empty($data['name'])) {
				$this->error('请填写名称(标识)');
			}
			if (empty($data['title'])) {
				$this->error('请填写标题');
			}

			if(!preg_match('/^[a-zA-Z0-9_]+$/', $data['name'])) {
				$this->error('名称只能由字母、数字和"_"组成');
			}
			$data['name'] = strtoupper($data['name']);

			if (M('config')->where(array('name'=> $data['name']))->find()) {
				$this->error('配置项名称(标识)已经存在，请更换');
			}
			

			if (M('config')->add($data)) {
				$this->success('添加成功',U('index'));
			} else {
				$this->error('添加失败');
			}			
			
			exit();
		}
		
		$this->assign('configgroup', get_item('configgroup'));
		$this->assign('configtype', get_item('configtype'));
		$this->display();
	}

	public function edit() {		
		$id = I('id', 0, 'intval');
		if (IS_POST) {
			$data = I('post.');
			$id = $data['id'] = I('id', 0, 'intval');	
			$data['groupid'] = I('groupid', 0, 'intval');
			$data['typeid'] = I('typeid', 0, 'intval');
			$data['sort'] = I('sort', 0, 'intval');
			

			if (empty($data['name'])) {
				$this->error('请填写名称(标识)');
			}
			if (empty($data['title'])) {
				$this->error('请填写标题');
			}

			if(!preg_match('/^[a-zA-Z0-9_]+$/', $data['name'])) {
				$this->error('名称只能由字母、数字和"_"组成');
			}
			$data['name'] = strtoupper($data['name']);

			if (M('config')->where(array('name'=> $data['name'], 'id' => array('neq' , $id)))->find()) {
				$this->error('配置项名称(标识)已经存在，请更换');
			}

			if (false !== M('config')->save($data)) {
				$this->success('添加成功',U('index'));
			} else {
				$this->error('添加失败');
			}			
			
			exit();
		}
		$vo = M('config')->find($id);
		$vo['value'] = htmlspecialchars($vo['value']);//ueditor

		$this->assign('vo', $vo);
		$this->assign('configgroup', get_item('configgroup'));
		$this->assign('configtype', get_item('configtype'));
		$this->display();
	}

	//删除
	public function del() {

		$id = I('id',0 , 'intval');
		$groupid = I('groupid', 0, '');
		//批量删除
		if (empty($id)) {
			$this->error('参数错误!');
		}

		if (M('config')->delete($id)) {		
			$this->success('删除成功', U('index', array('groupid'=>$groupid)));
			
		}else {
			$this->error('删除失败');
		}
	}


	//批量更新排序
	public function sort() {
		$sortlist = I('sortlist', array(), 'intval');
		$groupid = I('groupid', 0, 'intval');
		foreach ($sortlist as $k => $v) {
			$data = array(
					'id' => $k,
					'sort' => $v,
				);
			M('config')->save($data);		
		}
		$this->redirect('System/index', array('groupid'=> $groupid));
	}

	

	public function site()	{
		if (IS_POST) {

			$data = I('config', array(),'trim');
			$data['CFG_IMGTHUMB_SIZE'] = strtoupper($data['CFG_IMGTHUMB_SIZE']);
			if (empty($data['CFG_IMGTHUMB_SIZE'])) {
				$this->error('缩略图组尺寸不能为空');
			}
			$data['CFG_IMGTHUMB_SIZE'] = str_replace(array('，','Ｘ'), array(',','X'), $data['CFG_IMGTHUMB_SIZE']);
			$data['CFG_IMGTHUMB_WIDTH'] = str_replace(array('，','Ｘ'), array(',','X'), $data['CFG_IMGTHUMB_WIDTH']);
			
			foreach ($data as $k => $v) {
				$ret = M('config')->where(array('name' => $k))->save(array('value'=> $v));
			}
			if($ret !== false) {				
				F('config/site', null);
				$this->success('修改成功',U('System/site'));

			}else {

				$this->error('修改失败！');
			}

			exit();
		}
		$vlist   =   M("config")->order('groupid,sort')->select();
        if(!$vlist) {
            $vlist = array();
        }
        $configgroup = get_item('configgroup');

        $glist = array();
        foreach ($configgroup as $k => $v) {
        	$glist[$k] = array();
        	foreach ($vlist as $k2 => $v2) {
        		if ($k == $v2['groupid']) {
        			$glist[$k][] = $v2;
        			//unset($vlist[$k2]);
        		}        		
        		
        	}
        }

		$this->assign('vlist', $glist);
		$this->assign('configgroup', $configgroup);
		$this->assign('groupnum', count($configgroup));
		$this->assign('configtype', get_item('configtype'));
		$this->display();

	}

	public function url() {
		if (IS_POST) {
			$data = I('config', '' ,'');

			if ($data['HOME_URL_MODEL'] == 0 || $data['HOME_URL_MODEL'] == 3) {
				$data['HOME_URL_ROUTER_ON'] = 0;
			}
			$data['HOME_HTML_CACHE_ON'] = isset($data['HOME_HTML_CACHE_ON']) ? $data['HOME_HTML_CACHE_ON'] : 0;
			$data['MOBILE_HTML_CACHE_ON'] = isset($data['MOBILE_HTML_CACHE_ON']) ? $data['MOBILE_HTML_CACHE_ON'] : 0;
			$data['HTML_CACHE_INDEX_ON'] = isset($data['HTML_CACHE_INDEX_ON']) ? $data['HTML_CACHE_INDEX_ON'] : 0;
			$data['HTML_CACHE_LIST_ON'] = isset($data['HTML_CACHE_LIST_ON']) ? $data['HTML_CACHE_LIST_ON'] : 0;
			$data['HTML_CACHE_SHOW_ON'] = isset($data['HTML_CACHE_SHOW_ON']) ? $data['HTML_CACHE_SHOW_ON'] : 0;
			$data['HTML_CACHE_SPECIAL_ON'] = isset($data['HTML_CACHE_SPECIAL_ON']) ? $data['HTML_CACHE_SPECIAL_ON'] : 0;

			
			
			foreach ($data as $k => $v) {
				$ret = M('meta')->where(array('name' => $k))->save(array('value'=> $v));
			}
			if($ret !== false) {
				F('config/meta', null);
				$this->success('修改成功',U('System/url'));

			}else {

				$this->error('修改失败！');
			}

			exit();
		}


		$list = M('meta')->where(array('groupid' => 1))->select();
		$vo = array();
		foreach ($list as $k => $v) {
			$vo[$v['name']] = $v['value'];
		}

		$this->assign('vo', $vo);
		$this->display();
	}


	public function online() {
		if (IS_POST) {
			$data = I('post.', '');
			//$data['cfg_online_qq'] = str_replace(array("\r","\n"), array("","|||"), $data['cfg_online_qq']);
			//$data['cfg_online_wangwang'] = str_replace(array("\r","\n"), array("","|||"), $data['cfg_online_wangwang']);
			//$data['cfg_online_qq_param'] = I('cfg_online_qq_param', '', '');//html
			//$data['cfg_online_wangwang_param'] = I('cfg_online_wangwang_param', '', '');//html

			$data = I('config', '' ,'');
			foreach ($data as $k => $v) {
				$ret = M('meta')->where(array('name' => $k))->save(array('value'=> $v));
			}
			if($ret !== false) {
				F('config/meta', null);
				$this->success('修改成功',U('System/online'));

			}else {

				$this->error('修改失败！');
			}

			exit();

		}

		$list = M('meta')->where(array('groupid' => 9))->select();
		$vo = array();
		foreach ($list as $k => $v) {
			$vo[$v['name']] = $v['value'];
		}
	
		$onlineStyleList = get_file_folder_List('./Data/static/js_plugins/online/', 2, '*.css');
		$onlineStyleList = str_replace('.css', '', $onlineStyleList);

		$this->assign('vo', $vo);
		$this->assign('onlineStyleList', $onlineStyleList);
		$this->display();
	}





	public function update() {
		header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
		//清除缓存
        $this->clearCache();
	}

	public function clearCache($dellog = false) {
		header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码

		//清除缓存
		is_dir(DATA_PATH . '_fields/') && del_dir_file(DATA_PATH . '_fields/', false);
		is_dir(CACHE_PATH) && del_dir_file(CACHE_PATH, false);//模板缓存（混编后的）
		echo ('<p>清除模板缓存成功!</p>');
		is_dir(DATA_PATH) && del_dir_file(DATA_PATH, false);//项目数据（当使用快速缓存函数F的时候，缓存的数据）
		echo ('<p>清除项目数据成功!</p>');
		is_dir(TEMP_PATH) && del_dir_file(TEMP_PATH, false);//项目缓存（当S方法缓存类型为File的时候，这里每个文件存放的就是缓存的数据）
		echo ('<p>清除项目项目缓存成功!</p>');
		if ($dellog) {
			is_dir(LOG_PATH) && del_dir_file(LOG_PATH, false);//日志
		}
		is_file(RUNTIME_PATH.APP_MODE.'~runtime.php') && @unlink(RUNTIME_PATH.APP_MODE.'~runtime.php');//RUNTIME_FILE

        echo '清除完成';
	}




}


?>