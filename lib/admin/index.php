<?php exit('404');?>
{include header}
<div class="headline">网站首页</div>
<!-- hook.admin_index_header -->
<!-- hook.admin_index_system -->
<div class="title">系统信息</div>
<div class="info">
	<!-- hook.admin_index_system_top -->
	<div class="info-item">总文章：<span>{$conf.article.count}</span></div>
	<div class="info-item">总评论：<span>{$conf.comment.count}</span></div>
	<div class="info-item">总浏览：<span>{$conf.views}</span></div>
	<!-- hook.admin_index_info_top -->
	<div class="info-item">当前系统版本：<span>{#V}</span></div>
	<div class="info-item">当前数据库版本：<span>{$conf.db.version}</span></div>
	<!-- hook.admin_index_info_bottom -->
	<a href="javascript:checkUpdate()" class="blue a-line">检查更新</a>
	<a href="javascript:systemSync()" class="blue a-line">系统同步</a>
	<a href="javascript:dbSync()" class="blue a-line">数据库版本同步</a>
	<script>
		function dbSync(){
			SX.alert({
				content:`数据库版本将与官方最新版本同步，同步之前，请做好数据备份哦，以免更新失败造成数据丢失！是否同步？`,
				btn:['拒绝','立即同步'],
				yes(){
					SX.pop('同步中，请稍等...',0);
					SX.ajax('{url admin/dbUpdate}').then(res=>{
						if(res.error){
							SX.pop('同步失败');
						}else{
							SX.pop('同步成功',()=>{
								location.reload();
							});
						}
					})
				}
			})
		}
		function systemSync(){
			SX.alert({
				content:`系统核心文件将与官方最新版本同步，同步之前，请做好数据备份哦，以免更新失败造成数据丢失！是否同步？`,
				btn:['拒绝','立即同步'],
				yes(){
					SX.pop('同步中，请稍等...',0);
					SX.ajax('{url admin/update}').then(res=>{
						if(res.error){
							SX.pop('同步失败');
						}else{
							setTimeout(()=>{
								SX.ajax('{url admin/update}').then(res=>{
									if(res.error){
										SX.pop('同步失败');
									}else{
										SX.pop('同步成功啦！记得清理浏览器缓存刷新一下哦！',()=>{
											location.reload();
										});
									}
								})
							},1000);
						}
					})
				}
			})
		}
		function checkUpdate(a){
			SX.ajax('{url admin/checkUpdate}').then(res=>{
				if(res.error){
					SX.pop(res.error);
				}else{
					if(res.data){
						SX.alert({
							content:`清雨已有新版本<strong>${res.data}</strong>，更新之前，请做好数据备份哦，以免更新失败造成数据丢失！是否更新？`,
							btn:['拒绝','立即更新'],
							yes(){
								SX.pop('更新中，请稍等...',0);
								SX.ajax('{url admin/update}').then(res=>{
									if(res.error){
										sx.pop(res.message);
									}else{
										setTimeout(()=>{
											SX.ajax('{url admin/update}').then(res=>{
												if(res.error){
													sx.pop(res.message);
												}else{
													SX.pop('更新成功啦！记得清理浏览器缓存刷新一下哦！',()=>{
														location.reload();
													});
												}
											})
										},1000);
									}
								})
							}
						})
					}else{
						if(!a) SX.alert('您的版本已是最新，无需更新！');
					}
				}
			})
		}
	</script>
	{if $_SESSION.updateAlert}<script>checkUpdate(1);</script>{/if}
	<!-- hook.admin_index_system_bottom -->
</div>
<!-- hook.admin_index_news -->
<div class="title">官网动态</div>
<div class="border index-news">暂无</div>
<script>
	SX.ajax('{#API_HOST}getNews').then(res=>{
		if(!res.error && res.data){
			SX('.index-news').html(res.data);
		}
	})
</script>
<!-- hook.admin_index_server -->
<div class="title">服务器信息 · <a href="{url admin/error}" class="blue">错误日志</a> · <a href="{url admin/config}" class="blue" target="_blank">查看config</a> · <a href="{url admin/phpinfo}" class="blue" target="_blank">查看phpinfo</a></div>
<div class="table nowrap">
	<table>
		<!-- hook.admin_index_server_top -->
		<tr>
			<td class="text-right">服务器域名/IP地址</td>
			<td class="text-left">{$_SERVER.SERVER_NAME}（{if '/'==DIRECTORY_SEPARATOR}{$_SERVER.SERVER_ADDR}{else}{#@gethostbyname($_SERVER.SERVER_NAME)}{/if}）</td>
			<td class="text-right">当前您的IP地址</td>
			<td class="text-left">{#ip()}</td>
		</tr>
		<tr>
			<td class="text-right">服务器操作系统</td>
			<td class="text-left">{#php_uname('s')}</td>
			<td class="text-right">服务器解译引擎</td>
			<td class="text-left">{$_SERVER.SERVER_SOFTWARE}</td>
		</tr>
		<tr>
			<td class="text-right">服务器时间</td>
			<td class="text-left">{#date('Y-m-d H:i:s',time())}</td>
			<td class="text-right">服务器端口</td>
			<td class="text-left">{$_SERVER.SERVER_PORT}</td>
		</tr>
		<tr>
			<td class="text-right">服务器主机名</td>
			<td class="text-left">{#php_uname('n')}</td>
			<td class="text-right">绝对路径</td>
			<td class="text-left">{#ROOT}</td>
		</tr>
		<tr>
			<td class="text-right">PHP版本</td>
			<td class="text-left">{#PHP_VERSION}</td>
			<td class="text-right">探针路径</td>
			<td class="text-left">{#str_replace('\\','/',__FILE__)}</td>
		</tr>
		<tr>
			<td class="text-right">上传最大字节</td>
			<td class="text-left">{#ini_get('upload_max_filesize')}</td>
			<td class="text-right">当前模式</td>
			<td class="text-left">{if DEBUG==0}线上模式（无错）{elseif DEBUG==1}调试模式（无错+日志）{elseif DEBUG==2}开发模式（报错+日志）{/if}</td>
		</tr>
		<!-- hook.admin_index_server_bottom -->
	</table>
</div>
<!-- hook.admin_index_footer -->
{include footer}