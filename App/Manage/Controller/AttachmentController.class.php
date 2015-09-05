<?php
namespace Manage\Controller;

class AttachmentController extends CommonController {
	
	public function index() {		
		
		$count = M('attachment')->count();
		$page = new \Common\Lib\Page($count, 10);
		$page->rollPage = 7;
		$page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('attachment')->order('id DESC')->limit($limit)->select();
		if (!$list) {
			$list = array();
		}
		

		//统计引用
		foreach ($list as $k => $v) {
			$list[$k]['num'] = M('attachmentindex')->where(array('attid' => $v['id']))->count();
		}

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('upload', get_url_path(get_cfg_value('CFG_UPLOAD_ROOTPATH')));
		$this->display();
	}


	//彻底删除文章
	public function del() {

		$id = I('id',0 , 'intval');
		$vo = M('attachment')->find($id);
		if (empty($vo)) {
			$this->error('不存在');
		}
		//$_SERVER['DOCUMENT_ROOT'];//有的虚拟主机不行		
		$path_upload = C('CFG_UPLOAD_ROOTPATH');
		// "/"开始,则转为绝对路径
		if (strpos($path_upload,"/") === 0) {
			$doc_path = str_ireplace(str_replace("\\","/",$_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_FILENAME']);
			$path_upload = $doc_path. $path_upload;
		}
		
		$list = glob($path_upload.$vo['filepath'].'*');
		
		if (!empty($list)) {
			foreach ($list as $v) {
				$ret = @unlink($v);
				if (!$ret) {
					$this->error('删除文件失败！文件：'.$v);
				}
			}
		}
	
		if (M('attachment')->delete($id)) {			
			M('attachmentindex')->where(array('attid' => $id))->delete();
			$this->success('删除成功', U('Attachment/index'));
		}else {
			$this->error('删除失败');
		}
	}







}



?>