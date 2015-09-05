<?php
namespace Manage\Controller;
use Common\Lib\Category;

class CategoryController extends CommonController {
	
	//分类列表
	public function index() {

		//CategoryView 视图模型//$cate = get_category();
		$cate = D('CategoryView')->nofield('content')->order('category.sort,category.id')->select();		
		$cate = Category::toLevel($cate, '&nbsp;&nbsp;&nbsp;&nbsp;', 0);

		$this->assign('cate', $cate);
		$this->display();
	}

	//添加分类
	public function add() {
	
		if (IS_POST) {
			$this->addPost();
			exit();
		}
		$pid = I('pid', 0, 'intval');
		$cate = M('category')->order('sort')->select();
		$cate = Category::toLevel($cate, '---',0);
		$modelList = M('model')->where(array('status' => 1))->order('sort')->select();
		$groupList = M('membergroup')->order('rank')->select();
		$roleList = M('role')->order('id')->select();//管理员组
		$styleListList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'List_*');
		$styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Show_*');

		$this->assign('pid', $pid);
		$this->assign('cate', $cate);
		$this->assign('mlist', $modelList);
		$this->assign('groupList', $groupList);
		$this->assign('roleList', $roleList);
		$this->assign('styleListList', $styleListList);
		$this->assign('styleShowList', $styleShowList);
		$this->display();
	}

	//添加分类处理

	public function addPost() {

		$data = I('post.', '');
		$acc_groupid = I('acc_groupid', '');//会员组权限
		$acc_roleid = I('acc_roleid', '');//管理组权限

		
		$data['name'] = trim($data['name']);
		$data['ename'] = trim($data['ename']);		
		$data['type'] = empty($data['type'])? 0 : intval($data['type']);
		$pic = $data['catpic'] = I('catpic', '', 'htmlspecialchars,trim');

		if (isset($data['type']) && $data['type'] ==1 ) {
			$data['modelid'] = 0;
		}
		//M验证
		if (empty($data['name'])) {
			$this->error('栏目名称不能为空！');
		}


		if (empty($data['ename'])) {
			$data['ename'] = get_pinyin(iconv('utf-8','GBK//ignore',$data['name']),0,1,C('DEFAULT_LANG'));
		}elseif ($data['type'] == 0) {
			if (!ctype_alnum($data['ename'])) {
				$this->error('别名只能由字母和数字组成，不能包含特殊字符！');
			}
		}	
	

		if ($id = M('category')->add($data)) {
			//管理员组权限
			if (!empty($acc_roleid)) {
				$access = array();
				foreach ($acc_roleid as $v) {
					$tmp = explode(',', $v);
					$access[] = array(
							'catid' => $id,
							'roleid' => $tmp[1],
							'action' => $tmp[0],
							'flag' => 1,
							);
				}

				M('categoryAccess')->addAll($access);
			}

			//会员组权限
			if (!empty($acc_groupid)) {
				$access = array();
				foreach ($acc_groupid as $v) {
					$tmp = explode(',', $v);
					$access[] = array(
							'catid' => $id,
							'roleid' => $tmp[1],
							'action' => $tmp[0],
							'flag' => 0,
							);
				}
				M('categoryAccess')->addAll($access);
			}


			//更新上传附件表
			if (!empty($pic)) {

				$attid = get_att_attachment($pic,true);//内容中的图片			
				insert_att_index($attid,$id,0,'category');//attachmentindex入库
			}

			get_category(0,1);//清除栏目缓存
			get_category(1,1);//清除栏目缓存
			get_category(2,1);//清除栏目缓存
			$this->success('添加栏目成功<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U('Category/index'));
		}else {
			$this->error('添加栏目失败');
		}
		
	}


	//修改分类
	public function edit() {

		if (IS_POST) {
			$this->editPost();
			exit();
		}
		$id = I('id', 0, 'intval');
		$data = M('category')->find($id);
		if (!$data) {
			$this->error('记录不存在');
		}

		$cate = M('category')->order('sort')->select();
		$cate = Category::toLevel($cate, '---',0);
		$modelList = M('model')->where(array('status' => 1))->order('sort')->select();
		$groupList = M('membergroup')->order('rank')->select();
		$roleList = M('role')->order('id')->select();//管理员组
		$styleListList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'List_*');
		$styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Show_*');

		$this->assign('data', $data);
		$this->assign('cate', $cate);
		$this->assign('mlist', $modelList);
		$this->assign('groupList', $groupList);
		$this->assign('roleList', $roleList);
		$this->assign('styleListList', $styleListList);
		$this->assign('styleShowList', $styleShowList);		
		$this->display();
	}



	//修改分类处理

	public function editPost() {

		$data = I('post.', '');		
		$id = $data['id'] = intval($data['id']);
		$pid = $data['pid'];		
		
		$acc_groupid = I('acc_groupid', '');//会员组权限
		$acc_roleid = I('acc_roleid', '');//管理组权限
		
		$data['name'] = trim($data['name']);
		$data['ename'] = trim($data['ename']);		
		$data['type'] = empty($data['type'])? 0 : intval($data['type']);
		$pic = $data['catpic'] = I('catpic', '', 'htmlspecialchars,trim');

		if (isset($data['type']) && $data['type'] ==1 ) {
			$data['modelid'] = 0;
		}

		if ($id == $pid) {
			$this->error('失败！不能设置自己为自己的子栏目，请重新选择父级栏目');
		}
		//M验证
		if (empty($data['name'])) {
			$this->error('栏目名称不能为空！');
		}

		if (empty($data['ename'])) {
			$data['ename'] = get_pinyin(iconv('utf-8','GBK//ignore',$data['name']),0,1,C('DEFAULT_LANG'));
		}elseif ($data['type'] == 0) {
			if (!ctype_alnum($data['ename'])) {
				$this->error('别名只能由字母和数字组成，不能包含特殊字符！');
			}
		}
	

		/*
		if (M('category')->where(array('name' => $data['name'], 'id' => array('neq' , $id)))->find()) {
			$this->error('栏目名称已经存在！');
		}
		*/

		

		if (false !== M('category')->save($data)) {

			$msg = '';
			//判断oldmodelid 与 modelid是否不变
			if ($data['oldmodelid'] != $data['modelid']) {
				$tablename = M('model')->where(array('id' => $data['oldmodelid']))->getField('tablename');
				if (!empty($tablename) && $tablename != 'page') {
					M($tablename)->where(array('cid' => $id))->delete();
					$msg = '!!!';
				}
			}

			//清除权限
			M('categoryAccess')->where(array('catid' => $id))->delete();
			//管理员组权限
			if (!empty($acc_roleid)) {
				$access = array();
				foreach ($acc_roleid as $v) {
					$tmp = explode(',', $v);
					$access[] = array(
							'catid' => $id,
							'roleid' => $tmp[1],
							'action' => $tmp[0],
							'flag' => 1,
							);
				}

				M('categoryAccess')->addAll($access);
			}

			//会员组权限
			if (!empty($acc_groupid)) {
				$access = array();
				foreach ($acc_groupid as $v) {
					$tmp = explode(',', $v);
					$access[] = array(
							'catid' => $id,
							'roleid' => $tmp[1],
							'action' => $tmp[0],
							'flag' => 0,
							);
				}
				M('categoryAccess')->addAll($access);
			}

			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'category'))->delete();
			//更新上传附件表
			if (!empty($pic)) {
				$attid = get_att_attachment($pic,true);//内容中的图片			
				insert_att_index($attid,$id,0,'category');//attachmentindex入库
			}

			get_category(0,1);//清除栏目缓存
			get_category(1,1);
			get_category(2,1);
			$this->success('修改栏目成功'. $msg .'<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U('Category/index'));
		}else {
			$this->error('修改栏目失败');
		}
		
	}

	//批量更新排序
	public function sort() {
		$sortlist = I('sortlist', array(), 'intval');
		foreach ($sortlist as $k => $v) {
			$data = array(
					'id' => $k,
					'sort' => $v,
				);
			M('category')->save($data);		
		}
		$this->redirect('Category/index');
	}


	//修改分类处理

	public function del() {

		$id = I('id', 0, 'intval');

		//查询是否有子类
		$childnum = M('category')->where(array('pid' => $id))->count();
		if ($childnum) {
			$this->error('删除失败：请先删除本栏目下的子栏目');
		}
		$self = D('CategoryView')->field(array('modelid', 'tablename'))->where(array('category.id'=>$id))->find();
		if (!$self) {
			$this->error('栏目不存在');
		}
		$tablename = $self['tablename'];
		$modelid = $self['modelid'];

		if (M('category')->delete($id)) {
			$msg = '';
			if (!empty($tablename) && $tablename != 'page') {
				//删除栏目下文档之前，先删除文章资源引用
				$arcid = M($tablename)->where(array('cid' => $id))->getField('id', true);
				if (!empty($arcid)) {
					M('attachmentindex')->where(array('modelid' => $modelid, 'arcid' => array('IN', $arcid)))->delete();
					
					M($tablename)->where(array('cid' => $id))->delete();
				}		
				$msg = '!!!';
			}
			M('categoryAccess')->where(array('catid' => $id))->delete();
			
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => 0, 'desc' => 'category'))->delete();

			//更新栏目缓存
			get_category(0,1);
			get_category(1,1);
			get_category(2,1);
			$this->success('删除栏目成功'. $msg .'<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U('Category/index'));
		}else {
			$this->error('删除栏目失败');
		}		
	}


}




?>