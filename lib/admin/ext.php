<?php exit('404');?>
{include header}
<div class="headline">扩展管理</div>
<!-- hook.admin_ext_header -->
<div class="tip">一共<span>{$ext.count}</span>个扩展 ‧ 已安装<span>{$ext.installCount}</span>个扩展 · <a href="javascript:SX.import('ext')" class="red">导入扩展</a><!-- hook.admin_ext_menu --></div>
<!-- hook.admin_ext_list_top -->
{if $ext.installList}
<div class="title">已安装</div>
<ul class="ext">
{foreach $ext.installList}
	<li class="ext-uninstall">
		<div class="ext-icon"><img src="{$item.icon}"/></div>
		<div class="ext-info">
			<div class="ext-name">
				{$item.name}<span class="ext-version">v{$item.version}</span>
				<!-- hook.admin_ext_install_operate_top -->
				<a href="{url admin/ext/uninstall/$item.id}" data-pjax="false" class="ext-btn" onclick="return SX.confirm(this,'确实要卸载吗？')">卸载</a>
				<a href="{url admin/ext/download/$item.id}" data-pjax="false" class="tpl-btn bg-purple">导出</a>
				{if $item.setting}<a href="{url admin/ext/setting/$item.id}" class="tpl-btn bg-blue">设置</a>{/if}
				<!-- hook.admin_ext_install_operate_bottom -->
			</div>
			<div class="ext-intro">{$item.intro}</div>
		</div>
	</li>
{/foreach}
</ul>
{/if}
{if $ext.notInstallList}
<div class="title">未安装</div>
<ul class="ext">
{foreach $ext.notInstallList}
	<li class="ext-install">
		<div class="ext-icon"><img src="{$item.icon}"/></div>
		<div class="ext-info">
			<div class="ext-name">
				{$item.name}<span class="ext-version">v{$item.version}</span>
				<!-- hook.admin_ext_notInstall_operate_top -->
				<a class="ext-btn" href="{url admin/ext/install/$item.id}" data-pjax="false">安装</a>
				<a href="{url admin/ext/delete/$item.id}" data-pjax="false" class="ext-btn bg-red" onclick="return SX.confirm(this,'确实要删除吗？删除不可恢复！')">删除</a>
				<a href="{url admin/ext/download/$item.id}" data-pjax="false" class="tpl-btn bg-purple">导出</a>
				<!-- hook.admin_ext_notInstall_operate_bottom -->
			</div>
			<div class="ext-intro">{$item.intro}</div>
		</div>
	</li>
{/foreach}
</ul>
{/if}
<!-- hook.admin_ext_footer -->
{include footer}