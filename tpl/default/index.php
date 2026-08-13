<?php exit('404');?>
{include header}
<div class="banner">
	<canvas id="rain" width="1210" height="185"></canvas>
</div>
<div class="content">
	<div class="box">
		<div class="box-name list-title">
			<span>标题{if $page =='search'}（一共找到<span>{$article.count}</span>篇文章）{/if}</span>
			<span>分类</span>
			<span>浏览</span>
			<span>评论</span>
			<span>时间</span>
		</div>
		{if $article.list}
		<ul class="list">
		{foreach $article.list}
			{{
				$class = $item.isTop ? 'top' : 'common';
				$class = $item.isPrivate ? $class.' private' : $class;
				$time = explode(' ',$item.time);
			}}
			<li class="{$class}">
				<a href="{$item.url}">{$item.title}</a>
				<span>{$item.category.name}</span>
				<span>{$item.views}</span>
				<span>{$item.comments}</span>
				<span>{$time[0]}</span>
			</li>
		{/foreach}
		</ul>
		{else}
		<div class="empty">暂无数据</div>
		{/if}
		{$article.paging.html}
	</div>
	<div class="sidebar">
		<div class="box user">
			{if isset($conf.username)}
			<img src="{$conf.avatar}" alt="" class="avatar"/>
			<div class="user-name">{$conf.username}</div>
			{/if}
			<div class="user-info">
				<div>总文章<div>{$conf.article.count}</div></div>
				<div>总评论<div>{$conf.comment.count}</div></div>
				<div>总预览<div>{$conf.views}</div></div>
			</div>
			<form action="{url search}" class="search" method="post">
				<input type="text" name="name" placeholder="文章搜索" value="{$page == 'search'?$searchName:''}"/>
				<input type="submit" class="btn" value="搜索"/>
			</form>
		</div>
		<div class="box">
			<div class="box-name">分类</div>
			<ul class="sidebar-tag">
				{foreach $categoryList}
				<li><a href="{$item.url}">{$item.name}({$item.count})</a></li>
				{/foreach}
			</ul>
		</div>
		<div class="box">
			<div class="box-name">标签</div>
			<ul class="sidebar-tag">
				{foreach $tagList}
				<li><a href="{$item.url}">{$item.name}({$item.count})</a></li>
				{/foreach}
			</ul>
		</div>
	</div>
</div>
{include footer}