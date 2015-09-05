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
        var URL = '/xyh/xyhai.php?s=/Attachment';
        var APP	 = '/xyh/xyhai.php?s=';
        var SELF='/xyh/xyhai.php?s=/Attachment/index';
        var PUBLIC='/xyh/App/Manage/View/Public';
        //-->
        </script>
</head>
<body>
<div class="main">
    <div class="pos">已上传文件管理
    </div>

    <div class="sub clear"><span>说明：</span>1、引用数：只统计图片类型的文件，被引用的文件建议不删除。2、
        缩略图:只针对图片类型文件。
    </div>
    <div class="list">    
    <form action="<?php echo U('Attachment/delAll');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th>编号</th>
                <th>原文件名称</th>
                <th>文件地址</th>
                <th>文件大小</th>
                <th>缩略图</th>
                <th>上传时间</th>
                <th>引用数</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft"><?php echo ($v["title"]); ?></td>
                <td class="aleft"><?php echo ($v["filepath"]); ?></td>
                <td><?php echo ($v["filesize"]); ?></td>
                <td><?php if($v['haslitpic'] == 1): ?>有<?php else: ?>无<?php endif; ?></td>
                <td><?php echo (date('Y-m-d H:i:s', $v["uploadtime"])); ?></td>
                <td><?php if($v['filetype'] == 1): ?><span class="red"><?php echo ($v["num"]); ?></span><?php else: ?><span>--</span><?php endif; ?></td>
                <td>
                    <?php if($v['filetype'] == 1): ?><a href="<?php echo ($upload); echo ($v["filepath"]); ?>" target="_blank">预览</a><?php else: ?>预览<?php endif; ?>
                    <a href="javascript:;" onclick="toConfirm('<?php echo U('Attachment/del',array('id' => $v['id']), '');?>', '确实要删除吗？')">删除</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="page" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>