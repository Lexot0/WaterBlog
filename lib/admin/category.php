<?php exit('404');?>
{include header}
<div class="headline">分类设置</div>
<!-- hook.admin_category_header -->
<div class="tip">用鼠标点击<div class="icon-drag"></div>上下拖拽可排序</div>
<div class="category drag">
	{foreach $categoryList}
	<div class="form-row">
		<div class="icon-drag"></div>
		<input type="hidden" name="id[]" value="{$item.id}"/>
		<!-- hook.admin_category_col_top -->
		<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="分类名称" value="{$item.name}"/></div>
		<div class="form-col"><label>别称</label><input type="text" name="newId[]" placeholder="分类别称" value="{$item.id}"/></div>
		<div class="form-col"><label>描述</label><input type="text" name="intro[]" placeholder="分类描述" value="{$item.intro}"/></div>
		<!-- hook.admin_category_col_form -->
		<div class="form-col">
			<a class="red" href="javascript:;" onclick="del(this,'{$item.id}')">删除</a>
			<!-- hook.admin_category_col_operate -->
		</div>
		<!-- hook.admin_category_col_bottom -->
	</div>
	{/foreach}
</div>
<div class="btn" onclick="add()">添加分类</div>
<div class="btn bg-blue" onclick="submit()">保存</div>
<script>
	sx.drag({
		el:'.drag',
		dragEl:'.icon-drag'
	});
	var delId = [];
	function del(el,id=''){
		if(id.length)delId.push(id);
		SX(el.parentNode.parentNode).del();
		// hook.admin_category_del_js
	}
	function add(){
		SX('.category').append(`<div class="form-row">
			<div class="icon-drag"></div>
			<input type="hidden" name="id[]" value=""/>
			<!-- hook.admin_category_col_top_js -->
			<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="分类名称" value=""/></div>
			<div class="form-col"><label>别称</label><input type="text" name="newId[]" placeholder="分类别称" value=""/></div>
			<div class="form-col"><label>描述</label><input type="text" name="intro[]" placeholder="分类描述" value=""/></div>
			<!-- hook.admin_category_col_form_js -->
			<div class="form-col">
				<a class="red" href="javascript:;" onclick="del(this)">删除</a>
				<!-- hook.admin_category_col_operate_js -->
			</div>
			<!-- hook.admin_category_col_bottom_js -->
		</div>`);
		// hook.admin_category_add_js
		sx.drag({
			el:'.drag',
			dragEl:'.icon-drag'
		});
	}
	function submit(){
		var data = SX('.category').form();
		data['delId[]'] = delId;
		// hook.admin_category_submit_js
		SX.ajax('{url admin/category}',data).then((res)=>{
			sx.pop(res.message);
			// hook.admin_category_submit_success_js
			!res.error && SX.pjax.render();
		});
	}
</script>
<!-- hook.admin_category_footer -->
{include footer}