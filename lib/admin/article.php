<?php exit('404');?>
{include header}
<div class="headline">文章管理</div>
<!-- hook.admin_article_header -->
<div class="article">
	<div class="article-list">
		<!-- hook.admin_article_menu_header -->
		<div class="article-menu">
			<div class="article-menu-left">
				<!-- hook.admin_article_menu_left_top -->
				<div class="form-col">
					<div class="btn bg-red" onclick="del()">批量删除</div>
				</div>
				<div class="form-col">
					<div class="select">
						<select name="cid" title="分类">
							{foreach $categoryList}
							<option value="{$item.id}">{$item.name}</option>
							{/foreach}
						</select>
					</div>
					<div class="btn bg-blue" onclick="move('category')">移动到该分类</div>
				</div>
				<div class="form-col">
					<div class="select">
						<select name="tag">
							{foreach $tagList}
							<option value="{$item.id}">{$item.name}</option>
							{/foreach}
						</select>
					</div>
					<div class="btn bg-blue" onclick="move('tag')">移动到该标签</div>
				</div>
				<div class="form-col">
					<input type="text" name="search" placeholder="文章搜索"/>
					<div class="btn" onclick="search()">搜索</div>
					{if $type == 'search'}
					<a href="{url admin/article}" class="btn bg-gray ml">返回</a>
					{/if}
				</div>
				<!-- hook.admin_article_menu_left_bottom -->
			</div>
			<div class="article-menu-right">
				<!-- hook.admin_article_menu_right_top -->
				<a href="{url admin/article/create}" class="btn">创建文章</a>
				<!-- hook.admin_article_menu_right_bottom -->
			</div>
		</div>
		<!-- hook.admin_article_menu_footer -->
		{if $article.list}
		<div class="table table-hover nowrap article-table">
			<table>
				<thead>
					<tr>
						<th class="selAll">选择</th>
						<th style="width:100%;">标题</th>
						<!-- hook.admin_article_list_th_1 -->
						<th>分类</th>
						<th>标签</th>
						<th>浏览</th>
						<th>评论</th>
						<!-- hook.admin_article_list_th_2 -->
						<th>发布时间</th>
						<!-- hook.admin_article_list_th_3 -->
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach $article.list}
					{{
						$item.icon = $item.isTop ? '▲' : ($item.isPrivate ? '▣' : '❖');
						$item.updateTime = dates($item.updateTime);
						$item.createTime = dates($item.createTime);
					}}
					<tr>
						<td class="center"><input type="checkbox" name="article" value="{$item.id}"/></td>
						<td><a href="{$item.url}" class="article-title" target="_blank">{$item.icon} {$item.title}</a></td>
						<!-- hook.admin_article_list_td_1 -->
						<td>{$item.category.name}</td>
						<td class="article-tag">{$item.tag.name}</td>
						<td>{$item.views}</td>
						<td>{$item.comments}</td>
						<!-- hook.admin_article_list_td_2 -->
						<td>{$item.createTime}</td>
						<!-- hook.admin_article_list_td_3 -->
						<td>
							<a class="btn bg-blue" href="{url admin/article/editor/$item.id}">编辑</a>
							<a class="btn bg-red" href="javascript:sx.delArticle('{$item.id}')">删除</a>
							<!-- hook.admin_article_list_operate -->
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="paging">{$article.paging.html}</div>
		{else}
		<div class="empty">暂无数据</div>
		{/if}
		<!-- hook.admin_article_bottom -->
	</div>
	<div class="article-sidebar">
		<!-- hook.admin_article_sidebar_top -->
		<div class="title">分类</div>
		<ul class="tag-list">
			{foreach $categoryList}
			<li><a href="{url admin/article/category/$item.id}">{$item.name}({$item.count})</a></li>
			{/foreach}
		</ul>
		<div class="title">标签</div>
		<ul class="tag-list">
			<li><a href="{url admin/article}">全部({$conf.article.count})</a></li>
			{foreach $tagList}
			<li><a href="{url admin/article/tag/$item.id}">{$item.name}({$item.count})</a></li>
			{/foreach}
		</ul>
		<!-- hook.admin_article_sidebar_bottom -->
	</div>
</div>
<script>
	SX('tbody tr').click((_,i)=>{SX('[name=article]')[i].click()});
	SX('[name=article]').click(()=>{SX.sp()});
	SX('.selAll').click(()=>{SX('[name=article]').click()});
	//批量删除
	function del(){
		var sel = [];
		SX('[name=article]').each(function(){
			if(this.checked){
				sel.push(this.value);
			}
		})
		if(!sel.length) return SX.pop('请选择文章');
		SX.alert({
			content:'确实要删除吗？删除不可恢复！',
			yes(){
				SX.ajax('{url admin/article/delete}',{'id[]':sel}).then(res=>{
					sx.pop(res.message);
					!res.error && SX.pjax.render();
				})
			}
		})
	}
	//移动
	function move(type){
		var sel = [];
		SX('[name=article]').each(function(){
			if(this.checked){
				sel.push(this.value);
			}
		})
		if(!sel.length) return SX.pop('请选择文章');
		var data = {'id[]':sel};
		if(type == 'category'){ //分类
			data.cid = SX('[name=cid]')[0].value;
		}else if(type == 'tag'){ //标签
			data.cid = SX('[name=tag]')[0].value;
		}
		SX.ajax('{url admin/article/move/}'+type,data).then(res=>{
			sx.pop(res.message);
			!res.error && SX.pjax.render();
		})
	}
	//搜索
	function search(){
		var name = SX('[name=search]').val().trim();
		if(!name.length) return SX.pop('搜索内容不能为空');
		SX.pjax.open('{url admin/article/search/}'+name);
	}
</script>
<!-- hook.admin_article_footer -->
{include footer}