<?php
//官网：https://prain.cn
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
$_SERVER['_memory_usage'] = memory_get_usage();
$timeStart = microtime(true);
session_start();
//主页
define('HOME',substr($_SERVER['SCRIPT_NAME'],0,-9));
//根目录
$root = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
$root = (substr($root,-1)==='/'?substr($root,0,-1):$root).HOME;
define('ROOT',$root);
//数据目录
define('DB',ROOT.'db/');
//类库目录
define('LIB',ROOT.'lib/');
//扩展目录
define('EXT',ROOT.'ext/');
//官网API接口地址
define('API_HOST','https://prain.cn/api/');
//清雨版本
define('V','1.3.4');

//引入库
include LIB.'function.php';
include LIB.'common.php';
include LIB.'fk.class.php';
include LIB.'tpl.class.php';
include LIB.'file.util.class.php';

//文件操作类
$util = new fileUtil();
//系统配置
$conf = db('conf');
//配置备份与还原
$confBackup = db('conf.backup');
if($confBackup){
	if(!isset($conf['rewrite'])){
		$conf = $confBackup;
		dbSave('conf', $confBackup);
	}elseif($conf != $confBackup){
		dbSave('conf.backup', $conf);
	}
}else{
	dbSave('conf.backup', $conf);
}
//用户配置
$ini = db('ini');
//恢复后台登录状态
$loginCookieName = 'prain_login';
$loginCookiePath = HOME ? HOME : '/';
if(empty($_SESSION['login']) && !empty($_COOKIE[$loginCookieName])){
	$loginCookie = explode(':', $_COOKIE[$loginCookieName], 2);
	$loginTime = isset($loginCookie[0]) ? (int)$loginCookie[0] : 0;
	$loginSign = isset($loginCookie[1]) ? $loginCookie[1] : '';
	$loginPayload = $loginTime.'|'.ip().'|'.(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	$loginHash = hash_hmac('sha256', $loginPayload, $conf['password']);
	$loginValid = function_exists('hash_equals') ? hash_equals($loginHash, $loginSign) : $loginHash === $loginSign;
	if($loginTime > time() - 2592000 && $loginSign && $loginValid){
		$_SESSION['login'] = true;
		$_SESSION['token'] = getToken();
	}else{
		setcookie($loginCookieName, '', time() - 3600, $loginCookiePath, '', isHttps(), true);
	}
}
//保存登陆状态
define('LOGIN', isset($_SESSION['login'])?$_SESSION['login']:false);
//0:线上模式（无错）1:调试模式（无错+日志）2:开发模式（报错+日志）
define('DEBUG', $conf['debug']);
//伪静态url
define('URL',HOME.($conf['rewrite']?'':'?'));
function_exists('ini_set') AND ini_set('display_errors', DEBUG ? '1' : '0');
error_reporting(DEBUG ? E_ALL : 0);
DEBUG AND set_error_handler('errorHandle', -1);

//缓存信息
$_SESSION['ip'] = isset($_SESSION['ip']) ? $_SESSION['ip'] : ip();
$_SESSION['checkIP'] = isset($_SESSION['checkIP']) ? $_SESSION['checkIP'] : 0;
$_SESSION['views'] = isset($_SESSION['views']) ? $_SESSION['views'] : [];
$_SESSION['commentName'] = $commentName = isset($_SESSION['commentName']) ? $_SESSION['commentName'] : '';
$_SESSION['commentContact'] = $commentContact = isset($_SESSION['commentContact']) ? $_SESSION['commentContact'] : '';
$_SESSION['commentCount'] = isset($_SESSION['commentCount']) ? $_SESSION['commentCount'] : 0;
$_SESSION['loginCount'] = isset($_SESSION['loginCount']) ? $_SESSION['loginCount'] : 0;
$_SESSION['vcode'] = isset($_SESSION['vcode']) ? $_SESSION['vcode'] : 0;
$_SESSION['token'] = isset($_SESSION['token']) ? $_SESSION['token'] : 0;
$_SESSION['includeTheme'] = 0;

//鉴权
if(LOGIN && $_SESSION['token'] !== getToken()){
	setcookie($loginCookieName, '', time() - 3600, $loginCookiePath, '', isHttps(), true);
	session_destroy();
	jump();
}

//请求方式：POST、GET
$method = $_SERVER['REQUEST_METHOD'];
//是否为ajax请求
$ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) == 'xmlhttprequest');
//当前地址
$url = $_SERVER['REQUEST_URI'];
//当前主题模板绝对路径
define('TPLPATH',ROOT.'tpl/'.$conf['tpl'].'/');
//当前主题模板相对路径
define('TPL',HOME.'tpl/'.$conf['tpl'].'/');
//当前主题样式路径
define('TPL_STYLE',HOME.'tpl/'.$conf['tpl'].'/style/');
//当前主题图片路径
define('TPL_IMG',HOME.'tpl/'.$conf['tpl'].'/img/');
//公共样式路径
define('LIB_STYLE',HOME.'lib/style/');
//当前网址
$host = (isHttps() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].HOME;
//前端页面
$page = get(0,'str','index');
//后端页面
$adminPage = get(1,'str','index');
//后端检测登陆
if($page == 'admin' && $adminPage != 'login' && $adminPage != 'prompt') checkLogin();
//hook钩子名称预设
//前端
$hook=[];
//后端
foreach ([
	//全局公共
	'common','editor','prompt','article_init','category_init','get_article_info','get_article_list','pages_init','get_category',
	'tag_init','get_tag','get_comment','get_comment_html','del_comment',
	//后端视图层
	'admin_head_header','admin_meta','admin_css','admin_script','admin_head_footer','admin_body_header','admin_sidebar_top','admin_sidebar_menu_top','admin_sidebar_menu_1','admin_sidebar_menu_2','admin_sidebar_menu_3','admin_sidebar_menu_bottom','admin_sidebar_bottom','admin_header_menu_left','admin_header_menu_right_login','admin_header_menu_right','admin_content_top','admin_footer','admin_body_footer','admin_body_footer','admin_body_footer','admin_body_footer',
	//首页模板
	'admin_index_header','admin_index_info_top','admin_index_info_bottom','admin_index_system','admin_index_system_top','admin_index_system_bottom','admin_index_news','admin_index_server','admin_index_server_top','admin_index_server_bottom','admin_index_footer',
	//文章管理
	'admin_article_header','admin_article_menu_header','admin_article_menu_left_top','admin_article_menu_left_bottom','admin_article_menu_right_top','admin_article_menu_right_bottom','admin_article_menu_footer','admin_article_list_th_1','admin_article_list_th_2','admin_article_list_th_3','admin_article_list_td_1','admin_article_list_td_2','admin_article_list_td_3','admin_article_list_operate','admin_article_bottom','admin_article_sidebar_top','admin_article_sidebar_bottom','admin_article_footer',
	//创建文章
	'admin_article_create_top','admin_article_create_title','admin_article_create_category','admin_article_create_tag','admin_article_create_url','admin_article_create_attr','admin_article_create_attr_left','admin_article_create_attr_right','admin_article_create_intro','admin_article_create_content','admin_article_create_bottom',
	//编辑文章
	'admin_article_editor_top','admin_article_editor_title','admin_article_editor_category','admin_article_editor_tag','admin_article_editor_url','admin_article_editor_attr','admin_article_editor_attr_left','admin_article_editor_attr_right','admin_article_editor_intro','admin_article_editor_content','admin_article_editor_bottom',
	//导航设置
	'admin_navbar_header','admin_navbar_col_top','admin_navbar_col_form','admin_navbar_col_operate','admin_navbar_col_bottom','admin_navbar_del_js','admin_navbar_col_top_js','admin_navbar_col_form_js','admin_navbar_col_operate_js','admin_navbar_col_bottom_js','admin_navbar_add_js','admin_navbar_submit_success_js','admin_navbar_footer',
	//分类管理
	'admin_category_header','admin_category_col_top','admin_category_col_form','admin_category_col_operate','admin_category_col_bottom','admin_category_del_js','admin_category_col_top_js','admin_category_col_form_js','admin_category_col_operate_js','admin_category_col_bottom_js','admin_category_add_js','admin_category_submit_js','admin_category_submit_success_js','admin_category_footer',
	//友情链接
	'admin_link_header','admin_link_col_top','admin_link_col_form','admin_link_col_operate','admin_link_col_bottom','admin_link_del_js','admin_link_col_top_js','admin_link_col_form_js','admin_link_col_operate_js','admin_link_col_bottom_js','admin_link_add_js','admin_link_submit_success_js','admin_link_footer',
	//设置
	'admin_setting_header','admin_setting_top','admin_setting_form_1','admin_setting_form_2','admin_setting_form_3','admin_setting_form_4','admin_setting_form_5','admin_setting_bottom','admin_setting_footer',
	//主题
	'admin_tpl_header','admin_tpl_menu','admin_tpl_list_top','admin_tpl_install_operate','admin_tpl_notInstall_operate','admin_tpl_operate','admin_tpl_footer',
	//扩展
	'admin_ext_header','admin_ext_menu','admin_ext_list_top','admin_ext_install_operate_top','admin_ext_install_operate_bottom','admin_ext_notInstall_operate_top','admin_ext_notInstall_operate_bottom','admin_ext_footer',
	//错误
	'admin_error_header','admin_error_menu','admin_error_list_top','admin_error_footer',
	//登录
	'admin_login_header','admin_login_form','admin_login_form_bottom','admin_login_footer',
	//后端业务层
	'admin_model_common','admin_model_login_success','admin_model_login_fail','admin_model_navbar_item','admin_model_navbar','admin_model_category_item','admin_model_category','admin_model_link','admin_model_article_category','admin_model_article_tag','admin_model_article_search','admin_model_article_delete','admin_model_article_move_tag','admin_model_article_move_category','admin_model_article_create','admin_model_article_create_success','admin_model_article_create_fail','admin_model_article_editor','admin_model_article_editor_success','admin_model_article_editor_fail','admin_model_article','admin_model_setting','admin_model_tpl','admin_model_tpl_install','admin_model_tpl_uninstall','admin_model_tpl_delete','admin_model_tpl_download','admin_model_ext','admin_model_ext_install','admin_model_ext_uninstall','admin_model_ext_delete','admin_model_ext_download','admin_model_error','admin_model_default_page',
	//前端视图层
	'head_header','meta','css','script','head_footer','body_header','body_footer',
	//前端业务层
	'model_index','model_category','model_tag','model_search','model_comment_delete','model_comment_delete_success','model_comment_delete_fail','model_comment_add','model_comment_add_success','model_comment_add_fail','model_upload','model_import','model_prompt','model_default_page','model_default_page_filter',
] as $extTag) {
	$hook[$extTag] = [];
}
//模板编译
$tpl = new Tpl([
	'path' => '/tpl/',
	'name' => $conf['tpl'],
	'compile' => $conf['compile'],
]);
//获取配置信息
$ajax && post('getConf','int') && ret(['HOST'=>getHost(),'HOME'=>HOME,'URL'=>URL]);
//检测安装
if(!$conf['install']){
	!is_writeable(DB) && exit('db文件夹无写入权限，请检查！');
	!is_writeable(TPLPATH) && exit('tpl文件夹无写入权限，请检查！');
	!extension_loaded('curl') && exit('请开启curl扩展！');
	!extension_loaded('gd') && exit('请开启gd扩展！');
	if($page == 'install'){
		if($method == 'POST'){
			$conf['title'] = post('title','str',$conf['title']);
			$conf['name'] = post('name','str',$conf['name']);
			$conf['intro'] = post('intro','str',$conf['intro']);
			$conf['password'] = md5((string)post('password','str',$conf['password']));
			$conf['install'] = post('install','bool',$conf['install']);
			if($conf['password']){
				$tpl->compile();
				conf($conf);
			}
			jump();
		}
	}
	exit(include LIB.'admin/install.php');
}
//ip黑名单检测
if(!$_SESSION['checkIP'] && $conf['blacklist']){
	$_SESSION['checkIP'] = 1;
	$blacklist = explode(' ',$conf['blacklist']);
	$error = '你的IP被系统拉入黑名单，如需解除，请联系管理员！';
	foreach($blacklist as $_ip){
		if(strpos($_ip,'/') !== false){
			preg_match($_ip,$_SESSION['ip']) AND exit($error);
		}else{
			$_SESSION['ip'] == $_ip AND exit($error);
		}
	}
}
//更新浏览量
if(!isset($_SESSION['isVisit'])){
	$_SESSION['isVisit'] = 1;
	dbUpdate('conf',['views'=>++$conf['views']]);
}

