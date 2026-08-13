<?php exit('404');?>
		</div>
		<div class="footer">
			<!-- hook.admin_footer -->
			<div class="footer-bar">
				<div class="footer-left">
					<span>开源系统 - <a href="https://prain.cn" target="_blank">清雨 v{#V}</a></span>
				</div>
				<div class="footer-right">
					<span>RunTime: {#getRunTime()} s</span>
					<span>Memory: {#getMemory()} kb</span>
				</div>
			</div>
			{$conf.js}
		</div>
		<!-- hook.admin_body_footer -->
	</div>
</body>
</html>