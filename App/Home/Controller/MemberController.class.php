<?php
namespace Home\Controller;

class MemberController extends HomeCommonController {

	public function _initialize() {
		$uid = intval(get_cookie('uid'));
		if (empty($uid)) {
			$this->redirect(MODULE_NAME . '/Public/login');
		}
	}
	
	public function index() {

		$uid = get_cookie('uid');
		$user = D('MemberView')->nofield('password,encrypt')->find($uid);
		if (!$user) {
			$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
		}
                $check_lang=array('序列码已绑定','接收到样本','完成检测','完成分析','完成报告');
                $book_info=M('check')->where('member_id='.$uid)->select();
                //echo M('check')->getlastsql();
                foreach($book_info as $k=>$v){
                    $book_info[$k]['status_info']=$check_lang[$v['status']];
                    $sn=M('sn')->where('id='.$v['sid'])->find();
                    $book_info[$k]['sn']=$sn['sn'];
                    $book_info[$k]['product']=$sn['product'];   
                }
		$this->assign('book_info', $book_info);
                $this->assign('user', $user);
                $this->assign('mid', 3);
		$this->assign('title', '会员中心');
		$this->display();
	}

	public function name() {
		$uid = get_cookie('uid');
		$user = M('member')->find($uid);
		if (!$user) {
			$this->error('请重新登录',U(MODULE_NAME.'/Public/login'));
		}

		if (IS_POST) {
			$data['nickname'] = I('nickname', '', 'htmlspecialchars,trim');
			$data['id'] = $uid;
			if (empty($data['nickname'])) {
				$this->error('你还没有输入昵称！');
			}

			$notallowname = explode(',', C('CFG_MEMBER_NOTALLOW'));
			if (in_array($data['nickname'], $notallowname)) {
				$this->error('此昵称系统禁用，请重新更换一个！');
			}

			if (M('member')->save($data) !== false) {
				set_cookie( array('name' => 'nickname', 'value' => $data['nickname'], 'expire' => get_cookie('expire') ));
				$this->success('修改成功',U(MODULE_NAME. '/Member/index'));
			}else{
				$this->error('修改昵称失败！');
			}
			exit();
		}
		
		$this->assign('user', $user);
		$this->assign('title', '修改昵称');
		$this->display();
	}


	public function password() {
                $this->assign('mid', 3);
		$uid = get_cookie('uid');
		if (IS_POST) {
			$oldpassword = I('oldpassword', '');
			$password = I('password', '');
			$repassword = I('repassword', '');
			if (empty($oldpassword)) {
				$this->error('请填写旧密码！');
			}
			if (empty($password)) {
				$this->error('请填写新密码！');
			}

			if ($password != $repassword) {
				$this->error('两次密码不一样，请重新填写！');
			}
			
			$self = M('member')->field(array('email', 'password' ,'encrypt'))->where(array('id' => $uid))->find();
			if (!$self) {
				$this->error('用户不存在，请重新登录');
			}

			if (get_password($oldpassword, $self['encrypt']) != $self['password']) {
				$this->error('旧密码错误');
			}

			$passwordinfo = get_password($password);

			$data = array(
				'id'		=> $uid,
				'password'		=> $passwordinfo['password'],		
				'encrypt'		=> $passwordinfo['encrypt']
				);

			if (false !== M('member')->save($data)) {
				$this->success('修改密码成功', U(MODULE_NAME. '/Member/password'));
			}else {

				$this->error('修改密码失败');
			}
			exit();
		}

		$this->assign('title', '修改密码');
		$this->display();
	}

	public function avatar() {

		//$this->display();
	}


