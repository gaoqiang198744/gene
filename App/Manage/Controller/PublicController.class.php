<?php
namespace Manage\Controller;

class PublicController extends CommonController {

	
	public function index() {

	}

	//后台内容主页
	public function main() {
		/* phpversion */
		$this->assign('phpversion', phpversion());
		$this->assign('software', $_SERVER["SERVER_SOFTWARE"]);
		$this->assign('os', PHP_OS);

		$_mysql_ver = M()->query('SELECT VERSION() as ver;');

		if(is_array($_mysql_ver)) {
			$mysql_ver = $_mysql_ver[0]['ver'];
		}else {
			$mysql_ver = '';
		}
		$this->assign('mysql_ver', $mysql_ver);
		$this->assign('saeflag', defined('APP_SAE_FLAG') ? 1 : 0);
		
		/* uploads */
		$this->assign('environment_upload', ini_get('file_uploads') ? ini_get('upload_max_filesize') : '不支持');
		$this->assign('cms_info', rw_data('ver', '', './Data/resource/'));
		$this->display();
	}


	public function getFileOfImg() {

		header("Content-Type: text/html; charset=utf-8");

		$action = I('action', '', 'trim');
		if (IS_POST && $action != 'get') {
			exit();
		}


	    //需要遍历的目录列表，最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
	    //$paths = './uploads/img1';

        //显示有缩略图　文件
        $files = M('attachment')->where(array('filetype' => 1, 'haslitpic' => 1))->order('uploadtime DESC')->getField('filepath',50);//最新50条
       
        if ( !count($files) ) return;
        rsort($files,SORT_STRING);

        //读取缩略图配置信息
        $imgtbSize = explode(',', C('CFG_IMGTHUMB_SIZE'));//配置缩略图第一个参数
    	$imgTSize = explode('X', $imgtbSize[0]);

        $str = "";
        $sto_url = get_url_path(C('CFG_UPLOAD_ROOTPATH')); 
        foreach ( $files as $file ) {   
        	$file = $sto_url. $file;     	
            $str .= $file . "ue_separate_ue";
            
        }
        echo $str;

	}
	

	//上传图片
	public function upload() {
		header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
		$tb = I('get.tb', 0, 'intval'); //缩略图地址前缀/,1:_s,2:_m,0默认
		$editor = I('get.editor', 0, 'intval');//编辑器标志
		

		//百度编辑新版要求--start		
	    if (isset($_GET['fetch'])) {
			//获取存储目录--对应百度编辑器
	    	$imgSavePathConfig = array (
	       		'uploads',
	    	);

	        header( 'Content-Type: text/javascript' );
	        echo 'updateSavePath('. json_encode($imgSavePathConfig) .');';
	        return;

	    }
	    //百度编辑要求--end 
	
		 //文件上传地址提交给他，并且上传完成之后返回一个信息，让其写入数据库     
        if(empty($_FILES)){              
            echo json_encode(array(
						'url' => '', 'name' => '',	'original' => '',
		     			'state' => '必须选择上传文件'
		     	));
        }else{  
            $info = $this->_uploadPicture();//获取图片信息
          
            if(isset($info) && is_array($info)){  
                //写入数据库的自定义c方法  
				if(!$this->_uploadData($info)){  
                    //echo '上传入库失败'; 
                    echo json_encode(array(
						'url' => '',
		     			'name' => '',
		     			'original' => '',
		     			'state' => '上传入库失败'
		     			));
                    exit();
                }

                //读取缩略图配置信息
                $imgtbSize = explode(',', C('CFG_IMGTHUMB_SIZE'));//配置缩略图第一个参数
            	$imgTSize = explode('X', $imgtbSize[0]);				

                //数组索引转为数字
                $new_info = array();
                foreach ($info as $k => $v) {

                	$v['url'] = get_url_path(C('CFG_UPLOAD_ROOTPATH')). $v['savepath']. $v['savename'];
	                //返回缩略图地址//$tb == 2
                	if (!empty($imgTSize)) {
						$v['turl'] = get_picture($v['url'], $imgTSize[0], $imgTSize[1]);
					}
					$v['size'] 		= 	round($v['size']/1024,2);
					$v['state'] 	= 	'SUCCESS';

	                $new_info[] = $v;
                }
              	
              	//ueditor编辑内一次上传一个
              	if ($editor) {
              		echo json_encode(array('state' => 'SUCCESS', 'url' => $new_info[0]['url'], 
              			'fileType' => $new_info[0]['ext'],
              			'original' => $new_info[0]['name']
              			));
              	} else {
              		echo json_encode(array('state' => 'SUCCESS', 'info' => $new_info));
              	}
                
                


            }else{  
            //echo "{'url':'','title':'','original':'','state':'". $info ."'}";
           		echo json_encode(array(
				'url' => '',  'name' => '', 'original' => '',
     			'state' => '失败:'. $info 
     			));
   
            }  
        }  

	}