//文章列表
$articleList = db('article',[],['createTime'=>1]);
//标签列表
$tagList = getTag();
//分类列表
$categoryList = getCategory();
//导航链接
$navbarList = $conf['navbar'];
//友情链接
$linkList = $conf['link'];
//加载已安装的扩展
$extList = $conf['ext'];
foreach ($extList as $k => $v) {
	$commonPath = EXT.$k.'/common.php';
	if(is_file($commonPath)) include $commonPath;
}
foreach($hook['common'] as $fn) $fn();
$settingPage = [];
foreach ($extList as $k => $v) {
	$mainPath = EXT.$k.'/main.php';
	$settingPage[$k] = ($page == 'admin' && $adminPage == 'ext' && get(2) == 'setting' && get(3) == $k) ? 'admin/ext/setting/'.$k : '';
	if(is_file($mainPath)) include $mainPath;
}
//加载模板中的公共文件
$commonPath = TPLPATH.'common.php';
if(is_file($commonPath)) include $commonPath;
$settingPage[$conf['tpl']] = ($page == 'admin' && $adminPage == 'tpl' && get(2) == 'setting' && get(3) == $conf['tpl']) ? 'admin/tpl/setting/'.$conf['tpl'] : '';
$mainPath = TPLPATH.'main.php';
if(is_file($mainPath)) include $mainPath;
//页面路由
switch($page){

	//验证码
	case 'vcode':
		include LIB.'vcode.class.php';
		$code = new vcode($conf['vcode']['width'],$conf['vcode']['height'],$conf['vcode']['length']);
		$_SESSION['vcode'] = $code->getCode();
		echo $code->outimg();
		exit;

	//首页
	case 'index':
		$pageNum = get(1,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle(LOGIN?[]:['isPrivate'=>0],['createTime'=>1],'index/{page}',$pageNum,$pageSize);
		foreach($hook['model_index'] as $fn) $fn();
		include $tpl->view($page);
		break;

	//分类
	case 'category':
		$cid = get(1,'str');
		if(!$cid || empty($categoryList[$cid])) jump('index');
		$pageNum = get(2,'int',1);
		$pageSize = $conf['article']['paging'];
		$category = $categoryList[$cid];
		$article = getArticle(LOGIN?['cid'=>$cid]:['cid'=>$cid,'isPrivate'=>0],['createTime'=>1,'isTop'=>1],'category/'.$cid.'/{page}',$pageNum,$pageSize);
		foreach($hook['model_category'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'category.php')?'category':'index');
		break;

	//标签
	case 'tag':
		$tag = get(1,'urldecode');
		$cond = ['tag'=>['IN'=>$tag]];
		if(!LOGIN)$cond['isPrivate'] = 0;
		$pageNum = get(2,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle($cond,[],'tag/'.$tag.'/{page}',$pageNum,$pageSize);
		foreach($hook['model_tag'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'tag.php')?'tag':'index');
		break;

	//搜索页
	case 'search':
		$searchName = post('name','urldecode');
		$searchName && jump('search&name='.$searchName);
		$searchName = strip_tags(get('name','urldecode'));
		$cond = ['title'=>['LIKE'=>$searchName]];
		if(!LOGIN) $cond['isPrivate'] = 0;
		$pageNum = get(1,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle($cond,['createTime'=>1,'isTop'=>1],'search/{page}&name='.$searchName,$pageNum,$pageSize);
		foreach($hook['model_search'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'search.php')?'search':'index');
		break;

	//评论
	case 'comment':
		$action = get(1,'str');

		//删除留言
		if($action == 'delete'){
			checkLogin();
			$articleId = get(2,'str');
			$commentId = get(3,'int');
			if(!$articleId || !$commentId){
				foreach($hook['model_comment_delete_fail'] as $fn) $fn();
				!$articleId && prompt('文章ID不存在');
				!$commentId && prompt('评论ID不存在');
			}
			foreach($hook['model_comment_delete'] as $fn) $fn();
			delComment('comment/'.$articleId,$commentId);
			commentsInit($articleId);
			foreach($hook['model_comment_delete_success'] as $fn) $fn();
			jump($articleId.'#comment');
		}

		//添加留言
		if($method == 'POST'){
			$vcode = post('vcode','str');
			if($conf['vcode']['open'] && (!$vcode || strtolower($_SESSION['vcode']) !== strtolower((string)$vcode))){
				prompt('验证码不正确');
			}
			if($_SESSION['commentCount'] >= $conf['comment']['restrict']){
				prompt('每日评论次数不能超过'.$conf['comment']['restrict'].'次哦！');
			}
			$articleId = post('page','str');
			$path = 'comment/'.$articleId;
			if(!isset($articleList[$articleId])) prompt('该文章不存在');
			if(!$articleList[$articleId]['isComment']) prompt('该文章未开启评论留言');
			$name = trim(post('name','stripTags'));
			$contact = trim(post('contact','stripTags'));
			$content = trim(post('content','stripTags'));
			mb_strlen($name)>20 && prompt('名字不能超过20个字符');
			mb_strlen($contact)>20 && prompt('联系方式不能超过20个字符');
			mb_strlen($content) > 2000 && prompt('留言字数不能大于2000');
			$sensitive = explode(' ',trim($conf['sensitive']));
			foreach($sensitive as $_s){
				$_s = trim($_s);
				if($_s && strpos($content, $_s) !== false) {
					prompt('您的留言包含敏感信息: 【'.$_s.'】');
				}
			}
			$_SESSION['commentName'] = $name;
			$_SESSION['commentContact'] = $contact;
			$comment = db($path);
			$id = $comment ? $comment[count($comment)-1]['id'] + 1 : 1;
			if(!empty($content)){
				$post = [
					'id' => $id,
					'pid' => post('pid','int',0),
					'admin' => LOGIN,
					'name' => $name ? $name : ip(),
					'contact' => $contact,
					'content' => $content,
					'ip' => ip(),
					'time' => time()
				];
				$selComment = arrWhere($comment,['ip'=>ip(),'content'=>$content,'time'=>['>'=>strtotime(date('Y-m-d',time()))]]);
				if(count($selComment) > 20) prompt('禁止灌水');
				foreach($hook['model_comment_add'] as $fn) $fn();
				$comment[] = $post;
				dbSave($path,$comment);
				$conf['comment']['count'] += 1;
				conf('comment',$conf['comment']);
				$_SESSION['commentCount'] += 1;
				unset($_SESSION['vcode']);
				commentsInit($articleId);
				foreach($hook['model_comment_add_success'] as $fn) $fn();
				jump($articleId.'#comment-'.$id);
			}
			foreach($hook['model_comment_add_fail'] as $fn) $fn();
			prompt('留言内容不能为空');
		}
		break;

	//上传
	case 'upload':
		checkLogin();
		$arr = upload($_POST);
		exit(type($arr,'json'));
		break;

	//导入
	case 'import':
		checkLogin();
		if(isset($_FILES['file']) && $_FILES['file']){
			if(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) == 'sx'){
				$itype = get(1,'str','system');
				$sx = file_get_contents($_FILES['file']['tmp_name']);
				//id
				!preg_match('/\s*#\s*Id:\s*(.+)/', $sx, $id) && err('应用缺少Id');
				!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_-]+$/',$id[1]) && err('应用Id格式不正确');
				!check($id[1],'length',1,30) && err('应用Id长度限制1-30');
				//type
				!preg_match('/\s*#\s*Type:\s*(.+)/', $sx, $type) && err('应用缺少Type');
				if($type[1] !== 'tpl' && $type[1] !== 'ext' && $type[1] !== 'system') err('应用Type不正确');
				if(preg_match('/\s*#\s*Limit:\s*(.+)/', $sx, $limit)){
					$v1 = str_replace('.','',$limit[1]);
					$v2 = str_replace('.','',V);
					if((int)$v1 > (int)$v2) err('你的清雨版本太低，无法安装使用，需要升级到v'.$limit[1].'及以上');
				}
				foreach($hook['model_import'] as $fn) $fn();
				if($itype == 'tpl'){
					unsx($sx,ROOT.'tpl/'.$id[1].'/');
				}elseif($itype == 'ext'){
					unsx($sx,EXT.$id[1].'/');
				}else{
					unsx($sx);
				}
				unlink($_FILES['file']['tmp_name']);
				msg('导入成功');
			}
		}
		err('导入失败');
		break;

	//提示
	case 'prompt':
		if(empty($_SESSION['prompt'])) jump();
		$prompt = $_SESSION['prompt']['text'];
		$url = $_SESSION['prompt']['url'];
		foreach($hook['model_prompt'] as $fn) $fn();
		include $tpl->view($page);
		unset($_SESSION['prompt']);
		break;
	
	//登录
	case 'login':
		if(is_file(TPLPATH.'login.php')){
			include $tpl->view('login');
		}else{
			jump('admin/login');
		}
		break;

	//后台
	case 'admin':
		$adminPage = get(1,'string','index');

		//模板编译
		$adminTpl = new Tpl([
			'path' => '/lib/',
			'name' => 'admin',
			'compile' => $conf['compile'],
		]);

		foreach($hook['admin_model_common'] as $fn) $fn();

		//登录
		if($adminPage == 'login'){
			$conf['title'] = $conf['title'].'-登录';
			if($method == 'POST'){
			    if($_SESSION['loginCount'] > 9 && time()-$_SESSION['loginTime']<1800) prompt('密码错误次数太多啦，请君好好想想，半小时后再登录吧！');
				$password = post('password','str');
				$vcode = post('vcode','str');
				!$password && prompt('密码不能为空');
				if($conf['vcode']['open']){
					!$vcode && prompt('验证码不能为空');
					strtolower($_SESSION['vcode']) !== strtolower($vcode) && prompt('验证码错误');
				}
				if(md5($password) === (string)$conf['password']){
					$_SESSION['login'] = true;
					$_SESSION['token'] = getToken();
					$loginTime = time();
					$loginPayload = $loginTime.'|'.ip().'|'.(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
					$loginSign = hash_hmac('sha256', $loginPayload, $conf['password']);
					setcookie($loginCookieName, $loginTime.':'.$loginSign, $loginTime + 2592000, $loginCookiePath, '', isHttps(), true);
					foreach($hook['admin_model_login_success'] as $fn) $fn();
				}else{
					$_SESSION['loginCount'] += 1;
					$_SESSION['loginTime'] = time();
					foreach($hook['admin_model_login_fail'] as $fn) $fn();
					prompt('密码错误');
				}
				jump('admin/index');
			}
			exit(include $adminTpl->view('login'));
		}

		//提示
		if($adminPage == 'prompt'){
			if(empty($_SESSION['prompt'])) jump('admin');
			$prompt = $_SESSION['prompt']['text'];
			$url = $_SESSION['prompt']['url'];
			include $adminTpl->view('prompt');
			unset($_SESSION['prompt']);
			exit;
		}

		//后台路由
		switch($adminPage){

			//登出
			case 'logout':
				setcookie($loginCookieName, '', time() - 3600, $loginCookiePath, '', isHttps(), true);
				session_destroy();
				jump();

			//查看phpinfo
			case 'phpinfo':
				print_r(phpinfo());
				exit;
			
			//查看配置
			case 'config':
				echo '<!DOCTYPE html><html lang="zh-Hans"><head><meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/><title>server</title></head><body style="font-size:13px;display:flex;"><pre style="flex:0 0 50%;width:50%;white-space:pre-wrap;padding:10px;word-break:break-all;box-sizing:border-box;">'."\nconfig:\n";
				print_r($conf);
				echo '</pre><pre style="flex:0 0 50%;width:50%;white-space:pre-wrap;padding:10px;word-break:break-all;box-sizing:border-box;">'."\nserver:\n";
				print_r($_SERVER);
				echo '</pre></body></html>';
				exit;

			//检查更新
			case 'checkUpdate':
				ret(checkUpdate());
			
			//数据库版本更新
			case 'dbUpdate':
				$data = curl(API_HOST.'getDb');
				if($data){
					dbSync(type($data,'array'));
					$conf = db('conf');
					delCompile();
					msg('更新成功');
				}
				err('更新失败');

			//核心文件更新
			case 'update':
				$data = curl(API_HOST.'getSystem');
				if($data){
					delFile(ROOT.'update.php');
					unsx($data);
					$list = db('list');
					if(!$conf['db'] || $conf['db']['version'] != $list['#version']) dbSync($list);
					delCompile();
					if(is_file(ROOT.'update.php')){
						include ROOT.'update.php';
						delFile(ROOT.'update.php');
					}
					msg('更新成功啦！记得清理浏览器缓存刷新一下哦！');
				}
				err('更新失败');

			//首页
			case 'index':
				$_SESSION['updateAlert'] = isset($_SESSION['updateAlert']) ? 0 : 1;
				include $adminTpl->view('index');
				break;
			
			//导航设置
			case 'navbar':
				if($method == 'POST'){
					$name = post('name');
					$url = post('url');
					$target = post('target','array',[]);
					$post = [];
					if($name){
						foreach($name as $k => $v){
							!$v && err('名称不能为空');
							!$url[$k] && err('链接不能为空');
							$navbar = [
								'name' => $v,
								'url' => $url[$k],
								'target' => in_array((string)$k,$target) ? 1 : 0,
								'child' => [],
							];
							foreach($hook['admin_model_navbar_item'] as $fn) $fn();
							$post[] = $navbar;
						}

					}
					foreach($hook['admin_model_navbar'] as $fn) $fn();
					conf('navbar',$post);
					msg('保存成功');
				}
				include $adminTpl->view('navbar');
				break;

			//分类管理
			case 'category':
				if($method == 'POST'){
					$id = post('id');
					$newId = post('newId');
					$name = post('name');
					$intro = post('intro');
					$delId = post('delId');
					$post = [];
					$isModify = 0;
					if($newId){
						$modifyId = [];
						foreach($newId as $k => $v){
							!$name[$k] && err('名称不能为空');
							!$v && err('别称不能为空');
							// 判断分类是否已修改
							if($id[$k] && $v !== $id[$k]){
								$modifyId[$id[$k]] = $v;
							}
							$category = [
								'id' => $v,
								'name' => $name[$k],
								'intro' => $intro[$k],
								'count' => 0,
							];
							foreach($hook['admin_model_category_item'] as $fn) $fn();
							$post[$v] = $category;
						}
						if($modifyId){
							foreach($articleList as $k => $v){
								if(isset($modifyId[$v['cid']])){
									$isModify = 1;
									$articleList[$k]['cid'] = $modifyId[$v['cid']];
								}
							}
						}
					}
					if($delId){
						foreach($articleList as $k => $v){
							if(in_array($v['cid'],(array)$delId)){
								$isModify = 1;
								$articleList[$k]['cid'] = '';
							}
						}
					}
					if($isModify) dbSave('article',$articleList);
					foreach($hook['admin_model_category'] as $fn) $fn();
					conf('category',$post);
					categoryInit();
					msg('保存成功');
				}
				include $adminTpl->view('category');
				break;
			
			//友情链接
			case 'link':
				if($method == 'POST'){
					$name = post('name');
					$url = post('url');
					$target = post('target','array',[]);
					$post = [];
					if($name){
						foreach($name as $k => $v){
							!$v && err('名称不能为空');
							!$url[$k] && err('链接不能为空');
							$post[] = [
								'name' => $v,
								'url' => $url[$k],
								'target' => in_array((string)$k,$target) ? 1 : 0
							];
						}
					}
					foreach($hook['admin_model_link'] as $fn) $fn();
					conf('link',$post);
					msg('保存成功');
				}
				include $adminTpl->view('link');
				break;

			//文章管理
			case 'article':
				$type = get(2,'str');

				//分类
				if($type == 'category'){
					$cid = get(3,'urldecode');
					$pageNum = get(4,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(['cid'=>$cid],[],'admin/article/category/'.$cid.'/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article_category'] as $fn) $fn();
					include $adminTpl->view('article');
					break;
				}
				
				//标签
				if($type == 'tag'){
					$tag = get(3,'urldecode');
					$pageNum = get(4,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(['tag'=>['IN'=>$tag]],[],'admin/article/tag/'.$tag.'/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article_tag'] as $fn) $fn();
					include $adminTpl->view('article');
					break;
				}

				//搜索
				if($type == 'search'){
					$searchName = get(3,'urldecode');
					$pageNum = get(4,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(['title'=>['LIKE'=>$searchName]],[],'admin/article/search/'.$searchName.'/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article_search'] as $fn) $fn();
					include $adminTpl->view('article');
					break;
				}

				//批量删除
				elseif($type == 'delete'){
					$id = post('id');
					if(!$id) {
						$id = get(3,'string');
						$id = $id ? [$id] : 0;
					}
					if($id){
						$errorCount = 0;
						foreach ($id as $v) {
							if(isset($articleList[$v])){
								delContentFiles(db($articleList[$v]['path']));
								delFile(dbPath($articleList[$v]['path']));
								delFile(dbPath('comment/'.$v));
								unset($articleList[$v]);
							}else{
								$errorCount += 1;
							}
						}
						foreach($hook['admin_model_article_delete'] as $fn) $fn();
						$conf['article']['count'] = count($articleList);
						conf('article',$conf['article']);
						dbSave('article',$articleList);
						tagInit();
						categoryInit();
						$errorCount ? err($errorCount.'篇文章删除失败') : msg('删除成功');
					}
					err('删除失败');
				}

				//移动分类和标签
				elseif($type == 'move'){
					$id = post('id');
					$cid = post('cid','str');
					$com = get(3,'str');
					if($com == 'tag'){
						if($id && $cid){
							foreach ($id as $v) {
								if(isset($articleList[$v])){
									$articleList[$v]['tag'] = [$cid];
								}
							}
							foreach($hook['admin_model_article_move_tag'] as $fn) $fn();
							dbSave('article',$articleList);
							tagInit();
							msg('操作成功');
						}
					}elseif($com == 'category'){
						if($id && $cid){
							foreach ($id as $v) {
								if(isset($articleList[$v])){
									$articleList[$v]['cid'] = $cid;
								}
							}
							foreach($hook['admin_model_article_move_category'] as $fn) $fn();
							dbSave('article',$articleList);
							categoryInit();
							msg('操作成功');
						}
					}
					err('操作失败');
				}

				//创建
				elseif($type == 'create'){
					if($method == 'POST'){
						$title = trim((string)post('title','stripTags'));
						$content = trim((string)post('content'));
						$id = trim((string)post('id'));
						$id = empty($id)?'T'.time():$id;
						if(!$title) prompt('标题不能为空');
						if(!$content) prompt('内容不能为空');
						if(isset($articleList[$id])) prompt('已存在该URL名称');
						$tag = post('tag','trim');
						$tag = $tag ? preg_split('/\s+/', (string)$tag) : [];
						$intro = post('intro','string');
						if(!$intro){
							$fk = new fk($content);
							$html = preg_replace('/<[^>]+>/i','',$fk->html);
							$html = preg_replace('/[\r\n]+/','',$html);
							$intro =  mb_substr($html,0,$conf['brief'],'utf-8');
						}
						// 缩略图
						$img = '';
						if($conf['thumb']['open']){
							$img = getContentImg($content);
							$img = $img ? imgThumb($img[0], $conf['thumb']['width'], $conf['thumb']['height'], $conf['thumb']['type']==1) : '';
						}
						$path = 'article/'.$id;
						if(dbSave($path,$content)){
							$post = [
								'id'=>$id,
								'cid'=>post('cid','trim'),
								'path'=>$path,
								'title'=>$title,
								'intro'=>$intro,
								'img'=>$img,
								'tag'=>$tag,
								'isTop'=>post('isTop','int',0),
								'isPrivate'=>post('isPrivate','int',0),
								'isComment'=>post('isComment','int',0),
								'isFk'=>post('isFk','int',0),
								'views'=>0,
								'comments'=>0,
								'updateTime'=>time(),
								'createTime'=>time(),
							];
							foreach($hook['admin_model_article_create'] as $fn) $fn();
							$articleList[$id] = $post;
							$conf['article']['count'] = count($articleList);
							conf('article',$conf['article']);
							if(!dbSave('article',$articleList)){
								dbDelete($path);
							}
							tagInit();
							categoryInit();
							foreach($hook['admin_model_article_create_success'] as $fn) $fn();
						}else{
							foreach($hook['admin_model_article_create_fail'] as $fn) $fn();
							prompt('保存失败');
						}
						jump('admin/article');
					}
					include $adminTpl->view('article.create');
				}

				//编辑
				elseif($type == 'editor'){
					$id = get(3,'str');
					$article = getArticle(['id'=>$id]);
					if($article['list']){
						$article = $article['list'][$id];
					}else{
						prompt('没有该数据');
					}
					$article['content'] = db($article['path']);
					if($method == 'POST'){
						$title = trim((string)post('title','stripTags'));
						$content = trim((string)post('content'));
						if(!$title) prompt('标题不能为空');
						if(!$content) prompt('内容不能为空');
						$intro = post('intro','string');
						if(!$intro){
							$fk = new fk($content);
							$html = preg_replace('/<[^>]+>/i','',$fk->html);
							$html = preg_replace('/[\r\n]+/','',$html);
							$intro =  mb_substr($html,0,$conf['brief'],'utf-8');
						}
						// 缩略图
						$img = '';
						if($conf['thumb']['open']){
							$img = getContentImg($content);
							$img = $img ? imgThumb($img[0], $conf['thumb']['width'], $conf['thumb']['height'], $conf['thumb']['type']==1) : '';
						}
						//判断编辑的文档是不是为上个文档的url，不是的话，删除旧有的数据，建立新数据
						$name = post('id','trim');
						$newId = empty($name)?'T'.time():$name;
						$tag = post('tag','trim');
						$tag = $tag ? preg_split('/\s+/', (string)$tag) : [];
						$isRename = $newId != $id;
						$oldPath = $article['path'];
						if($isRename){
							if(isset($articleList[$newId])) prompt('已存在该URL名称');
							if(is_file(dbPath('comment/'.$newId))) prompt('已存在该URL名称的评论数据');
							$article['path'] = 'article/'.$newId;
						}
						if(dbSave($article['path'],$content)){
							$post = [
								'id'=>$newId,
								'cid'=>post('cid','trim'),
								'path'=>$article['path'],
								'title'=>$title,
								'intro'=>$intro,
								'img'=>$img,
								'tag'=>$tag,
								'isTop'=>post('isTop','int',0),
								'isPrivate'=>post('isPrivate','int',0),
								'isComment'=>post('isComment','int',0),
								'isFk'=>post('isFk','int',0),
								'views'=>$article['views'],
								'comments'=>$article['comments'],
								'updateTime'=>time(),
								'createTime'=>$article['createTime'],
							];
							foreach($hook['admin_model_article_editor'] as $fn) $fn();
							if($isRename) unset($articleList[$id]);
							$articleList[$newId] = $post;
							if(!dbSave('article',$articleList)){
								dbDelete($article['path']);
								prompt('编辑失败');
							}
							if($isRename){
								delFile(dbPath($oldPath));
								if(is_file(dbPath('comment/'.$id))){
									$util->rename(DB.'comment/'.$id.'.php',DB.'comment/'.$newId.'.php',false);
								}
							}
							tagInit();
							categoryInit();
							foreach($hook['admin_model_article_editor_success'] as $fn) $fn();
						}else{
							foreach($hook['admin_model_article_editor_fail'] as $fn) $fn();
							prompt('编辑失败');
						}
						jump('admin/article');
					}
					include $adminTpl->view('article.editor');
				}else{
					$pageNum = get(2,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle([],['createTime'=>1,'isTop'=>1],'admin/article/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article'] as $fn) $fn();
					include $adminTpl->view('article');
				}
				break;
			
			//基础设置
			case 'setting':
				$tplList = getTpl();
				if($method == 'POST'){
					$conf['title'] = post('title','str','');
					$conf['name'] = post('name','str','');
					$conf['intro'] = post('intro','str','');
					$conf['mood'] = post('mood','str','');
					$conf['key'] = post('key','str','');
					$conf['desc'] = post('desc','str','');
					$conf['brief'] = post('brief','int',0);
					$conf['avatar'] = post('avatar','str','');
					$conf['username'] = post('username','str','');
					$password = post('password','str');
					$conf['password'] = strlen((string)$password) ? md5((string)$password) : $conf['password'];
					$compile = post('compile','bool');
					if($compile !== $conf['compile']){
						$conf['compile'] = $compile;
						delCompile();
					}
					$conf['debug'] = post('debug','int',$conf['debug']);
					$rewrite = post('rewrite','bool');
					if($rewrite !== $conf['rewrite']){
						$conf['rewrite'] = $rewrite;
						delCompile();
					}
					$conf['comment']['restrict'] = post('commentRestrict','int',$conf['comment']['restrict']);
					$conf['comment']['paging'] = post('commentPaging','int',$conf['comment']['paging']);
					$conf['article']['paging'] = post('articlePaging','int',$conf['article']['paging']);
					$conf['vcode']['open'] = post('vcodeOpen','bool',false);
					$conf['vcode']['width'] = post('vcodeWidth','int',86);
					$conf['vcode']['height'] = post('vcodeHeight','int',28);
					$conf['vcode']['length'] = post('vcodeLength','int',4);
					$conf['thumb']['open'] = post('thumbOpen','bool',false);
					$conf['thumb']['width'] = post('thumbWidth','int',300);
					$conf['thumb']['height'] = post('thumbHeight','int',300);
					$conf['thumb']['type'] = post('thumbType','int',1);
					$conf['icp'] = post('icp','str','');
					$conf['prn'] = post('prn','str','');
					$conf['views'] = post('views','int',0);
					$conf['sensitive'] = post('sensitive','str','');
					$conf['blacklist'] = post('blacklist','str','');
					$conf['js'] = post('js','str','');
					foreach($hook['admin_model_setting'] as $fn) $fn();
					conf($conf);
					header('Location:'.HOME.($conf['rewrite']?'':'?').'admin/setting');
				}
				include $adminTpl->view('setting');
				break;

			//编译模板
			case 'compile':
				delCompile();
				jump('admin/tpl');

			//主题管理
			case 'tpl':
				$tpl = getTpl();
				$tplPage = get(2,'str');
				$tplId = get(3,'str');
				foreach($hook['admin_model_tpl'] as $fn) $fn();
				if($tplId && isset($tpl['list'][$tplId])){
					if($tplPage == 'install'){
						$conf = db('conf');
						//先执行当前主题的卸载程序
						foreach($hook['admin_model_tpl_uninstall'] as $fn) $fn();
						$uninstallPath = TPLPATH.'uninstall.php';
						if(is_file($uninstallPath)) include $uninstallPath;
						$conf['tpl'] = $tplId;

						//最后执行新主题的安装程序
						foreach($hook['admin_model_tpl_install'] as $fn) $fn();
						$installPath = ROOT.'tpl/'.$conf['tpl'].'/install.php';
						if(is_file($installPath)) include $installPath;
						conf('tpl',$conf['tpl']);
					}
					elseif($tplPage == 'delete'){
						foreach($hook['admin_model_tpl_delete'] as $fn) $fn();
						$util->delete(ROOT.'tpl/'.$tplId);
					}
					elseif($tplPage == 'download'){
						foreach($hook['admin_model_tpl_download'] as $fn) $fn();
						$sx = sx(ROOT.'tpl/'.$tplId);
						Header('Content-type: application/octet-stream');
						Header('Accept-Ranges: bytes');
						header('Content-Disposition: attachment; filename='.$tplId.'.sx');
						exit($sx);
					}
					elseif($tplPage == 'setting'){
						if($tplId && $tpl['list'][$tplId]['setting']){
							$settingTpl = new Tpl([
								'path' => '/tpl/',
								'name' => $tplId
							]);
							include $settingTpl->view('setting');
							exit;
						}
					}
					jump('admin/tpl');
				}
				include $adminTpl->view('tpl');
				break;

			//扩展管理
			case 'ext':
				$ext = getExt();
				$extPage = get(2,'str');
				$extId = get(3,'str');
				foreach($hook['admin_model_ext'] as $fn) $fn();
				if($extId && isset($ext['list'][$extId])){
					$extConfPath = EXT.$extId.'/conf.php';
					$extConf = include $extConfPath;
					if($extPage == 'install'){
						if(!isset($conf['ext'][$extId])){
							$conf['ext'][$extId] = 1;
							foreach($hook['admin_model_ext_install'] as $fn) $fn();
							conf('ext',$conf['ext']);
							$installPath = EXT.$extId.'/install.php';
							if(is_file($installPath)) include $installPath;
						}
					}
					elseif($extPage == 'uninstall'){
						if(isset($conf['ext'][$extId])){
							unset($conf['ext'][$extId]);
							foreach($hook['admin_model_ext_uninstall'] as $fn) $fn();
							conf('ext',$conf['ext']);
							$uninstallPath = EXT.$extId.'/uninstall.php';
							if(is_file($uninstallPath)) include $uninstallPath;
						}
					}
					elseif($extPage == 'delete'){
						foreach($hook['admin_model_ext_delete'] as $fn) $fn();
						$util->delete(EXT.$extId);
					}
					elseif($extPage == 'download'){
						foreach($hook['admin_model_ext_download'] as $fn) $fn();
						$sx = sx(EXT.$extId);
						Header('Content-type: application/octet-stream');
						Header('Accept-Ranges: bytes');
						header('Content-Disposition: attachment; filename='.$extId.'.sx');
						exit($sx);
					}
					elseif($extPage == 'setting'){
						if($extId && $ext['list'][$extId]['setting']){
							$settingTpl = new Tpl([
								'path' => '/ext/',
								'name' => $extId
							]);
							include $settingTpl->view('setting');
							exit;
						}
					}
					jump('admin/ext');
				}
				include $adminTpl->view('ext');
				break;

			//错误日志
			case 'error':
				if(get(2) == 'delete'){
					dbSave('error',[]);
					jump('admin/error');
				}
				$pageNum = get(2,'int',1);
				$pageSize = 30;
				$error = getError([],['time'=>1],'admin/error/{page}', $pageNum, $pageSize);
				foreach($hook['admin_model_error'] as $fn) $fn();
				include $adminTpl->view('error');
				break;

			//其它
			default:
				foreach($hook['admin_model_default_page'] as $fn) $fn();
				!$_SESSION['includeTheme'] && prompt('没有该数据');
		}
		break;

	//其它
	default:
		foreach($hook['model_default_page'] as $fn) $fn();
		$id = get(0,'string');
		if(isset($articleList[$id])){
			$articleList[$id]['isPrivate'] && !LOGIN && prompt('游客无法访问私密文章，请登录！');
		}else{
			$_SESSION['includeTheme'] ? exit : prompt('没有该数据');
		}
		$page = 'page';
		$article = getArticle($id);

		//上一篇、下一篇
		$keyList = array_keys(arrWhere($articleList,['isPrivate'=>0], ['createTime'=>1,'isTop'=>1]));
		$current = array_search($id,$keyList);
		$prevId = $current ? $keyList[$current-1] : false;
		$nextId = $current+1 < count($keyList) ? $keyList[$current+1] : false;
		$article['prev'] = $prevId ? getArticle($prevId) : false;
		$article['next'] = $nextId ? getArticle($nextId) : false;

		//网站描述和标题
		$conf['desc'] = $article['intro'];
		$conf['title'] = $article['title'].'-'.$conf['title'];

		//留言板
		$pageNum = get(1,'int',1);
		$pageSize = $conf['comment']['paging'];
		$comment = getComment($id,[],[],$id.'/{page}#comment',$pageNum,$pageSize);

		//更新浏览量
		if(!in_array($id,$_SESSION['views'])){
			$_SESSION['views'][]=$id;
			$articleList[$id]['views'] += 1;
			dbSave('article',$articleList);
		}
		foreach($hook['model_default_page_filter'] as $fn) $fn();
		include $tpl->view($page);
}
?>
