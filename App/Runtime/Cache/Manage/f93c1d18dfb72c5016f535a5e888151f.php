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
        var URL = '/xyh/xyhai.php?s=/Area';
        var APP	 = '/xyh/xyhai.php?s=';
        var SELF='/xyh/xyhai.php?s=/Area/index';
        var PUBLIC='/xyh/App/Manage/View/Public';
        //-->
        </script>
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?></div>    
    <div class="operate">
        <div class="left"><input type="button" onclick="goUrl('<?php echo U('Area/add', array('pid' => $pid));?>')" class="btn_blue" value="添加地区">
        <input type="button" onclick="doConfirmBatch('<?php echo U('Area/del', array('batchFlag' => 1,'pid' => $pid));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
        <input type="button" onclick="doGoSubmit('<?php echo U('Area/sort', array('pid' => $pid));?>','form_do')" class="btn_blue" value="更新排序"></div>
        <?php if($pid > 0): ?><input type="button" onclick="goUrl('<?php echo U('Area/index', array('pid' => 0));?>')" class="btn_blue" value="返回顶级">
        <?php else: ?>
        <input type="button" onclick="goUrl('<?php echo U('Area/createJsArea', array('pid' => 0));?>')" class="btn_blue" value="生成JS"><?php endif; ?>
        </div>
    </div>
    <div class="list">    
    <form action="<?php echo U('Area/sort', array('pid' => $pid));?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>名称</th>
                <th>简称</th>
                <th>英文名</th>
                <th>排序</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft"><a href="<?php echo U('Area/index',array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?></a></td>
                <td ><?php echo ($v["sname"]); ?></td>
				<td><?php echo ($v["ename"]); ?></td>
                <td><input type="text" name="sortlist[<?php echo ($v["id"]); ?>]" value="<?php echo ($v["sort"]); ?>" id="sortlist" size="5" /></td>
                <td>
                <a href="<?php echo U('Area/edit',array('id' => $v['id'], 'pid' => $v['pid']));?>">修改</a>
                <a href="<?php echo U('Area/del', array('id' => $v['id'], 'pid' => $v['pid']));?>">删除</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="page" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>