	//上传文件
	public function uploadFile() {
		header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
		
		$editor = I('get.editor', 0, 'intval');
		 //文件上传地址提交给他，并且上传完成之后返回一个信息，让其写入数据库     
        if(empty($_FILES)){  
            //$this->error('必须选择上传文件');              
            echo json_encode(array(
						'url' => '', 'title' => '',	'original' => '',
		     			'state' => '必须选择上传文件'
		     	));
        }else{  
        	$sfile = I('post.sfile', '', 'htmlspecialchars,trim');//判断其他子目录
            $info = empty($sfile)? $this->_uploadFile() : $this->_uploadFile($sfile);//获取附件信息
          
            if(isset($info) && is_array($info)){  
                //写入数据库的自定义c方法  
				if(!$this->_uploadData($info)){  
                    //echo '上传入库失败'; 
                    echo json_encode(array(
						'url' => '',
		     			'name' => '',
		     			'original' => '',
		     			'state' => '上传入库失败'
		     			));
                    exit();
                }

                //数组索引转为数字
                $new_info = array();
                foreach ($info as $k => $v) {

                	$v['url'] = get_url_path(C('CFG_UPLOAD_ROOTPATH')). $v['savepath']. $v['savename'];
					$v['size'] 		= 	round($v['size']/1024,2);
	                $new_info[] = $v;
                }
              
                
                //ueditor编辑内一次上传一个
              	if ($editor) {
              		echo json_encode(array('state' => 'SUCCESS', 'url' => $new_info[0]['url'], 
              			'fileType' => $new_info[0]['ext'],
              			'original' => $new_info[0]['name']
              			));
              	} else {
              		echo json_encode(array('state' => 'SUCCESS', 'info' => $new_info));
              	}


            }else{  
            	//echo "{'url':'','title':'','original':'','state':'". $info ."'}";
           		echo json_encode(array(
				'url' => '',  'name' => '', 'original' => '',
     			'state' => '失败:'. $info 
     			));
   
            }  



        }  

	}





	/**
	*图片(上传后)数组入库
	*filearr:图片数组
	**/	
	public function _uploadData($filearr) {
		if (!is_array($filearr)) {
			return false;
		}

		$db=M('attachment');  
		$num  = 0;
		

		foreach ($filearr as $k => $file) {
			
		    $data['filepath'] 	= 	$file['savepath'] .$file['savename'];  
		    $data['title'] 		= 	$file['name'];  
		    $data['haslitpic'] 	= 	empty($file['haslitpic']) ? 0 : 1;
		    $filetype =1;
		    //后缀
		    switch ($file['ext']) {
		       	case 'gif':
		       		$filetype =1;
		       		break;
		       	case 'jpg':
		       		$filetype =1;
		       		break;
		       	case 'png':
		       		$filetype =1;
		       		break;
		       	case 'bmp':
		       		$filetype =1;
		       		break;
		       	case 'swf'://flash
		       		$filetype =2;
		       		break;
		       	case 'mp3'://音乐
		       		$filetype =3;
		       		break;
		       	case 'wav':
		       		$filetype =3;
		       		break;
		       	case 'rm'://电影
		       		$filetype =4;
		       		break;

		       	case 'doc'://
		       		$filetype =5;
		       		break;
		       	case 'docx'://
		       		$filetype =5;
		       		break;
		       	case 'xls'://
		       		$filetype =5;
		       		break;
		       	case 'ppt'://
		       		$filetype =5;
		       		break;
		       	case 'zip'://
		       		$filetype =6;
		       		break;
		       	case 'rar'://
		       		$filetype =6;
		       		break;
		       	case '7z'://
		       		$filetype =6;
		       		break;
		       	
		       	default://其他
		       		$filetype = 0;
		       		break;
		       }   
		    $data['filetype'] 		= 	$filetype;
		    $data['filesize'] 		= 	$file['size'];
		    $data['uploadtime'] 	= 	time();
		    $data['aid'] 			= 	session(C('USER_AUTH_KEY'));//管理员ID
			if( $db->add($data))  
			{
				++$num;
			}  
		}  
		
		if($num==count($filearr))  
		{  
		    return true;     
		}else  
		{  
		    return false;  
		} 


	}

