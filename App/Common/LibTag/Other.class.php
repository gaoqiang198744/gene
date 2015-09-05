<?php
namespace Common\LibTag;
use Think\Template\TagLib;

//自定义标签库
class Other extends TagLib {
	
	//xxxtest测试数据，可删除
	protected $tags = array(
		//自定义标签
		'xxxtest'	=> array('close' => 0),	
	);
	

	public function _xxxtest($attr, $content) {
		return 'tag__xxxtest';
	}




}


?>