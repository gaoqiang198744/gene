<?php
namespace Manage\Controller;
use Common\Lib\Category;

class ClearHtmlController extends CommonController {
	
	public function index() {

	}


	//一键更新静态缓存html
	public function all() {

		if (IS_POST) {
			del_cache_html('', true);
			$this->success('更新成功!', U('ClearHtml/all'));
			exit();
		}

		$this->assign('type', '一键更新|静态缓存');
		$this->display();
	}


	//更新首页静态缓存html
	public function home() {

		if (IS_POST) {
			del_cache_html('Index_index', false, 'index:index');
			$this->success('更新成功!', U('ClearHtml/home'));
			exit();
		}

		$this->assign('type', '更新首页|静态缓存');
		$this->display('all');
	}

	//更新栏目静态缓存html
	public function lists() {

		if (IS_POST) {
			$isall = I('get.isall', 0, 'intval');
			if ($isall) {
				del_cache_html('List', true, '');
			}else {
				$idArr = I('key', array(), '');
				$cate = M('category')->where(array('id' => array('IN', $idArr), 'type' => 0))->field(array('id', 'ename'))->select();
				foreach ($cate as $v) {
					//更新静态缓存
					del_cache_html('List/index_'.$v['id'].'_', false, 'list:index');
					del_cache_html('List/index_'.$v['ename'], false, 'list:index');//还有只有名称
				}

			}
			
			$this->success('更新成功!', U('ClearHtml/lists'));
			exit();
		}

		//$cate = get_category();
		$cate = D('CategoryView')->nofield('content')->where(array('category.type' => 0))->order('category.sort,category.id')->select();
		$cate = Category::toLevel($cate, '&nbsp;&nbsp;&nbsp;&nbsp;', 0);

		$this->assign('cate', $cate);
		$this->assign('type', '更新栏目|静态缓存');
		$this->display('all');
	}


	//更新内容页静态缓存html
	public function shows() {

		if (IS_POST) {
			$isall = I('get.isall', 0, 'intval');
			if ($isall) {
				del_cache_html('Show', true, '');
			}else {
				$idArr = I('key', array(), '');
				$cate = D('CategoryView')->where(array('category.id' => array('IN', $idArr), 'type' => 0))->field(array('id', 'ename', 'tablename'))->select();
				foreach ($cate as $v) {
					//更新静态缓存
					del_cache_html('Show/index_'.$v['id'].'_', false, 'show:index');
					del_cache_html('Show/index_'.$v['ename'], false, 'show:index');//还有只有名称
				}

			}
			
			$this->success('更新成功!', U('ClearHtml/shows'));
			exit();
		}

		//$cate = get_category();
		$cate = D('CategoryView')->where(array('category.type' => 0))->order('category.sort,category.id')->select();
		$cate = Category::toLevel($cate, '&nbsp;&nbsp;&nbsp;&nbsp;', 0);

		$this->assign('cate', $cate);
		$this->assign('type', '更新内容页(文档)|静态缓存');
		$this->display('all');
	}

	//更新专题静态缓存html
	public function special() {

		if (IS_POST) {
			$isall = I('get.isall', 0, 'intval');
			if ($isall) {
				del_cache_html('Special', true, '');
			}else {				
					del_cache_html('Special/index', false, 'special:index');
			}
			
			$this->success('更新成功!', U('ClearHtml/special'));
			exit();
		}

		$this->assign('type', '更新专题|静态缓存');
		$this->display('all');
	}




}


?>