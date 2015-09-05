<?php
namespace Mobile\Controller;

class GuestbookController extends MobileCommonController {
	
	public function index() {
					
		//分页
		$count = M('guestbook')->count();

		$page = new \Common\Lib\Page($count, 10);		
		$page->rollPage = 3;
		$page->setConfig('theme',' %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('guestbook')->order('id DESC')->limit($limit)->select();

		$this->assign('page', $page->show());
		$this->assign('vlist', $list);
		$this->assign('title', '留言本');
		$this->display();
	}
	//添加

	public function add() {

		if (!IS_POST) {
			exit();
		}
		$content = I('content', '');
		$data =  I('post.');		
		$verify = I('vcode','','htmlspecialchars,trim');
		if (C('CFG_VERIFY_GUESTBOOK') == 1 && !check_verify($verify)) {
			$this->error('验证码不正确');
		}

		if (empty($data['username'])) {
			$this->error('姓名不能为空!');
		}
		if (empty($data['content'])) {
			$this->error('留言内容不能为空!');
		}
		if (check_badword($content)) {
			$this->error('留言内容包含非法信息，请认真填写!');
		}

	
		

		$data['posttime'] = time();
		$data['ip'] = get_client_ip();
	
		$db = M('guestbook');

		if($id = $db->add($data)) {
			$this->success('添加成功',U('Guestbook/index'));
		}else {
			$this->error('添加失败');
		}
	}

	

}



?>