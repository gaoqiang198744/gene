<?php
namespace Manage\Controller;
use Common\Lib\Category;

class IndexController extends CommonController{
	
	public function index(){
		$menu = M('menu')->where(array('status' => 1))->order('sort,id')->select();
		if (empty($menu)) {
			$menu = array();
		}		
		$qmenu = M('menu')->where(array('status' => 1, 'quick' => 1))->order('sort,id')->select();
		if (empty($qmenu)) {
			$qmenu = array();
		}
		$menu_c = $qmenu_c = array();

		//权限，是否开启验证且不是超级管理员
		if (C('USER_AUTH_ON') && empty($_SESSION[C('ADMIN_AUTH_KEY')])) {
            if(C('USER_AUTH_TYPE')==2) {
                //加强验证和即时验证模式
                $accessList = \Org\Util\Rbac::getAccessList(session(C('USER_AUTH_KEY')));
            }else {
                $accessList = $_SESSION['_ACCESS_LIST'];
            }

            foreach ($menu as $k => $v) {
            	if (empty($v['module']) || empty($v['action'])) {
            		$menu_c[] = $v;
            	} elseif (isset($accessList[strtoupper(MODULE_NAME)][strtoupper($v['module'])][strtoupper($v['action'])])) {
            		$menu_c[] = $v;
            	}
            }

            foreach ($qmenu as $k => $v) {
            	if (empty($v['module']) || empty($v['action'])) {
            		$qmenu_c[] = $v;
            	} elseif (isset($accessList[strtoupper(MODULE_NAME)][strtoupper($v['module'])][strtoupper($v['action'])])) {
            		$qmenu_c[] = $v;
            	}
            }
          
            
        }else{
            $menu_c = $menu;
            $qmenu_c = $qmenu;
			
		}

		$this->assign('menu', Category::toLayer($menu_c));
		$this->assign('qmenu', $qmenu_c);
		$this->display();
	}

	public function getParentCate(){
		header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
		$count = D('CategoryView')->where(array('pid' => 0 , 'type' => 0))->count();
		$list = D('CategoryView')->nofield('content')->where(array('pid' => 0 , 'type' => 0))->order('category.sort,category.id')->select();
		if (empty($list)) {
			$list = array();
		}

		//权限检测
		$checkflag = true;
		if (empty($_SESSION[C('ADMIN_AUTH_KEY')])) {
        	$checkaccess = M('categoryAccess')->distinct(true)->where(array('flag' => 1, 'roleid' => intval($_SESSION['yang_adm_roleid'])))->getField('catid', true);
                     
        }else {
        	$checkflag = false;
        }
		if(empty($checkaccess)) { 
			$checkaccess= array(); 
		}

		$menudoclist = array('count' => $count);
		foreach ($list as $v) {
			if (!$checkflag || in_array($v['id'], $checkaccess) ) {				
				$menudoclist['list'][] = array(
					'id' => $v['id'],				
					'name' => $v['name'],		
					'url' => U(ucfirst($v['tablename']) .'/index', array('pid'=>$v['id']))
				);	
			}
		}
		exit(json_encode($menudoclist));
	}


}


?>