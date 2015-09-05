<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>我的网站</title>
<meta name="keywords" content="我的网站" />
<meta name="description" content="我的网站" />
<script type="text/javascript" src="/xyh/Public/Home/default/js/jquery-1.7.2.min.js" ></script>
<link rel="stylesheet" href="/xyh/Public/Home/default/js/FlexSlider/flexslider.css" type="text/css" />
<link href="/xyh/Public/Home/default/css/css.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="/xyh/Public/Home/default/js/FlexSlider/jquery.flexslider-min.js"></script>
<script language="javascript" type="text/javascript">
// Can also be used with $(document).ready()

$(window).load(function() {
  $('.flexslider').flexslider({
    animation: "slide",
	itemMargin: 0//,
	//controlNav: false
  });
});
</script>
<?php
 $_flag = 0; switch ($_flag) { case 0: if (C('CFG_MOBILE_AUTO') == 1) { if (C('HTML_CACHE_ON') == true) { echo '<script type="text/javascript" src="/xyh/Data/static/js/mobile_auto.js"></script>'; } else { go_mobile(); } } break; case 1: go_mobile(); break; case 2: if (C('CFG_MOBILE_AUTO') == 1) { echo '<script type="text/javascript" src="/xyh/Data/static/js/mobile_auto.js"></script>'; } break; default: break; } ?>
</head>
<body>
	
<!--top -->
<div id="top">
<!--[if IE 6]>
<script src="/xyh/Public/Home/default/js/DD_belatedPNG_0.0.8a-min.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('#top_logo');   /* string argument can be any CSS selector */
</script>
<![endif]-->
<script type="text/javascript">
$(function(){
	var $chkurl = "<?php echo U('Public/loginChk');?>";
	$.get($chkurl,function(data){
		//alert(data);
		if (data.status == 1) {
			$('#top_login_ok').show();
			$('#top_login_no').hide();
			//$('#top_login_ok').find('span');
			$('#top_login_ok>span').html('欢迎您，'+data.nickname);
		}else {			
			$('#top_login_ok').hide();
			$('#top_login_no').show();
		}
	},'json');	
});
</script>
<div class="warp" id="herd">
	<div id="top_fla">
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="998" height="159">
	  <param name="movie" value="/xyh/Public/Home/default/images/top.swf" />
	  <param name="quality" value="high" />
	  <PARAM NAME=wmode value="transparent">
	  <embed src="/xyh/Public/Home/default/images/top.swf" quality="high" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="998" height="159"></embed>
	</object>
	</div>
	<div id="top_member">
		<!--<a href="<?php echo U(MODULE_NAME.'/Product/basket');?>">购物车</a>-->
		<div id="top_login_no">
		<a href="<?php echo U(MODULE_NAME.'/Public/register');?>">会员注册</a>	
		<a href="<?php echo U(MODULE_NAME.'/Public/login');?>">会员登录</a>	
		<span>欢迎您，游客！您可以选择</span>	
		</div>
		<div id="top_login_ok" style="display:none;">
		<a href="<?php echo U(MODULE_NAME.'/Member/index');?>">会员中心</a>	
		<a href="<?php echo U(MODULE_NAME.'/Public/logout');?>">安全退出</a>
		<span>欢迎您， </span>
		</div>
			
	</div>
	<div id="top_logo"><a href="http://localhost/xyh"></a></div>
</div>
<!--menu -->
<div id="menu">
	<ul>
		<li><a href="http://localhost/xyh">首 页</a></li>
		<?php
 $_typeid = 0; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $_navlist = get_category(1); if($_typeid == 0) { $_navlist = Common\Lib\Category::toLayer($_navlist); }else { $_navlist = Common\Lib\Category::toLayer($_navlist, 'child', $_typeid); } foreach($_navlist as $autoindex => $navlist): $navlist['url'] = get_url($navlist); ?><li><a href='<?php echo ($navlist["url"]); ?>'><?php echo ($navlist["name"]); ?></a></li><?php endforeach;?>
	</ul>
