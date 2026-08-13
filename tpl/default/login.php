<?php exit('404');?>
{include header}
<form action="{url admin/login}" method="post" class="box login">
	<div class="headline">登录 · 请输入密码</div>
	<input type="password" name="password" placeholder="密码" />
	{if $conf.vcode.open}
	<input type="text" name="vcode" placeholder="验证码"/>
	<img src="{url vcode}" onclick="this.src='{url vcode}'" class="vcode-img" title="点击更换验证码" alt="验证码"/>
	{/if}
	<input type="submit" class="btn" value="提交"/>
</form>
{include footer}