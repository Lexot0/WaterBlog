# 清雨博客-PHP开源博客系统

#### 官方
+ 演示网站：[https://prain.cn](https://prain.cn)
+ 官方网站：[https://prain.cn](https://prain.cn)
+ 开发文档：[https://prain.cn/doc](https://prain.cn/doc)
+ 官方QQ群：[132264532](https://qm.qq.com/cgi-bin/qm/qr?k=v0OefYdxwvTNa8F2jxOr4FLZvj2hM4pL&jump_from=webapi&authKey=+mpW0tRQ5Jukk346+GmDYRp5yAS8ZoCrH5clOnOUXwe8l8LlFsdaX1pKRW23t/gG)

#### 介绍
清雨博客（Prain：Pure Rain的缩写，语义为清纯的雨，在这里表示清雨）是一款极为干净的开源PHP轻博客程序，整个程序包不到150KB，极为简小，与一张照片的大小相当，简洁高效，占用内存极小，不依赖任何数据库，不依赖富文本编辑器，但她却拥有十分强大的排版功能，这得力于她自身的fk标记语言，在开发她之前我有想过清雨的风格类型，兼容PC端和移动端，舍弃繁杂的界面和程序结构，以最直观最干净的方式呈现给用户，然后清雨诞生了，她的核心基于fk标记语言，所以她的存在将是目前博客程序前所未有的简洁，并且是十分高效的。

清雨十分简洁，功能却很出众，拥有基本的文章管理、主题管理、扩展管理等常用功能需求。该博客系统定位十分明确，就是简洁干净，不依赖任何第三方框架，包括不依赖数据库，官网就是采用的prain程序。


#### 特点
+ 整个程序包不到150KB，极为简小，比一张图片都要小的多
+ 占用内存极小，高效简洁，性能十分出色，是款干净的轻博客程序
+ 无需数据库，不依赖MySQL、Oracle、SQLServer、SQLite等数据库，降低维护成本
+ 核心由fk标记语言支持，具有强大的排版功能，无需使用任何富文本编辑器
+ 拥有强大的模板编译功能，使用简洁的标签编写精美的主题界面
+ 拥有强大的插件扩展机制，不满功能需求，可自由扩展

#### 环境要求
PHP版本：PHP5.6+ 推荐PHP8.0+

#### 安装
将下载的程序代码解压到你的网站根目录，直接运行你的网站，会自动跳转到安装页面
在安装页面输入您的网站标题、网站名称、网站描述、登录密码，点击提交后会进入首页

#### 伪静态设置
Nginx环境：
```nginx
if (!-d $request_filename){
    set $rule 1$rule;
}
if (!-f $request_filename){
    set $rule 2$rule;
}
if ($rule = "21"){
 rewrite ^/.*?([^/]*)$ /index.php?$1 last;
}
rewrite ^/db/(?!upload/).*? /[F];
```

Apache环境：
```apache
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^.*?([^/]*)$ index.php?$1 [QSA,PT,L]
RewriteRule ^db/(?!upload/).*? [F]
</IfModule>
```