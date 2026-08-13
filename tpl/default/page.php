<?php exit('404');?>
{include header}
<div class="box article">
	<div class="headline">{$article.title}</div>
	<div class="article-info">
		{if $article.category.name}
		<span>分类：<a href="{$article.category.url}">{$article.category.name}</a></span>
		{/if}
		<span>时间：{$article.time}</span>
	</div>
	<div class="article-content">{$article.content}</div>
	<div class="article-menu">
		{if $article.prev}
			<div class="article-menu-item">上一篇：<a href="{$article.prev.url}">{$article.prev.title}</a></div>
		{/if}
		{if $article.next}
			<div class="article-menu-item">下一篇：<a href="{$article.next.url}">{$article.next.title}</a></div>
		{/if}
	</div>
</div>
{if $article.isComment}
<div class="box comment">
	<form action="{url comment}" id="comment" method="post">
		<input type="hidden" name="page" value="{$id}"/>
		<input type="hidden" name="pid"/>
		<div class="comment-form">
			<div class="comment-info">
				<div class="comment-info-item">
					<label>名字</label><input type="text" name="name" maxlength="20" placeholder="你的名字" value="{$commentName}"/>
				</div>
				<div class="comment-info-item">
					<label>联系</label><input type="text" name="contact" maxlength="20" placeholder="联系方式" value="{$commentContact}"/>
				</div>
			</div>
			<textarea name="content" placeholder="评论留言"></textarea>
		</div>
		<div class="comment-submit">
			{if $conf.vcode.open}
			<img src="{url vcode}" onclick="this.src='{url vcode}'" class="vcode-img" title="点击更换验证码" alt="验证码"/>
			<input type="text" class="vcode-input" name="vcode" placeholder="验证码"/>
			{/if}
			<input type="submit" class="btn" value="提交"/><span class="comment-replys"></span>
		</div>
	</form>
	<div class="title">评论留言 (<span>{$comment.count}</span>条)</div>
	{$comment.html}
	{$comment.paging.html}
</div>
{/if}
{include footer}