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
        var URL = '/xyh/xyhai.php?s=/Special';
        var APP	 = '/xyh/xyhai.php?s=';
        var SELF='/xyh/xyhai.php?s=/Special/index';
        var PUBLIC='/xyh/App/Manage/View/Public';
        //-->
        </script>
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?></div>
    <div class="operate">
        <div class="left">
            <?php if(ACTION_NAME == "index"): ?><input type="button" onclick="goUrl('<?php echo U('Special/add');?>')" class="btn_blue" value="添加专题">
                <input type="button" onclick="doConfirmBatch('<?php echo U('Special/del', array('batchFlag' => 1));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
                <input type="button" onclick="goUrl('<?php echo U('Special/trach');?>')" class="btn_green" value="回收站">
            <?php else: ?>
                <input type="button" onclick="goUrl('<?php echo U('Special/index');?>')" class="btn_blue" value="返回">
                <input type="button" onclick="doGoBatch('<?php echo U('Special/restore', array('batchFlag' => 1));?>')" class="btn_green" value="还原">
                <input type="button" onclick="doConfirmBatch('<?php echo U('Special/clear', array('batchFlag' => 1));?>', '确实要彻底删除选择项吗？')" class="btn_orange" value="彻底删除"><?php endif; ?>


            
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="post" action="<?php echo U('Special/index');?>">
                <input type="text" name="keyword" title="关键字" class="inp_default" value="<?php echo ($keyword); ?>"> 
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
    </div>
    <div class="list">    
    <form action="<?php echo U('Special/del');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>标题</th>
                <th>分类</th>
                <th>点击</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft" style="color:<?php echo ($v["color"]); ?>"><?php echo ($v["title"]); if($v["flag"] > 0): ?><span style="color:#079B04;">[<?php echo (flag2Str($v["flag"])); ?>]</span><?php endif; ?></td>
                <td><?php echo ($v["catename"]); ?></td>
                <td><?php echo ($v["click"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s', $v["updatetime"])); ?></td>
                <td>
                <?php if(ACTION_NAME == "index"): ?><a href="<?php echo (view_url($v,'Special/shows')); ?>" target="_blank">查看</a>
                <a href="<?php echo U('Special/edit',array('id' => $v['id']), '');?>">编辑</a>
                <a href="javascript:;" onclick="toConfirm('<?php echo U('Special/del',array('id' => $v['id']), '');?>', '确实要彻底删除吗？')">删除</a>
                <?php else: ?>
                <a href="<?php echo U('Special/restore',array('id' => $v['id']), '');?>">还原</a>
                <a href="javascript:;" onclick="toConfirm('<?php echo U('Special/clear',array('id' => $v['id']), '');?>', '确实要彻底删除吗？')">彻底删除</a><?php endif; ?>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="page" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>