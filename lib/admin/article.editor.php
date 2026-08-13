<?php exit('404');?>
{include header}
<div class="headline">编辑文章</div>
<form action="{url admin/article/editor/$article.id}" method="post" class="article-form article-editor">
	<!-- hook.admin_article_editor_top -->
	<div class="form article-editor-title">
        <div class="key">标题</div>
        <div class="value">
            <input type="text" name="title" placeholder="文章标题" value="{$article.title}">
            <small>浏览器上显示的名称，建议控制在60个字以内</small>
        </div>
    </div>
	<!-- hook.admin_article_editor_title -->
	<div class="form article-editor-category">
        <div class="key">分类</div>
        <div class="value">
			<div class="select">
				<select name="cid" title="分类">
					{foreach $categoryList}
					<option value="{$item.id}" {if $item.id === $article.cid}selected="selected"{/if}>{$item.name}</option>
					{/foreach}
				</select>
			</div>
        </div>
    </div>
	<!-- hook.admin_article_editor_category -->
	<div class="form article-editor-tag">
        <div class="key">标签</div>
        <div class="value">
			<input type="text" name="tag" placeholder="多个标签用空格隔开" value="{$article.tag.name}"/>
        </div>
    </div>
	<!-- hook.admin_article_editor_tag -->
	<div class="form article-editor-url">
        <div class="key">URL</div>
        <div class="value">
			<input type="text" name="id" placeholder="URL名称，不填以时间命名" value="{$article.id}"/>
        </div>
    </div>
	<!-- hook.admin_article_editor_url -->
	<div class="form article-create-attr">
		<div class="key">属性</div>
		<div class="value">
			<!-- hook.admin_article_editor_attr_left -->
			<label><input name="isTop" type="checkbox" {if $article.isTop:checked} value="1"/>置顶</label>
			<label><input name="isPrivate" type="checkbox" {if $article.isPrivate:checked} value="1"/>私密</label>
			<label><input name="isComment" type="checkbox" {if $article.isComment:checked} value="1"/>评论</label>
			<label><input name="isFk" type="checkbox" {if $article.isFk:checked} value="1"/>FK</label>
			<!-- hook.admin_article_editor_attr_right -->
		</div>
	</div>
	<!-- hook.admin_article_editor_attr -->
	<div class="form article-editor-intro">
		<div class="key">描述</div>
		<div class="value">
			<textarea name="intro" rows="2" placeholder="描述，200字符以内">{$article.intro}</textarea>
		</div>
	</div>
	<!-- hook.admin_article_editor_intro -->
	<div class="form article-editor-content">
		<div class="key">内容</div>
		<div class="value">
			<textarea name="content" id="content" placeholder="内容">{#htmlentities($article.content)}</textarea>
		</div>
	</div>
	<!-- hook.admin_article_editor_content -->
	<div class="form article-editor-submit">
		<div class="key"></div>
		<div class="value">
			<input type="submit" class="btn bg-blue" value="保存"/>
		</div>
	</div>
	<!-- hook.admin_article_editor_bottom -->
	<!-- hook.editor -->
</form>
{include footer}