</div>
<div class="warp1 mt">
	<div id="ggao"><b>最新公告：</b><span><marquee><?php
 $where = array('endtime' => array('gt',time())); if (0 > 0) { $count = M('announce')->where($where)->count(); $thisPage = new \Common\Lib\Page($count, 0); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "1"; } $_announcelist = M('announce')->where($where)->order("starttime DESC")->limit($limit)->select(); if (empty($_announcelist)) { $_announcelist = array(); } foreach($_announcelist as $autoindex => $announcelist): if(0) $announcelist['title'] = str2sub($announcelist['title'], 0, 0); if(100) $announcelist['content'] = str2sub(strip_tags($announcelist['content']), 100, 0); echo ($announcelist["content"]); endforeach;?></marquee></span></div>

	<div id="syxw" class="f_l">
	<?php
 $block = get_block("Introduction"); $block_content = ''; if ($block) { if ($block['blocktype'] == 2) { if (!0) { $block_content = '<img src="'. $block['content'] .'" />'; }else { $block_content = $block['content']; } }else { if(0) { $block_content = str2sub(strip_tags($block['content']), 0, 0); }else { $block_content = $block['content']; } } } echo $block_content; ?>
	</div>
	<div id="banner" class="f_r">
		<div class="flexslider">
			<ul class="slides">
				<?php
 $_id = intval(1); if (!empty($_id)) { if (0) { echo '<script type="text/javascript" src="'.U('Abc/shows', array('id' => $_id, 'flag' => 0)).'"></script>'; }else { echo get_abc($_id, 0); } } ?>
			</ul>
		</div>
	</div>
</div>
</div>

<div class="warp1 mt">
<div class="left f_l">
<h3 class="left_bt">搜索中心</h3>
<div class="xbox">
	<form id="SearchForm" name="SearchForm" method="post" action="/xyh/index.php?s=/Search/index.html">
	<ul class="searchFormDiv">
	<li>
	  <select name="modelid">
	  	<?php
 $_table = explode('|', "model"); $_field = explode('|', "id,name"); $_joinwhere = array_filter(explode('|', "")); sort($_joinwhere); $_jointype = 'INNER'; $where = "id != 2"; if (empty($where)) { $where = ' 1 = 1'; } $_field_array = array(); foreach ($_table as $k => $v) { if (strtolower($v) == 'admin') { $_table = array(); break; } $_field_temp = empty($_field[$k])? array('*') : explode(',', $_field[$k]); foreach ($_field_temp as $k2 => $v2) { $v2 = trim($v2); $_field_temp[$k2] = strpos($v2, '(')? $v2 : $v. '.'. $v2; } $_field_array = array_merge($_field_array, $_field_temp); $_table[$k] = C('DB_PREFIX').$v.' '.$v; } if (!empty($_table)) { $_field_str = implode(',', $_field_array); if (!empty($_joinwhere)) { foreach ($_joinwhere as $k => $v) { $_temp = explode(':', $v); if (isset($_temp[1]) && in_array(strtoupper($_temp[1]), array('INNER','LEFT','RIGHT'))) { $_jointype = strtoupper($_temp[1]); } $_jointype .= ' JOIN'; $_joinwhere[$k] = $_jointype.' '.$_table[$k+1].' ON '.$_temp[0]; } } if (0 > 0) { if (count($_table) == 1) { $count = M()->table($_table[0])->where($where)->count(); }else { $count = M()->table($_table[0])->join($_joinwhere)->where($where)->count(); } $thisPage = new \Common\Lib\Page($count, 0); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "30"; } if (count($_table) == 1) { $_datatable = M()->table($_table[0])->field($_field_str)->where($where)->order("id")->limit($limit)->select(); }else { $_datatable = M()->table($_table[0])->field($_field_str)->join($_joinwhere)-> where($where)->order("id")->limit($limit)->select(); } } if (empty($_datatable)) { $_datatable = array(); } foreach($_datatable as $autoindex => $datatable): ?><option value="<?php echo ($datatable["id"]); ?>"><?php echo (str_replace('模型','',$datatable["name"])); ?></option><?php endforeach;?>
	   </select>
	</li>
	<li>
	  <input name="keyword" type="text" id="keyword"  value="请输入关键词" onfocus="if(this.value=='请输入关键词'){this.value='';}" onblur="if(this.value==''){this.value='请输入关键词';}" />
	</li>
	<li>
	  <input type="submit" value="查询" class="btn_blue"/></li>
	</ul>
    </form>
	</div>

