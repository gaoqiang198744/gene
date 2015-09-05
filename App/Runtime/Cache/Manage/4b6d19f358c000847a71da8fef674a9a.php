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
        var URL = '/xyh/xyhai.php?s=/Itemgroup';
        var APP	 = '/xyh/xyhai.php?s=';
        var SELF='/xyh/xyhai.php?s=/Itemgroup/index';
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
            <input type="button" onclick="goUrl('<?php echo U('Itemgroup/add');?>')" class="btn_blue" value="添加分组">
            <input type="button" onclick="doConfirmBatch('<?php echo U('Itemgroup/del', array('batchFlag' => 1));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">      
        </div>
    </div>
    <div class="list">    
    <form action="<?php echo U('Itemgroup/delAll');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>组名</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td><a href="<?php echo U('Iteminfo/index',array('group' => $v['name']), '');?>"><?php echo ($v["name"]); ?> | <?php echo ($v["remark"]); ?></a></td>
                <td><?php if($v['status']): ?>禁用<?php else: ?>正常<?php endif; ?></td>
                <td>
                    <a href="<?php echo U('Itemgroup/edit',array('id' => $v['id']), '');?>">编辑</a>
                    <a href="javascript:;" onclick="toConfirm('<?php echo U('Itemgroup/del',array('id' => $v['id']), '');?>', '确实要删除吗？')">删除</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="page" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>