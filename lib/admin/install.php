<?php if(!isset($conf)) exit('404');?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>清雨-系统安装</title>
    <link rel="shortcut icon" href="<?php echo HOME;?>lib/style/logo.svg"/>
	<link rel="stylesheet" href="<?php echo HOME;?>lib/style/admin.css"/>
</head>
<body style="background:#fff;">
	<div class="header" style="height:60px">
		<div class="header-title">清雨-系统安装</div>
	</div>
	<form action="<?php echo URL;?>install" method="post" style="padding:60px 20px">
		<input type="hidden" name="install" value="1"/>
	    <div class="form">
	        <div class="key">网站标题</div>
	        <div class="value">
	            <input type="text" name="title" placeholder="网站标题" value="<?php echo $conf['title'];?>"/>
	            <small>浏览器上显示的名称，建议控制在60个字以内</small>
	        </div>
	    </div>
	    <div class="form">
	        <div class="key">网站名称</div>
	        <div class="value">
	            <input type="text" name="name" placeholder="网站名称" value="<?php echo $conf['name'];?>"/>
	            <small>网站中显示的名称，建议控制在60个字以内</small>
	        </div>
	    </div>
	    <div class="form">
	        <div class="key">网站描述</div>
	        <div class="value">
	            <textarea name="intro" placeholder="网站描述" rows="3"><?php echo $conf['intro'];?></textarea>
	            <small class="help">描述文字建议控制在300个字以内</small>
	        </div>
	    </div>
	    <div class="form">
	        <div class="key">登录密码</div>
	        <div class="value">
	            <input name="password" type="password" placeholder="密码" value=""/>
	        </div>
	    </div>
	    <div class="center"><input type="submit" class="btn" value="提交"/></div>
	</form>
</body>
</html>