<div class="mt">
	<h3 class="left_bt">最新文章</h3>
	<div class="xbox left_box" id="abt">
	<ul class="sywz">
	
	<?php
 $_typeid = -1; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { $ids = Common\Lib\Category::getChildsId(get_category(), $_typeid, true); $where = array('article.status' => 0, 'article.cid'=> array('IN',$ids)); }else { $where = array('article.status' => 0); } if ($_keyword != '') { $where['article.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['article.id'] = array('IN', $_arcid); } if (0 > 0) { $where['_string'] = 'article.flag & 0 = 0 '; } if (0 > 0) { $count = D2('ArcView','article')->where($where)->count(); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $param['p'] = I('p', 1, 'intval'); $param_action = '/'.$ename; }else { $param = array(); $param_action = ''; } $thisPage = new \Common\Lib\Page($count, 0, $param, $param_action); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "8"; } $_artlist = D2('ArcView','article')->nofield('content')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_artlist)) { $_artlist = array(); } foreach($_artlist as $autoindex => $artlist): $_jumpflag = ($artlist['flag'] & B_JUMP) == B_JUMP? true : false; $artlist['url'] = get_content_url($artlist['id'], $artlist['cid'], $artlist['ename'], $_jumpflag, $artlist['jumpurl']); if(16) $artlist['title'] = str2sub($artlist['title'], 16, 0); if(0) $artlist['description'] = str2sub($artlist['description'], 0, 0); ?><li><a href="<?php echo ($artlist["url"]); ?>"><?php echo ($artlist["title"]); ?></a></li><?php endforeach;?>
	</ul>
	</div>
</div>	

</div>

<div class="right f_r">

<?php
 $_typeid = intval(0); $_type = "son"; $_temp = explode(',', "10"); $_temp[0] = $_temp[0] > 0? $_temp[0] : 10; if (isset($_temp[1]) && intval($_temp[1]) > 0) { $_limit[0] = $_temp[0]; $_limit[1] = intval($_temp[1]); }else { $_limit[0] = 0; $_limit[1] = $_temp[0]; } if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = get_category(1); if (0) { $__catlist = Common\Lib\Category::getLevelOfModelId($__catlist, 0); } if (0 == 0) { $__catlist = Common\Lib\Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $_type == 'top') { $_catlist = Common\Lib\Category::toLayer($__catlist); }else { if ($_type == 'self') { $_typeinfo = Common\Lib\Category::getSelf($__catlist, $_typeid ); $_catlist = Common\Lib\Category::toLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Common\Lib\Category::toLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex < $_limit[0]) continue; if($autoindex >= ($_limit[1]+$_limit[0])) break; $catlist['url'] = get_url($catlist); ?><div class="rbox <?php if($autoindex%2 == 0): ?>f_l <?php else: ?> f_r<?php endif; ?> <?php if($autoindex > 1): ?>mt<?php endif; ?>">
<h3 class="r_bt"><a href="<?php echo ($catlist["url"]); ?>">更多></a><span><?php echo ($catlist["name"]); ?></span></h3>
<div class="xbox" style="height:185px; overflow:hidden;">
	<ul class="sywz">
		<?php
 $_typeid = $catlist['id']; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { $_selfcate = Common\Lib\Category::getSelf(get_category(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Common\Lib\Category::getChildsId(get_category(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { $count = D2('ArcView',"$_tablename")->where($where)->count(); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $param['p'] = I('p', 1, 'intval'); $param_action = '/'.$ename; }else { $param = array(); $param_action = ''; } $thisPage = new \Common\Lib\Page($count, 0, $param, $param_action); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "10"; } $_list = D2('ArcView',"$_tablename")->nofield('content,pictureurls')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = get_content_url($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(0) $list['title'] = str2sub($list['title'], 0, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><span><?php echo (date('Y-m-d',$list["publishtime"])); ?></span><a href="<?php echo ($list["url"]); ?>" >[<?php echo ($list["catename"]); ?>]<?php echo ($list["title"]); ?></a></li><?php endforeach;?>
	</ul>
</div>
</div><?php endforeach;?>
<div class=" clear"></div>
</div>
<div class=" clear"></div>
</div>

<div class="warp1 mt">
<h3 class="r_bt"><span>产品展示</span></h3>
<div class="xbox" style="height:142px; overflow:hidden;">
<div id="demc">
    <div class="jdimg" id="my2Box">
      <ul id="my2Contnet">
		<?php
 $_typeid = -1; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { $ids = Common\Lib\Category::getChildsId(get_category(), $_typeid, true); $where = array('product.status' => 0, 'product.cid'=> array('IN',$ids)); }else { $where = array('product.status' => 0); } if ($_keyword != '') { $where['product.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['product.id'] = array('IN', $_arcid); } if (0 > 0) { $where['_string'] = 'product.flag & 0 = 0 '; } if (0 > 0) { $count = D2('ArcView','product')->where($where)->count(); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $param['p'] = I('p', 1, 'intval'); $param_action = '/'.$ename; }else { $param = array(); $param_action = ''; } $thisPage = new \Common\Lib\Page($count, 0, $param, $param_action); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "10"; } $_prolist = D2('ArcView','product')->nofield('content,pictureurls')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_prolist)) { $_prolist = array(); } foreach($_prolist as $autoindex => $prolist): $_jumpflag = ($prolist['flag'] & B_JUMP) == B_JUMP? true : false; $prolist['url'] = get_content_url($prolist['id'], $prolist['cid'], $prolist['ename'], $_jumpflag, $prolist['jumpurl']); if(0) $prolist['title'] = str2sub($prolist['title'], 0, 0); if(0) $prolist['description'] = str2sub($prolist['description'], 0, 0); ?><li><a href="<?php echo ($prolist["url"]); ?>"><img src="<?php echo ($prolist["litpic"]); ?>" alt="<?php echo ($prolist["title"]); ?>"/><span><?php echo ($prolist["title"]); ?></span></a></li><?php endforeach;?>	  
      </ul>
      <div class="clear"></div>

    </div>
  </div>
</div>
</div>

<div class="warp1 mt">
<h3 class="r_bt"><span>友情链接</span></h3>
<div class="xbox" id="yqlj">
<?php
 $_typeid = 0; if ($_typeid>0 || substr($_typeid,0,1) == '$') { $where = array('ischeck'=> $_typeid); }else { $where = array('id' => array('gt',0)); } if (0 > 0) { $count = M('link')->where($where)->count(); $thisPage = new \Common\Lib\Page($count, 0); $thisPage->rollPage = 5; $thisPage->setConfig('theme'," %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%"); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "20"; } $_flink = M('link')->where($where)->order("sort ASC")->limit($limit)->select(); if (empty($_flink)) { $_flink = array(); } foreach($_flink as $autoindex => $flink): ?><a href="<?php echo ($flink["url"]); ?>" target="_blank"><?php echo ($flink["name"]); ?></a><?php endforeach;?>
<div class="clear"></div>
</div>
</div>
<script language="javascript" src="/xyh/Public/Home/default/js/MSClass.js"></script>
<script type="text/javascript">
new Marquee(["my2Box","my2Contnet"],2,1,966,140,30,0,0);
</script>


<div class="warp1 mt" id="bottom">
	<a href="http://localhost/xyh">我的网站</a>版权所有
	<br />
	联系地址：昆明北京路  电话：0871-66666<br />
	Copyright © 2014-2014 XYHCMS. 行云海软件 版权所有 <a href="http://www.0871k.com" target="_blank">Power by XYHCMS</a>
</div>
<?php
 echo '<script type="text/javascript" src="'.U(MODULE_NAME. '/Public/online').'"></script>'; ?>

</body>
</html>