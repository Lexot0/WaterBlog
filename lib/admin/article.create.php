<?php exit('404');?>
{include header}
<div class="headline">创建文章</div>
<form action="{url admin/article/create}" method="post" class="article-form article-create">
	<!-- hook.admin_article_create_top -->
	<div class="form article-create-title">
        <div class="key">标题</div>
        <div class="value">
            <input type="text" name="title" placeholder="文章标题"/>
            <small>浏览器上显示的名称，建议控制在60个字以内</small>
        </div>
    </div>
	<!-- hook.admin_article_create_title -->
	<div class="form article-create-category">
        <div class="key">分类</div>
        <div class="value">
			<div class="select">
				<select name="cid" title="分类">
					{foreach $categoryList}
					<option value="{$item.id}">{$item.name}</option>
					{/foreach}
				</select>
			</div>
        </div>
    </div>
	<!-- hook.admin_article_create_category -->
	<div class="form article-create-tag">
        <div class="key">标签</div>
        <div class="value">
			<input type="text" name="tag" placeholder="多个标签用空格隔开"/>
        </div>
    </div>
	<!-- hook.admin_article_create_tag -->
	<div class="form article-create-url">
        <div class="key">URL</div>
        <div class="value">
			<input type="text" name="id" placeholder="URL名称，不填以时间命名"/>
        </div>
    </div>
	<!-- hook.admin_article_create_url -->
	<div class="form article-create-attr">
		<div class="key">属性</div>
		<div class="value">
			<!-- hook.admin_article_create_attr_left -->
			<label><input name="isTop" type="checkbox" value="1"/>置顶</label>
			<label><input name="isPrivate" type="checkbox" value="1"/>私密</label>
			<label><input name="isComment" type="checkbox" checked="checked" value="1"/>评论</label>
			<label><input name="isFk" type="checkbox" checked="checked" value="1"/>FK</label>
			<!-- hook.admin_article_create_attr_right -->
		</div>
	</div>
	<!-- hook.admin_article_create_attr -->
	<div class="form article-create-intro">
		<div class="key">描述</div>
		<div class="value">
			<textarea name="intro" rows="2" placeholder="描述，200字符以内"></textarea>
		</div>
	</div>
	<!-- hook.admin_article_create_intro -->
	<div class="form article-create-content">
		<div class="key">内容</div>
		<div class="value">
			<textarea name="content" id="content" placeholder="内容"></textarea>
		</div>
	</div>
	<!-- hook.admin_article_create_content -->
	<div class="form article-create-submit">
		<div class="key"></div>
		<div class="value">
			<input type="submit" class="btn bg-blue" value="保存"/>
		</div>
	</div>
	<!-- hook.admin_article_create_bottom -->
	<!-- hook.editor -->
</form>
{include footer}