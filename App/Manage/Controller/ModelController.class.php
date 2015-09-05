<?php
namespace Manage\Controller;

class ModelController extends CommonController {
	
	//列表
	public function index() {

		$list = M('model')->order('sort')->select();

		$this->assign('vlist', $list);
		$this->display();
	}

	public function add() {	
		if (IS_POST) {
			$this->addPost();
			exit();
		}	
		$styleListList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'List_*');
		$styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Show_*');

		$this->assign('styleListList', $styleListList);
		$this->assign('styleShowList', $styleShowList);
		$this->display();
	}

	public function addPost() {

		$data = I('post.');

		//M验证
		$validate = array(
			array('name','require','名称必须填写！'), 
			array('name','','模型名称已经存在！',0,'unique',1), 
			array('tablename','require','附加表名必须填写！'), 
			array('tablename','','附加表名已经存在！',0,'unique',1),
			array('template_list','require','请选择列表模板'), 
			array('template_show','require','请选择内容页模板'),  
		);
		$model = M('model');
		$result = $model->validate($validate)->create();
		if (!$result){
            $this->error($model->getError());
        }else{
            if ($model->add($data)) {
            	$this->success('添加成功',U('Model/index'));
            }else {
            	$this->error('添加失败');
            }
        }
	}


	//编辑
	public function edit() {
		if (IS_POST) {
			$this->editPost();
			exit();
		}
		$id = I('id', 0, 'intval');
		$data = M('model')->find($id);
		if (!$data) {
			$this->error('记录不存在');
		}
		
		$styleListList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'List_*');
		$styleShowList = get_file_folder_List('./Public/Home/' .C('CFG_THEMESTYLE') , 2, 'Show_*');

		$this->assign('vo', $data);
		$this->assign('styleListList', $styleListList);
		$this->assign('styleShowList', $styleShowList);
		$this->display();
	}



	//修改分类处理

	public function editPost() {

		$data = I('post.');
		$id = $data['id'] = I('id',0, 'intval');
		$data['name'] = trim($data['name']);
		$data['tablename'] = trim($data['tablename']);


		if (empty($data['name'])) {
			$this->error('模型名称不能为空！');
		}
		if (empty($data['template_list'])) {
			$this->error('请选择列表模板');
		}
		if (empty($data['template_show'])) {
			$this->error('请选择内容页模板');
		}

		if (M('model')->where(array('id' => array('neq', $id), array('tablename' => $data['tablename'] ,'name' => $data['name'], '_logic' => 'OR')))->find()) {
			$this->error('模型名称或附加表已经存在！');
		}

		if (false !== M('model')->save($data)) {
			$this->success('修改成功',U('Model/index'));
		}else {
			$this->error('修改失败');
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
			M('model')->save($data);		
		}

		$this->redirect('Model/index');
	}


	//彻底删除
	public function del() {

		$id = I('id',0 , 'intval');

		if (M('model')->delete($id)) {
			$this->success('删除成功', U('Model/index', array('id' => $id)));
		}else {
			$this->error('删除失败');
		}
	}


}



?>