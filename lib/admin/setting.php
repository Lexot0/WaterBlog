<?php exit('404');?>
{include header}
<div class="headline">网站设置</div>
<!-- hook.admin_setting_header -->
<form action="{url admin/setting}" method="post">
    <!-- hook.admin_setting_top -->
    <div class="form">
        <div class="key">网站标题</div>
        <div class="value">
            <input type="text" name="title" placeholder="网站名称" value="{$conf.title}"/>
            <small>浏览器上显示的名称，建议控制在60个字以内</small>
        </div>
    </div>
    <div class="form">
        <div class="key">网站名称</div>
        <div class="value">
            <input type="text" name="name" placeholder="网站名称" value="{$conf.name}"/>
            <small>网站中显示的名称，建议控制在60个字以内</small>
        </div>
    </div>
    <div class="form">
        <div class="key">网站描述</div>
        <div class="value">
            <textarea name="intro" placeholder="网站描述" rows="3">{$conf.intro}</textarea>
            <small>描述文字建议控制在300个字以内</small>
        </div>
    </div>
    <!-- hook.admin_setting_form_1 -->
    <div class="form">
        <div class="key">心情记录</div>
        <div class="value">
            <textarea name="mood" placeholder="心情记录" rows="3">{$conf.mood}</textarea>
        </div>
    </div>
    <div class="form">
        <div class="key">SEO关键字</div>
        <div class="value">
            <input type="text" name="key" placeholder="SEO关键字" value="{$conf.key}"/>
            <small>关键字请以逗号隔开,建议3个以上</small>
        </div>
    </div>
    <div class="form">
        <div class="key">SEO描述</div>
        <div class="value">
            <textarea name="desc" placeholder="SEO描述" rows="3">{$conf.desc}</textarea>
            <small>描述文字建议控制在100个字以内</small>
        </div>
    </div>
    <!-- hook.admin_setting_form_2 -->
    <div class="form">
        <div class="key">描述限制</div>
        <div class="value">
            <input type="text" name="brief" placeholder="描述限制" value="{$conf.brief}"/>
            <small>新建文章时的描述限制字数</small>
        </div>
    </div>
    <div class="form">
        <div class="key">用户头像</div>
        <div class="value">
            <input type="hidden" class="avatar" name="avatar" value="{$conf.avatar}"/>
            <img class="avatar" src="{$conf.avatar}" alt=""/>
            <div class="btn bg-orange" onclick="uploadAvatar()">上传</div>
        </div>
    </div>
    <div class="form">
        <div class="key">用户名</div>
        <div class="value">
            <input type="text" name="username" placeholder="用户名" value="{$conf.username}"/>
        </div>
    </div>
    <div class="form">
        <div class="key">登录密码</div>
        <div class="value">
            <input type="password" name="password" placeholder="新密码" value=""/>
            <small>如需设置新密码请输入，为空状态为原密码</small>
        </div>
    </div>
    <!-- hook.admin_setting_form_3 -->
    <div class="form">
        <div class="key">自动编译</div>
        <div class="value">
            <label>
                <input name="compile" type="checkbox" value="1" {if $conf.compile}checked{/if}/>主题模板根据修改自动编译，<span class="u">提示：尽量在开发过程中开启，因为她会消耗少许的性能</span>
            </label>
        </div>
    </div>
    <div class="form">
        <div class="key">伪静态</div>
        <div class="value">
            <label>
                <input name="rewrite" type="checkbox" value="1" {if $conf.rewrite}checked{/if}/><span class="u">提示：开启伪静态之前，请务必配置好网站的伪静态哦</span>
            </label>
        </div>
    </div>
    <div class="form">
        <div class="key">开发模式</div>
        <div class="value">
            <label>
                <input name="debug" type="radio" value="0" {if $conf.debug===0}checked{/if}/>线上模式(无错)
            </label>
            <label>
                <input name="debug" type="radio" value="1" {if $conf.debug===1}checked{/if}/>调试模式(无错+日志)
            </label>
            <label>
                <input name="debug" type="radio" value="2" {if $conf.debug===2}checked{/if}/>开发模式(报错+日志)
            </label>
        </div>
    </div>
    <div class="form">
        <div class="key">文章每页数</div>
        <div class="value">
            <input type="text" name="articlePaging" placeholder="文章每页数量" value="{$conf.article.paging}"/>
        </div>
    </div>
    <!-- hook.admin_setting_form_4 -->
    <div class="form">
        <div class="key">留言限制</div>
        <div class="value">
            <input type="text" name="commentRestrict" placeholder="每日留言限制次数" value="{$conf.comment.restrict}"/>
            <small>每日留言限制次数，强烈建议填写，防止灌水或遇到火车头</small>
        </div>
    </div>
    <div class="form">
        <div class="key">留言每页数</div>
        <div class="value">
            <input type="text" name="commentPaging" placeholder="留言每页数量" value="{$conf.comment.paging}"/>
        </div>
    </div>
    <div class="form">
        <div class="key">缩略图</div>
        <div class="value">
            <label><input name="thumbOpen" type="checkbox" value="1" {if $conf.thumb.open}checked{/if}/>开启</label>
            宽度：<input type="text" name="thumbWidth" style="width:80px;margin-right:20px;" placeholder="宽" value="{$conf.thumb.width}"/>
            高度：<input type="text" name="thumbHeight" style="width:80px;margin-right:20px;" placeholder="高" value="{$conf.thumb.height}"/>
            <label><input name="thumbType" type="radio" value="1" {if $conf.thumb.type==1:checked}/>裁剪缩略</label>
            <label><input name="thumbType" type="radio" value="2" {if $conf.thumb.type==2:checked}/>等比例缩略</label>
            <small>生成文章上传图片生成缩略图的宽高设置</small>
        </div>
    </div>
    <div class="form">
        <div class="key">验证码</div>
        <div class="value">
            <label><input name="vcodeOpen" type="checkbox" value="1" {if $conf.vcode.open}checked{/if}/>开启</label>
            宽度：<input type="text" name="vcodeWidth" style="width:80px;margin-right:20px;" placeholder="宽" value="{$conf.vcode.width}"/>
            高度：<input type="text" name="vcodeHeight" style="width:80px;margin-right:20px;" placeholder="高" value="{$conf.vcode.height}"/>
            字符数：<input type="text" name="vcodeLength" style="width:80px;margin-right:20px;" placeholder="长度" value="{$conf.vcode.length}"/>
            <small>验证码的宽度最低为60，高度最低为23，字符数请结合宽度来写，不然将导致验证码无法正常加载</small>
        </div>
    </div>
    <!-- hook.admin_setting_form_5 -->
    <div class="form">
        <div class="key">ICP备案号</div>
        <div class="value">
            <input type="text" name="icp" placeholder="ICP备案号" value="{$conf.icp}"/>
        </div>
    </div>
    <div class="form">
        <div class="key">公安备案号</div>
        <div class="value">
            <input type="text" name="prn" placeholder="公安备案号" value="{$conf.prn}"/>
        </div>
    </div>
    <div class="form">
        <div class="key">浏览量</div>
        <div class="value">
            <input type="text" name="views" placeholder="浏览量" value="{$conf.views}"/>
        </div>
    </div>
    <div class="form">
        <div class="key">敏感词</div>
        <div class="value">
            <textarea name="sensitive" placeholder="敏感词用空格隔开" rows="3">{$conf.sensitive}</textarea>
            <small>用于检测并屏蔽评论留言的敏感词，请以空格隔开</small>
        </div>
    </div>
    <div class="form">
        <div class="key">IP黑名单</div>
        <div class="value">
            <textarea name="blacklist" placeholder="黑名单IP用空格隔开" rows="3">{$conf.blacklist}</textarea>
            <small>以空格隔开，支持正则(双斜杠中为正则表达式)，例如屏蔽某个数字串154.56.***.78，应该这么写:/154\.56\.\d+\.78/</small>
        </div>
    </div>
    <div class="form">
        <div class="key">JS代码</div>
        <div class="value">
            <textarea name="js" placeholder="JS脚本代码" rows="3">{$conf.js}</textarea>
            <small>可以放置统计代码、百度分享、在线客服等脚本！</small>
        </div>
    </div>
    <!-- hook.admin_setting_bottom -->
    <div class="center"><input type="submit" class="btn bg-blue" value="提交"/></div>
</form>
<script>
    function uploadAvatar(){
        sx.upload({
            el:'.avatar',
            path:'/lib/img',
            name:'avatar.png'
        })
    }
</script>
<!-- hook.admin_setting_footer -->
{include footer}