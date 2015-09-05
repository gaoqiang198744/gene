<?php
namespace Manage\Controller;
use Common\Lib\Category;

class PageController extends CommonContentController {
	
	//编辑
	public function index() {
		//当前控制器名称
		$actionName = strtolower(CONTROLLER_NAME);
		$pid = I('pid', 0, 'intval');
		if (IS_POST) {
			$this->indexPost();
			exit();
		}
		
		$vo = M('category')->find($pid);//直接是编辑	
		$vo['content'] = htmlspecialchars($vo['content']);//ueditor
	
	
		//所有子栏目列表
		$cate = get_category();//全部分类
		$subcate = Category::toLayer(Category::clearCate(Category::getChilds($cate, $pid),'type'),'child',$pid);//子类,多维
		$poscate = Category::getParents($cate, $pid);

		$this->assign('vo', $vo);
		$this->assign('pid', $pid);
		$this->assign('subcate', $subcate);
		$this->assign('poscate', $poscate);
		$this->display();
	}


	//修改文章处理
	public function indexPost() {

		$id = I('pid', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$content = I('content', '', '');		
		$description = I('description', '');

		if (!$pid) {
			$this->error('参数错误');
		}

		
		if (empty($description)) {			
			$description = str2sub(strip_tags($content), 120);
		}

		$data = array('id' => $pid, 'description' => $description, 'content' => $content);

		//获取属于分类信息,得到modelid			
		$selfCate = Category::getSelf(get_category(0), $id);//当前栏目信息
		$modelid = $selfCate['modelid'];

		if (false !== M('category')->save($data)) {
	
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => $modelid))->delete();
			$attid = get_att_content($content);//内容中的图片			
			insert_att_index($attid,$id,$modelid);//attachmentindex入库	

			get_category(0,1);//更新栏目缓存
			get_category(1,1);
			get_category(2,1);

			$this->success('修改成功', U('Page/index', array('pid' => $pid)));
		}else {

			$this->error('修改失败');
		}
		
	}




}



?>