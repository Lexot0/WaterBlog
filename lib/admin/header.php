<?php exit('404'); ?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
	<!-- hook.admin_head_header -->
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<!-- hook.admin_meta -->
	<title>{$conf.title}-后台管理</title>
	<link rel="shortcut icon" href="{#LIB_STYLE}logo.svg"/>
    <link rel="stylesheet" href="{#LIB_STYLE}fk.css"/>
	<link rel="stylesheet" href="{#LIB_STYLE}admin.css"/>
	<!-- hook.admin_css -->
	<script src="{#LIB_STYLE}common.js" data-load="false"></script>
	<!-- hook.admin_script -->
	<!-- hook.admin_head_footer -->
</head>
<body>
	<!-- hook.admin_body_header -->
	<div class="header">
		<div class="icon-menu"><i></i></div>
		<div class="header-title"><img src="{#LIB_STYLE}logo.svg" alt="{$conf.title}"/>后台管理</div>
		<div class="header-menu">
			{if LOGIN}
			<div class="header-menu-left">
				<!-- hook.admin_header_menu_left -->
			</div>
			{/if}
			<div class="header-menu-right">
				{if LOGIN}
				<!-- hook.admin_header_menu_right -->
				{/if}
				<a href="{#HOME}" target="_blank">返回首页</a>
				{if LOGIN}
				<!-- hook.admin_header_menu_right_login -->
				<a href="{url admin/logout}" data-pjax="false">退出</a>
				{/if}
			</div>
		</div>
	</div>
	{if LOGIN}
	<div class="sidebar">
		<!-- hook.admin_sidebar_top -->
		<div class="menu">
			<!-- hook.admin_sidebar_menu_top -->
			<a href="{url admin/index}">网站首页</a>
			<a href="{url admin/navbar}">导航设置</a>
			<!-- hook.admin_sidebar_menu_1 -->
			<a href="{url admin/category}">分类设置</a>
			<a href="{url admin/article}">文章管理</a>
			<!-- hook.admin_sidebar_menu_2 -->
			<a href="{url admin/tpl}">主题管理</a>
			<a href="{url admin/ext}">扩展管理</a>
			<!-- hook.admin_sidebar_menu_3 -->
			<a href="{url admin/setting}">网站设置</a>
			<a href="{url admin/link}">友情链接</a>
			<!-- hook.admin_sidebar_menu_bottom -->
		</div>
		<!-- hook.admin_sidebar_bottom -->
	</div>
	{/if}
	<div class="main">
		<!-- hook.admin_content_top -->
		<div id="pjax-content" class="content">