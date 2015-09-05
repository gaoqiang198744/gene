<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="/xyh/App/Manage/View/Public/css/style.css" />
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/common.js"></script>
 <script language="JavaScript">
        <!--
        var URL = '/xyh/xyhai.php?s=/Abc';
        var APP	 = '/xyh/xyhai.php?s=';
        var SELF='/xyh/xyhai.php?s=/Abc/index';
        var PUBLIC='/xyh/App/Manage/View/Public';
        //-->
        </script>
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?>
    </div>
    <div class="operate">
        <div class="left">
            <input type="button" onclick="goUrl('<?php echo U('Abc/add');?>')" class="btn_blue" value="添加广告位">     
        </div>
    </div>
    <div class="list">    
    <form action="<?php echo U('Abc/delAll');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th>编号</th>
                <th>广告位</th>
                <th>类型</th>
                <th>尺寸</th>
                <th>广告数</th>                
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><?php echo ($v["id"]); ?></td>
                <td><a href="<?php echo U('Abc/detail',array('aid' => $v['id']), '');?>"><?php echo ($v["name"]); ?>  [<?php echo (str2sub($v["remark"],16)); ?>]</a></td>
                <td>
                    <?php switch($v['type']): case "1": ?>文本<?php break;?>
                        <?php case "2": ?>图片<?php break;?>
                        <?php case "3": ?>flash<?php break;?>
                        <?php default: ?>其他<?php endswitch;?>
                </td>                
                <td><?php echo ($v["width"]); ?>x<?php echo ($v["height"]); ?></td>
                <td><?php echo ($v["items"]); ?></td>
                <td>
                    <a href="<?php echo U('Abc/getcode',array('id' => $v['id']), '');?>">获取代码</a>
                    <a href="<?php echo U('Abc/detail',array('aid' => $v['id']), '');?>">广告列表</a>
                    <a href="<?php echo U('Abc/edit',array('id' => $v['id']), '');?>">编辑</a>
                    <a href="javascript:;" onclick="toConfirm('<?php echo U('Abc/del',array('id' => $v['id']), '');?>', '确实要删除吗？')">删除</a>
                    <a href="<?php echo U('Abc/addDetail',array('aid' => $v['id']), '');?>">添加广告</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="page" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>