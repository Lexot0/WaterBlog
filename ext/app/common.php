<?php
if($page == 'admin'){
	//添加菜单
	hook('admin_sidebar_menu_3','<a href="'.URL.'admin/app">应用中心</a>');

	//主题管理中添加菜单
	// if($adminPage == 'tpl'){
	// 	hook('admin_tpl_operate',function(){
	// 		global $item;
	// 		return '<a href="'.URL.'admin/app/upload/'.$item['id'].'" class="tpl-btn bg-orange">上传</a>';
	// 	});
	// }
}
?>