	//上传图片
	public function _uploadPicture() {
		$ext = '';//原文件后缀
		$ext_dest = 'jpg';//生成缩略图格式
		foreach ($_FILES as $key => $v) {
			$strtemp = explode('.', $v['name']);
			$ext = end($strtemp);//获取文件后缀，或$ext = end(explode('.', $_FILES['fileupload']['name']));
			break;
		}

		
		$upload = new \Think\Upload();//new Upload($config)
		//修配置项
		$upload->autoSub =true;//是否使用子目录保存图片
		$upload->subType = 'date';//子目录保存规则
		$upload->subName = array('date', 'Ymd');
		$upload->maxSize = get_upload_maxsize();//设置上传文件大小		
		$upload->exts = explode(',', C('CFG_UPLOAD_IMG_EXT'));//设置上传文件类型		
		$upload->rootPath = C('CFG_UPLOAD_ROOTPATH');//上传根路径		
		$upload->savePath ='img1/';//上传（子）目录
		$upload->saveName = array('uniqid', '');//上传文件命名规则
		$upload->replace = true; //存在同名是否覆盖
		$upload->callback = false; //检测文件是否存在回调函数，如果存在返回文件信息数组

		/*缩略图设置*/
		//设置需要生成缩略图,仅对图像文件有效
		//读取配置文件中的设置
		$imgtbSize = explode(',', C('CFG_IMGTHUMB_SIZE'));
		$imgtbArray = array();
		foreach ($imgtbSize as $v) {
			$t_size = explode('X', $v);

			if (empty($t_size) || empty($t_size[0]) || empty($t_size[1])) {
				continue;
			}
			$imgtbArray[] = array('w' => intval($t_size[0]), 'h' => intval($t_size[1]));
		}



		if($info = $upload->upload()) {
			//$info = current($info);	//第一张图片信息		

			//读取配置文件固定宽等比缩略
			$imgtbFixWidth = explode(',', C('CFG_IMGTHUMB_WIDTH'));
			$imgtbFixArray = array();
			foreach ($imgtbFixWidth as $v) {
				if (empty($v) || intval($v) == 0) {
					continue;
				}
				$imgtbFixArray[] = array('w' => intval($v), 'h' => intval($v * 100));
			}
		
			if (!empty($imgtbFixArray) || !empty($imgtbArray)) {
				//默认使用GD
				$think_img = new \Think\Image(); 
				$thumbType = C('CFG_IMGTHUMB_TYPE') ? 3:1;//配置大小

				foreach ($info as $k => $file) {
					$real_path = $upload->rootPath. $file['savepath']. $file['savename'];
					//$think_img->open($real_path);//$think_img->open($real_path)->thumb(xx,xx,xx)->save(xx,xx);

					//生成缩略图,固定大小
					foreach ($imgtbArray as $i => $v) {					
						$strSuffix = '!'.$v['w'].'X'.$v['h'];
						$think_img->open($real_path)->thumb($v['w'],$v['h'], $thumbType)->save($real_path.$strSuffix.'.'.$ext_dest,$ext_dest);

					}
					//生成缩略图，不放大，等宽，高度不限
					foreach ($imgtbFixArray as $v) {
						$strSuffix = '!'.$v['w'].'X';
						$think_img->open($real_path)->thumb($v['w'],$v['h'], 1)->save($real_path.$strSuffix.'.'.$ext_dest,$ext_dest);
					}

					$info[$k]['haslitpic'] = 1;//设置有缩略图
				}
				
			}
			return $info;		

		}else {
			
			//$str = array('err' =>1 ,'msg' => $upload->getError() );
			return $upload->getError();
		}


	}



	//上传文件
	public function _uploadFile($sfile = 'file1') {
		$ext = '';//原文件后缀
		foreach ($_FILES as $key => $v) {
			$strtemp = explode('.', $v['name']);
			$ext = end($strtemp);//获取文件后缀，或$ext = end(explode('.', $_FILES['fileupload']['name']));
			break;
		}


		$upload = new \Think\Upload();//new Upload($config)
		//修配置项
		$upload->autoSub =true;//是否使用子目录保存图片
		$upload->subType = 'date';//子目录保存规则
		$upload->subName = array('date', 'Ymd');
		$upload->maxSize = get_upload_maxsize();//设置上传文件大小		
		$upload->exts = explode(',', C('CFG_UPLOAD_FILE_EXT'));//设置上传文件类型		
		$upload->rootPath = C('CFG_UPLOAD_ROOTPATH');//上传根路径		
		$upload->savePath = rtrim($sfile, '/').'/';//上传（子）目录
		$upload->saveName = array('uniqid', '');//上传文件命名规则
		$upload->replace = true; //存在同名是否覆盖		
		$upload->callback = false; //检测文件是否存在回调函数，如果存在返回文件信息数组


		if($info = $upload->upload()) {
				
			return $info;		

		}else {
			
			return $upload->getError();
		}


	}


