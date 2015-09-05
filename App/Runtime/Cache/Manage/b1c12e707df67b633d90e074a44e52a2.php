<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="/xyh/App/Manage/View/Public/css/style.css" />
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/common.js"></script>
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/jquery.form.min.js"></script>
<script type="text/javascript">
	var data_path = "/xyh/Data";
	var tpl_public = "/xyh/App/Manage/View/Public";
</script>
<script type="text/javascript" src="/xyh/App/Manage/View/Public/js/jBox.config.js"></script>
<script type="text/javascript">
$(function () {
	//缩略图上传
	var litpic_tip = $(".litpic_tip");
	var btn = $(".litpic_btn span");
	$("#fileupload").wrap("<form id='myupload' action='<?php echo U('Public/upload');?>' method='post' enctype='multipart/form-data'></form>");
    $("#fileupload").change(function(){
    	if($("#fileupload").val() == "") return;
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			beforeSend: function() {
        		$('#litpic_show').empty();
				btn.html("上传中...");
    		},
			success: function(data) {
				if(data.state == 'SUCCESS'){					
					litpic_tip.html(""+ data.info[0].name +" 上传成功("+data.info[0].size+"k)");
					var img = data.info[0].url;
					$('#litpic_show').html("<img src='"+img+"' width='88' height='31'>");
					$("#litpic").val(img);
				}else {
					litpic_tip.html(data.state);		
				}			
					btn.html("上传logo");

			},
			error:function(xhr){
				btn.html("上传失败");
				litpic_tip.html(xhr);
			}
		});
	});
	
});


$(function () {

	$('#BrowerPicture').click(function(){
		$.jBox("iframe:<?php echo U('Public/browseFile', array('stype' => 'picture'));?>",{
			title:'XYHCMS',
			width: 650,
   			height: 350,
    		buttons: { '关闭': true }
   			}
		);
	});	

});


function selectPicture(sfile) {
	$.jBox.tip("选择文件成功");
	$.jBox.close(true);
	$("#litpic").val(sfile);
	$('#litpic_show').html("<img src='"+sfile+"' width='120'>");
}

</script>
</head>
<body>
<div class="main">
    <div class="pos">修改友情链接</div>
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U('Link/edit');?>">
		<dl>
			<dt> 网站名称：</dt>
			<dd>
				<input type="text" name="name" class="inp_large" value="<?php echo ($vo["name"]); ?>" />
			</dd>
		</dl>
		<dl>
			<dt> 网站地址：</dt>
			<dd>
				<input type="text" name="url" class="inp_large" value="<?php echo ($vo["url"]); ?>" />
			</dd>
		</dl>
		<dl>
			<dt> 网站Logo：</dt>
			<dd>
				<div class="litpic_show">
				    <div style="float:left;">
				    <input type="text" class="inp_w250" name="logo" id="litpic"  value="<?php echo ($vo["logo"]); ?>" />
				    <input type="button" class="btn_blue_b" id="BrowerPicture" value="选择站内图片">
				    </div>
						<div class="litpic_btn">
				        <span>上传Logo</span>
				        <input id="fileupload" type="file" name="mypic">
				    </div>
				    <div class="litpic_tip"></div>
				    <div id="litpic_show">
				    	<?php if($vo['logo']): ?><img src="<?php echo ($vo["logo"]); ?>" width='88' height='31' /><?php endif; ?>
				    </div>
				</div>
			</dd>
		</dl>	
		<dl>
			<dt> 排列位置：</dt>
			<dd>
				<input type="text" name="sort" class="inp_small" value="<?php echo ($vo["sort"]); ?>" />
			</dd>
		</dl>
		<dl>
			<dt> 网站简况：</dt>
			<dd>
				<textarea name="description" class="tarea_default"><?php echo ($vo["description"]); ?></textarea>
			</dd>
		</dl>	

		<dl>
			<dt> 链接位置：</dt>
			<dd>
				<input type="radio" name="ischeck" value="0" <?php if($vo['ischeck'] == 0): ?>checked="checked"<?php endif; ?> />首页
				&nbsp;&nbsp;
				<input type="radio" name="ischeck" value="1" <?php if($vo['ischeck']): ?>checked="checked"<?php endif; ?> />内页

			</dd>
		</dl>
		<div class="form_b">		
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
	</div>
</div>


</body>
</html>