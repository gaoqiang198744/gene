<?php
namespace Home\Controller;

class ResultController extends HomeCommonController {

	public function _initialize() {
		$uid = intval(get_cookie('uid'));
		if (empty($uid)) {
			$this->redirect(MODULE_NAME . '/Public/login');
		}
	}
	//基因检测结果
	public function index() {

		$uid = get_cookie('uid');
		$user = D('MemberView')->nofield('password,encrypt')->find($uid);
		if (!$user) {
			$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
		}              
                //查询该用户的疾病检测结果
                $sid=I('get.sid');
                $where='sid='.$sid;
                $result=M('result')->where($where)->select();
                $this->assign('result', $result);
                $this->assign('user', $user);
                $this->assign('mid', 3);
		
		$this->display();
	}
        	//基因检测结果详情
	public function detail() {

		$uid = get_cookie('uid');
		$user = D('MemberView')->nofield('password,encrypt')->find($uid);
		if (!$user) {
			$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
		}
                
                
                //查询该用户的疾病检测结果
                $dame=I('get.dname');
                $where='disease_name='.$dame;
                $result=M('disease')->where($where)->find();
                $this->assign('mid', 3);
                
                $this->assign('result', $result);
                
                
		
		$this->display();
	}
        //营养
        function yingyang(){
            $uid = get_cookie('uid');
            $user = D('MemberView')->nofield('password,encrypt')->find($uid);
            if (!$user) {
		$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
            }              
                //查询该用户的营养
            $sid=I('get.sid');
            $where='sid='.$sid;
            $yingyang=M('nutrition')->where($where)->order('id desc')->find();
            $person=M('person')->where($where)->order('id desc')->find();
            $this->assign('yingyang', $yingyang);
            $this->assign('person', $person);
            $this->assign('mid', 3);
            $this->display();
        }
        function sport(){
            $uid = get_cookie('uid');
            $user = D('MemberView')->nofield('password,encrypt')->find($uid);
            if (!$user) {
		$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
            }              
                //查询该用户的营养
            $sid=I('get.sid');
            $where='sid='.$sid;
            $sport=M('sport')->where($where)->order('id desc')->find();
            $person=M('person')->where($where)->order('id desc')->find();
            $this->assign('sport', $sport);
            $person['bmi']=$person['weight']/($person['height']*$person['height']/10000);
            $person['whr']=$person['waistline']/$person['hip'];
            $this->assign('person', $person);
            $this->assign('mid', 3);
            $this->display();
        }


	


}

?>