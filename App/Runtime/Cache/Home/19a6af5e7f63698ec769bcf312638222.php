<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo ($title); ?>-我的网站</title>
<script type="text/javascript" src="/xyh/Public/Home/default/js/jquery-1.7.2.min.js" ></script>
<link href="/xyh/Public/Home/default/css/css.css" rel="stylesheet" type="text/css" />

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
</div>
<div class="clear"></div>

</div>

<div class="content">
	<div class="warp1 mt">
		<div class="left f_l">
		<h3 class="flbt">会员中心</h3>
		<div class="xbox">
			<ul class="fllb">
			<li><a href="<?php echo U('Member/index');?>">会员中心</a></li>
			<li><a href="<?php echo U('Member/name');?>">昵称修改</a></li>
			<li><a href="<?php echo U('Member/person');?>">基本资料</a></li>
			<li><a href="<?php echo U('Member/password');?>">修改密码</a></li>
			<li><a href="<?php echo U('Public/logout');?>">安全退出</a></li>
			</ul>
		</div>		
	</div>
<div class="right f_r">
<h3 class="nybt"><span>个人中心</span></h3>
<div class="form">
	<form method='post' id="form_do" name="form_do" action="<?php echo U('Member/person');?>">		
		<div class="h3">基本资料</div>
		<dl>
			<dt>邮箱：</dt>
			<dd><?php echo ($vo["email"]); ?></dd>
		</dl>
		<dl>
			<dt>姓名：</dt>
			<dd>
				<input type="text" name="realname" id="realname" value="<?php echo ($vo["realname"]); ?>" class="inp_one" />
			</dd>
		</dl>

		<dl>
			<dt>性别：</dt>
			<dd>
				<select name="sex">
					<option value="0" <?php if($vo["sex"] == 0): ?>selected="selected"<?php endif; ?>>&nbsp;男&nbsp;</option>
					<option value="1" <?php if($vo["sex"] == 1): ?>selected="selected"<?php endif; ?>>&nbsp;女&nbsp;</option>
				</select>
			</dd>
		</dl>

		<dl>
			<dt>出生年月：</dt>
			<dd>
				<input type="text" name="birthday" id="birthday" value="<?php echo ($vo["birthday"]); ?>" />
			</dd>
		</dl>

		<dl>
			<dt>联系地址：</dt>
			<dd>
				<input type="text" name="address" id="address" value="<?php echo ($vo["address"]); ?>" />
			</dd>
		</dl>

		<dl>
			<dt>联系电话：</dt>
			<dd>
				<input type="text" name="tel" id="tel" value="<?php echo ($vo["tel"]); ?>" />
			</dd>
		</dl>
		<dl>
			<dt>手机：</dt>
			<dd>
				<input type="text" name="mobile" id="mobile" value="<?php echo ($vo["mobile"]); ?>" />
			</dd>
		</dl>

		<dl>
			<dt>QQ：</dt>
			<dd>
				<input type="text" name="qq" id="qq" value="<?php echo ($vo["qq"]); ?>" />
			</dd>
		</dl>



		<div class="form_b">
			<input type="hidden" name='new' value="<?php echo ($vo["new"]); ?>"/>
			<input type="submit" class="btn_blue" id="submit" value="保存">		
		</div>
	</form>

	</div>

</div>
<div class=" clear"></div>
</div>
</div>

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