<?php exit('404');?>
{include header}
<div class="prompt-text">
	<div class="prompt-title">「温馨提示」</div>
	{$prompt} ＞﹏＜
	{if $url}
	<div class="prompt-footer">即将返回，<span id="prompt-time">3</span>秒</div>
	<script>setInterval(()=>{var t=SX('#prompt-time')[0];t.innerHTML-=1},1000),setTimeout(()=>{location.href='{$url}'},3000);</script>
	{else}
	<div class="prompt-footer"><a href="javascript:history.back(),history.back();">【返回上一步】</a>{if LOGIN}<a href="{url admin}">【返回首页】</a>{/if}</div>
	{/if}
</div>
{include footer}