	//文件/夹管理
	function browseFile($spath = '', $stype = 'file') {
		$base_path = '/uploads/img1';
		$enocdeflag = I('encodeflag', 0, 'intval');
		switch ($stype) {
			case 'picture':
				$base_path = '/uploads/img1';
				break;
			case 'file':
				$base_path = '/uploads/file1';
				break;			
			case 'ad':
				$base_path = '/uploads/abc1';
				break;
			default:
				exit('参数错误');
				break;
		}

		if ($enocdeflag) {
			$spath = base64_decode($spath);
		}

		$spath = str_replace('.', '', $spath);
		if (strpos($spath, $base_path) === 0) {
			$spath = substr($spath,strlen($base_path));
		}
	
		$path = $base_path . '/'. $spath;
		$path = str_replace('//', '/', $path);

	
		$dir = new \Common\Lib\Dir('.'. $path);//加上.
		$list = $dir->toArray();
		for ($i=0; $i < count($list); $i++) { 
			
			$list[$i]['isImg'] = 0;
			if ($list[$i]['isFile']) {
				$url =  __ROOT__.rtrim($path,'/') . '/'. $list[$i]['filename'];
				$ext = explode('.', $list[$i]['filename']);
        		$ext = end($ext);
				if (in_array($ext, array('jpg','png','gif'))) {
					$list[$i]['isImg'] = 1;
				}
			}else {
				//为了兼容URL_MODEL(1、2)
				if (in_array(C('URL_MODEL'), array(1,2,3))) {
					$url = U('Public/browseFile', array('stype' => $stype, 'encodeflag' => 1 ,'spath'=>base64_encode(rtrim($path,'/') . '/'. $list[$i]['filename'])));
				}else {
					$url = U('Public/browseFile', array('stype' => $stype, 'spath'=> rtrim($path,'/') . '/'. $list[$i]['filename']));
				}
				
			}	
			$list[$i]['url'] = $url;			
			$list[$i]['size'] = get_byte($list[$i]['size']);
		}
		//p($list);
		$parentpath = substr($path, 0, strrpos($path, '/'));
		//为了兼容URL_MODEL(1、2)
		if (in_array(C('URL_MODEL'), array(1,2,3))) {
			$purl = U('Public/browseFile', array('spath'=> base64_encode($parentpath),'encodeflag' => 1, 'stype' => $stype));
		}else {
			$purl = U('Public/browseFile', array('spath'=> $parentpath, 'stype' => $stype));
		}
	
		$this->assign('purl', $purl);
		$this->assign('vlist', $list);
		$this->assign('stype', $stype);
		$this->assign('type', '浏览文件');
		$this->display();

	}


	public function editor($editorId = 'content', $width = '600', $height = '300', $type = 'ueditor') {
		$dataPath = __ROOT__. '/Data';
		$imageUrl = U('Public/upload',array('editor'=>1));
		$imageManagerUrl = U('Public/getFileOfImg');
		$fileUrl = U('Public/uploadFile',array('editor'=>1));
		$PHPSESSID = session_id();
		$lang = C('DEFAULT_LANG');
		$getEditor = '';
	
		if (!empty($editorId)) {
			$editorId = explode(',', $editorId);
			foreach ($editorId as $v) {
				if(!empty($v)) $getEditor .= 'UE.getEditor("'. $v .'");';
			}
		}
		$str = <<<str
	window.UEDITOR_HOME_URL = "$dataPath/ueditor/";
	window.onload = function() {
		window.UEDITOR_CONFIG.initialFrameWidth={$width};
		window.UEDITOR_CONFIG.initialFrameHeight={$height};
		//图片上传配置区
		window.UEDITOR_CONFIG.imageUrl = "{$imageUrl}" ;   //图片上传提交地址
		window.UEDITOR_CONFIG.imagePath = "";//图片修正地址，引用了fixedImagePath,如有特殊需求，可自行配置
		window.UEDITOR_CONFIG.imageManagerUrl = "{$imageManagerUrl}" ;////图片在线管理的处理地址
		window.UEDITOR_CONFIG.imageManagerPath = ""; //图片在线管理修正地址  

		//附件上传配置区
        window.UEDITOR_CONFIG.fileUrl = "{$fileUrl}"; //附件上传提交地址
        window.UEDITOR_CONFIG.filePath = ""; //附件修正地址，同imagePath
        //视频上传配置区
        window.UEDITOR_CONFIG.videoUrl = "{$fileUrl}";//附件上传提交地址
        window.UEDITOR_CONFIG.videoPath = "" ; //附件修正地址，同imagePath
        window.UEDITOR_CONFIG.PHPSESSID = "{$PHPSESSID}";

        //lang
        window.UEDITOR_CONFIG.lang = "{$lang}";
        
		{$getEditor}


	}

	document.write('<script type="text/javascript" src="$dataPath/ueditor/ueditor.config.js"></script>');
	document.write('<script type="text/javascript" src="$dataPath/ueditor/ueditor.all.min.js"></script>');

str;
	echo $str;
	}



}



?>