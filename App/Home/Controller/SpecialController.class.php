<?php
namespace Home\Controller;
use Common\Lib\Category;

class SpecialController extends HomeCommonController{
	
	public function index(){
		
		$cid = I('cid', 0,'intval');

		$cate = get_category(1);
		$self = Category::getSelf($cate, $cid);//当前栏目信息	
		
		$this->assign('title', '专题首页');
		$this->display();

	}

	/*测试－用户模型*/
	public function lists(){
		
		$cid = I('cid', 0,'intval');

		$cate = get_category(1);
		$self = Category::getSelf($cate, $cid);//当前栏目信息

		$patterns = array('/'.C('TMPL_TEMPLATE_SUFFIX').'$/');
		$replacements = array('');
		$template_list = preg_replace($patterns, $replacements, $self['template_list']);
		
		if (empty($template_list)) {
			$this->error('模板不存在');
		}
	
		
		$this->assign('title', '专题首页');
		$this->display($template_list);

	}


	public function shows($id = 0){
		$id = I('id', 0, 'intval');
		if ($id == 0) {
			$this->error('参数错误');
		}

		$content = M('special')->find($id);
		if (!$content) {
			$this->error('专题不存在');
		}
		$cid = $content['cid'];

		$cate = get_category(1);
		$self = Category::getSelf($cate, $cid);//当前栏目信息
		
		if(empty($self)) {
			$self = array(
					'id' => 0,
					'name' => '',
					'ename' => '',
					'url' => '',
				);
		}

		$this->assign('cate', $self);

		$patterns = array('/'.C('TMPL_TEMPLATE_SUFFIX').'$/');
		$replacements = array('');
		$template_show = preg_replace($patterns, $replacements, $content['template']);

		/*测试
		$patterns = array('/^Show_/', '/.html$/');
		$replacements = array('', '');
		$template_show = preg_replace($patterns, $replacements, $content['template']);
		*/
		if (empty($template_show)) {
			$this->error('模板不存在');
		}

		
		$this->assign('title', $content['title']);
		$this->assign('keywords', $content['keywords']);
		$this->assign('description', $content['description']);
		$this->assign('commentflag', $content['commentflag']);//是否允许评论,debug,以后加上个全局评价 $content['commentflag'] && CFG_Comment
		$this->assign('content', $content);
		$this->assign('tablename', 'special');
		$this->assign('id', $id);
		$this->display($template_show);


	}


}

?>