<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<script language="javascript" type="text/javascript" src="/xyh/App/Manage/View/Public/js/jquery-1.7.2.min.js"></script>
<script src="/xyh/App/Manage/View/Public/js/frame.js" language="javascript" type="text/javascript"></script>
<link href="/xyh/App/Manage/View/Public/css/frame.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	function get_cate(){
		$.get("<?php echo U('Index/getParentCate');?>",
            {
                'pid' : 0, 
                'rnd' : Math.floor(111+Math.random()*100000)
            },
            function(data){
                var listUl = $('#dl_items_2_0 ul');
                if(!isNaN(data.count) && data.count>0){
                	listUl.text('');
                }
                if(data.list && (typeof data.list == 'object')){
                    $.each(data.list, function(i, v){
                        var html = '<li><a href="'+v.url+'" target="main">'+v.name+'</a></li>';
                        listUl.append(html);
                    });
                }
                               
            },'json');
	}
	get_cate();
</script>
<!--[if IE 6]>
<script src="/xyh/App/Manage/View/Public/js/DD_belatedPNG_0.0.8a-min.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('.nav ul li a,.top_link ul li,background');   /* string argument can be any CSS selector */
</script>
<![endif]-->
</head>
<body class="showmenu">
<div class="pagemask"></div>
<iframe class="iframemask"></iframe>
<div class="head">
<div class="top_logo"> <img src="/xyh/App/Manage/View/Public/images/main/logo.png" /> </div>
     <div class="nav" id="nav">
      <ul>
      	<?php if(is_array($menu)): foreach($menu as $k=>$v): ?><li id="menu_<?php echo ($k); ?>"><a <?php if(empty($k)): ?>class="thisclass"<?php endif; ?> href="#" _for="left_menu_<?php echo ($k); ?>"><b><?php echo ($v["name"]); ?></b></a></li><?php endforeach; endif; ?>
      </ul>
    </div>
	 <div class="top_link">
      <ul>
	    <li id="i_self">你好，<?php echo (session('yang_adm_username')); ?></li>
		<li id="i_hide_menu"><a href="#" id="togglemenu">隐藏菜单</a></li>
        <li id="i_home"><a href="<?php echo C('CFG_WEBURL');?>" target="_blank">首页</a></li> 
        <li id="i_help"><a href="<?php echo go_link('http://www.xyhcms.com/');?>" target="_blank">帮助</a></li>    
        <li id="i_exit"><a href="<?php echo U('Login/logout');?>" target="_top">退出</a></li>		
      </ul>
    </div>
</div>
<!-- header end -->

<div class="left">
<div class="span"></div>
<div class="menu" id="menu">
<?php if(is_array($menu)): foreach($menu as $k=>$v): ?><div id="items_left_menu_<?php echo ($k); ?>">
	<?php if(is_array($v["child"])): $k2 = 0; $__LIST__ = $v["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($k2 % 2 );++$k2;?><dl id="dl_items_<?php echo ($k2); ?>_<?php echo ($k); ?>">
		<dt><?php echo ($v2["name"]); ?></dt>
		<dd>
		<ul>

      <?php if($v2['id'] == 7): if(is_array($qmenu)): $k3 = 0; $__LIST__ = $qmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v3): $mod = ($k3 % 2 );++$k3;?><li><a href="<?php echo U($v3['module'].'/'.$v3['action'].'', $v3['parameter']);?>" target="main"><?php echo ($v3["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>  
      <?php else: ?>
      <?php if(is_array($v2["child"])): $k3 = 0; $__LIST__ = $v2["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v3): $mod = ($k3 % 2 );++$k3;?><li><a href="<?php echo U($v3['module'].'/'.$v3['action'].'', $v3['parameter']);?>" target="main"><?php echo ($v3["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
		</ul>
		</dd>
	</dl><?php endforeach; endif; else: echo "" ;endif; ?>	
	
</div><?php endforeach; endif; ?>
<!-- Item End -->


</div>
</div>
<!-- left end -->

<div class="right">
	<div class="rightContent">
	<iframe id="main" name="main" frameborder="0" scrolling="yes" src="<?php echo U('Public/main');?>" ></iframe>
	</div>    
</div>
<!-- right end -->

<div class="qucikmenu" id="qucikmenu">
  <ul>
<li><a href="content_list.htm" target="main">数据列表</a></li>
<li><a href="catalog_main.htm" target="main">栏目管理</a></li>
<li><a href="sys_info.htm" target="main">修改系统参数</a></li>
  </ul>
</div>
<!-- qucikmenu end -->
</body>
</html>