	public function person() {
		$uid = get_cookie('uid');
		if (IS_POST) {
			$data['realname'] = I('realname', '', 'htmlspecialchars,trim');
			$data['birthday'] = I('birthday', '0000-00-00');
			$data['sex'] = I('sex', 0, 'intval');			
			$data['address'] = I('address', '');
			$data['tel'] = I('tel', '');
			$data['mobile'] = I('mobile', '');
			$data['qq'] = I('qq', '');
			$data['maxim'] = I('maxim', '');

			$data['userid'] = $uid;
			$data['updatetime'] = time();
			$new = I('new', 0,'intval');
			if (empty($data['realname'])) {
				$this->error('请输入姓名！');
			}
			
			$result = true;
			if ($new) {
				$result = M('memberdetail')->add($data);
			}else {
				$result = M('memberdetail')->save($data);
			}
			
			if (false !== $result) {
				$this->success('修改基本资料成功', U(MODULE_NAME. '/Member/person'));
			}else {

				$this->error('修改基本资料失败');
			}
			exit();
		}

		$userdetail = M('memberdetail')->where(array('userid' => $uid))->find();
		if (!$userdetail) {
			$userdetail = array(
				'uid' => $uid,
				'email' => get_cookie('email'),
				'realname' => '',
				'sex' => 0,
				'birthday' => '1990-1-1',
				'address'	=> '',
				'tel'	=> '',
				'mobile'	=> '',
				'qq'	=> '',
				'maxim' => '',
			);
			$userdetail['new'] = 1;
		}else {
			$userdetail['new'] = 0;
			$userdetail['uid'] = $uid;
			$userdetail['email'] =  get_cookie('email');
		}
		$this->assign('vo', $userdetail);
		$this->assign('title', '基本资料');
		$this->display();
	}
        public function book() {
		$uid = get_cookie('uid');
		if (IS_POST) {
			$realname = I('realname', '');
			$telephone = I('telephone', '');
			$province = I('province', '');
                        $city = I('city', '');
                        $district = I('district', '0');
                        $address = I('address', '');
                        $book_time = I('book_time', '');
			if (empty($realname)) {
				$this->error('请填写姓名！');
			}
			if (empty($telephone)) {
				$this->error('请填写手机！');
			}
			if (empty($address)) {
				$this->error('请填写地址！');
			}
			if (empty($book_time)) {
				$this->error('请填写预约时间！');
			}
			$data = array(
				'member_id'		=> $uid,
                                'realname'		=> $realname,
				'telephone'		=> $telephone,		
				'province'		=> $province,
                                'city'		        => $city,
                                'district'		=> $district,
                                'address'		=> $address,
                                'book_time'		=> strtotime($book_time),
                                'create_time'           =>  time(),
                                'status'                => 0,
				);
                        //print_r($data);exit;
			if (false !== M('check')->add($data)) {
				$this->success('预约成功');
			}else {

				$this->error('预约失败');
			}
			exit();
		}

		$this->assign('title', '预约取样');
		$this->display();
	}
        public function bind(){
            if(IS_POST){
                $uid = get_cookie('uid');
                $sn=I('post.sn','');
                $password=I('post.password','');
                $sndata=M('sn')->where("sn='".$sn."'")->find();
                if($sndata){
                    if($sndata['password']==$password){
                        if($sndata['member_id']==0){
                            $add=array('member_id'=>$uid,'create_time'=>time(),'sid'=>$sndata['id'],'status'=>0);
                            $check=M('check')->add($add);
                            $save=array('use_time'=>time(),'member_id'=>$uid);
                            $sn_save=M('sn')->where("id='".$sndata['id']."'")->save($save);
                            if($check && $sn_save){
                                $data['status']=1;
                                $data['info']='绑定成功';
                            }else{
                                $data['status']=0;
                                $data['info']='绑定失败';                                
                            }
                        }else{
                            $data['status']=0;
                            $data['info']='序列码已使用'; 
                        }
                    }else{
                        $data['status']=0;
                        $data['info']='序列号和密码错误';
                    }
                    
                }else{
                    $data['status']=0;
                    $data['info']='序列号和密码错误';
                    
                }
                echo json_encode($data);
            }
        }


}

?>