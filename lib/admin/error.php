<?php exit('404');?>
{include header}
<div class="headline">错误日志</div>
<!-- hook.admin_error_header -->
<div class="tip">一共<span>{$error.count}</span>条错误日志 · <a href="{url admin/error/delete}" data-pjax="false" class="red" onclick="return SX.confirm(this,'确实要清空吗？清空不可恢复！')">清空日志</a> <!-- hook.admin_error_menu --></div>
<!-- hook.admin_error_list_top -->
<ul class="log">
	{foreach $error.list}
		<li>
			<span class="log-ip">IP：{$item.ip}</span>
			<span class="log-time">时间：{#dates($item.time)}</span>
			<p>请求地址：{$item.url}</p>
			<p>{$item.content}</p>
		</li>
	{/foreach}
</ul>
<div class="paging">{$error.paging.html}</div>
<!-- hook.admin_error_footer -->
{include footer}