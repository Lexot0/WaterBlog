<?php exit('404');?>
{include header}
<div class="headline">友情链接</div>
<!-- hook.admin_link_header -->
<div class="tip">用鼠标点击<div class="icon-drag"></div>上下拖拽可排序</div>
<div class="link drag">
	{foreach $linkList}
	<div class="form-row">
		<div class="icon-drag"></div>
		<!-- hook.admin_link_col_top -->
		<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="名称" value="{$item.name}"/></div>
		<div class="form-col"><label>链接</label><input type="text" name="url[]" placeholder="链接" value="{$item.url}"/></div>
		<!-- hook.admin_link_col_form -->
		<div class="form-col"><label><input name="target[]" type="checkbox" {if $item.target}checked=""{/if} value="{$index}">新窗口打开</label></div>
		<div class="form-col">
			<a class="red" href="javascript:;" onclick="del(this)">删除</a>
			<!-- hook.admin_link_col_operate -->
		</div>
		<!-- hook.admin_link_col_bottom -->
	</div>
	{/foreach}
</div>
<div class="btn" onclick="add()">添加链接</div>
<div class="btn bg-blue" onclick="submit()">保存</div>
<script>
	sx.drag({
		el:'.drag',
		dragEl:'.icon-drag'
	});
	function initTarget(){
		SX('[name="target[]"]').each(function(i){
			this.value = i;
		})
	}
	function del(el){
		SX(el.parentNode.parentNode).del();
		initTarget();
		// hook.admin_link_del_js
	}
	function add(){
		SX('.link').append(`<div class="form-row">
			<div class="icon-drag"></div>
			<!-- hook.admin_link_col_top_js -->
			<div class="form-col"><label>名称</label><input type="text" name="name[]" placeholder="名称" value=""/></div>
			<div class="form-col"><label>链接</label><input type="text" name="url[]" placeholder="链接" value=""/></div>
			<!-- hook.admin_link_col_form_js -->
			<div class="form-col"><label><input name="target[]" type="checkbox" value="1">新窗口打开</label></div>
			<div class="form-col">
				<a class="red" href="javascript:;" onclick="del(this)">删除</a>
				<!-- hook.admin_link_col_operate_js -->
			</div>
			<!-- hook.admin_link_col_bottom_js -->
		</div>`);
		initTarget();
		sx.drag({
			el:'.drag',
			dragEl:'.icon-drag'
		});
		// hook.admin_link_add_js
	}
	function submit(){
		SX.ajax('{url admin/link}',SX('.link').form()).then((res)=>{
			sx.pop(res.message);
			// hook.admin_link_submit_success_js
			!res.error && SX.pjax.render();
		});
	}
</script>
<!-- hook.admin_link_footer -->
{include footer}