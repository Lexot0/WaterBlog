<?php
// 应用中心
if(($page == 'admin' && $adminPage == 'app') || $page == 'pay'){
	//用户key
	$keyPath = EXT.'app/key.php';
	if(!is_file($keyPath)) save($keyPath,['host'=>'','key'=>'']);
	$userKey = include $keyPath;

	//验证host
	if(getHost().URL !== $userKey['host']){
		$userKey = ['host'=>getHost().URL,'key'=>''];
		save($keyPath,$userKey);
	}

	/**
	 * 错误提示
	 * @param string $msg
	 */
	function errorMsg($msg){
		exit('<h3 class="center">'.$msg.'</h3>');
	}

	/**
	 * 获取官方应用中心数据
	 * @param  string $url
	 * @param  bool|array|string $params
	 * @return string
	 */
	function getApi($url,$params=[]){
		global $conf,$userKey;
		$tpl = getTpl();
		$ext = getExt();
		$res = curl('https://prain.cn/api/'.$url,array_merge($userKey,[
			'appVersion' => '1.1.0',
			'system'=>[
				'version'=>V,
				'dbVersion'=>$conf['db']['version']
			],
			'url'=>URL,
			'tpl'=>$tpl['list'],
			'ext'=>$ext['list']
		],$params));
		$arr = type($res,'array');
		if(type($arr) == 'array'){
			if($arr['error'] &&  $arr['data']=== 4001){
				jump('admin/app/login');
			}
			return $arr;
		}
		return $res;
	}

	//支付
	if($page == 'pay'){
		$payType = get(1,'str');
		$id = get(2,'str');
		!$id && errorMsg('ID参数不能为空');
		if($payType == 'create'){
			$res = getApi('pay/create',['id'=>$id]);
			$res['error'] && err($res['message']);
			// 微信支付：href($res['data']['pay_url']);
			ret($res['data']['pay_url']);
		}
		elseif($payType == 'select'){
			$res = getApi('pay/select',['id'=>$id]);
			if($res['error']){
				err($res['message']);
			}else{
				$res['data']['state'] ? msg($res['data']['orderId']) : err('未支付');
			}
		}
		exit;
	}
}
if($page == 'admin' && $adminPage == 'app'){
	//模板编译
	$appTpl = new Tpl([
		'path' => '/ext/',
		'name' => 'app',
		'compile' => $conf['compile'],
	]);

	//页面路由
	$appPage = get(2,'str','tpl');
	$appType = get(3,'str','index');

	//登录限制
	if(!LOGIN) jump('admin/login');

	//登录
	if($appPage == 'login'){
		if($method == 'POST'){
			$form = post('form','int',1);
			$username = post('username','str');
			$password = post('password','str');
			$arr = getApi('login',['username'=>$username,'password'=>$password,'form'=>$form]);
			if(!$arr['error']){
				$userKey['key'] = $arr['data'];
				save($keyPath,$userKey);
				msg($arr['message']);
			}
			err($arr['message']);
		}
		$html = getApi('login');
		include $appTpl->view('app');
	}
	//注册账号
	elseif($appPage == 'register'){
		$html = getApi('register',post());
		if($method == 'POST'){
			$html['error'] ? err($html['message']) : msg($html['message']);
		}
		include $appTpl->view('app');
	}
	//忘记账号、忘记密码
	elseif($appPage == 'forget'){
		$type = get(3,'str','username');
		$hash = get(4,'str','');
		$post = post();
		if($hash) $post['hash'] = $hash;
		$html = getApi('forget/'.$type, $post);
		if($method == 'POST'){
			$html['error'] ? err($html['message']) : ret($html['data'],$html['message']);
		}
		include $appTpl->view('app');
	}
	//退出
	elseif($appPage == 'logout'){
		$userKey['key'] = '';
		save($keyPath,$userKey);
		jump('admin/app/user');
	}
	//主题列表
	if($appPage == 'tpl'){
		$pageNum = get(3,'int',1);
		$pageSize = 30;
		$html = getApi('tpl',['page'=>$pageNum,'size'=>$pageSize]);
		include $appTpl->view('app');
	}
	//扩展列表
	elseif($appPage == 'ext'){
		$pageNum = get(3,'int',1);
		$pageSize = 30;
		$html = getApi('ext',['page'=>$pageNum,'size'=>$pageSize]);
		include $appTpl->view('app');
	}
	//应用详情
	elseif($appPage == 'view'){
		$id = get(3,'string');
		$html = getApi('view',['id'=>$id]);
		include $appTpl->view('app');
	}
	//应用评论
	elseif($appPage == 'comment'){
		$id = post('id','string');
		$pid = post('pid','int',0);
		$content = post('content','string');
		$res = getApi('comment',['id'=>$id,'pid'=>$pid,'content'=>$content]);
		$res['error'] ? err($res['message']) : msg($res['message']);
	}
	//应用评论删除
	elseif($appPage == 'deleteComment'){
		$id = post('id','int',0);
		$res = getApi('deleteComment',['id'=>$id]);
		$res['error'] ? err($res['message']) : msg($res['message']);
	}
	//用户中心
	elseif($appPage == 'user'){
		$param = [];
		//账号设置
		if($appType == 'settingUpdate'){
			if($method == 'POST'){
				$res = getApi('user/'.$appType,$_POST);
				$res['error'] ? err($res['message']) : msg($res['message']);
			}
		}
		$param['tab'] = get(3,'string','install');
		$html = getApi('user/'.$appType,$param);
		include $appTpl->view('app');
	}
	//申请成为开发者
	elseif($appPage == 'apply'){
		if($method == 'POST'){
			$res = getApi('apply-developer');
			$res['error'] ? err($res['message']) : msg($res['message']);
		}
		$html = getApi('apply');
		include $appTpl->view('app');
	}
	//开发者中心
	elseif($appPage == 'developer'){
		$tab = get(3,'string','tpl');
		$html = getApi('developer',['tab'=>$tab]);
		include $appTpl->view('app');
	}
	//应用管理
	elseif($appPage == 'manage'){
		$tab = get(3,'string','tpl');
		$html = getApi('manage',['tab'=>$tab]);
		include $appTpl->view('app');
	}
	//销售记录
	elseif($appPage == 'sales'){
		$html = getApi('sales',[
			'page'=> get(3,'int',1)
		]);
		include $appTpl->view('app');
	}
	//我的提现
	elseif($appPage == 'withdraw'){
		if($method == 'POST'){
			$res = getApi('withdraw-submit',['amount'=>post('amount','float',0)]);
			$res['error'] ? err($res['message']) : msg($res['message']);
		}
		$html = getApi('withdraw',[
			'page'=> get(3,'int',1)
		]);
		include $appTpl->view('app');
	}
	//发布新应用
	elseif($appPage == 'publish'){
		$tab = get(3,'str','upload');
		if($tab === 'upload'){
			if($method == 'POST'){
				if(isset($_FILES['file'])){
					$file = $_FILES['file'];
					$sx = file_get_contents($file['tmp_name']);
					if($sx){
						//清空sx文件夹
						$util->delete(EXT.'app/tmp');
						$util->createDir(EXT.'app/tmp');
						unsx($sx,EXT.'app/tmp/');
						unlink($file['tmp_name']);
						$confPath = EXT.'app/tmp/conf.php';
						!is_file($confPath) && err('应用缺少配置文件');
						!is_file(EXT.'app/tmp/icon.png') && err('应用缺少主图');

						$appConf = include $confPath;
						!isset($appConf['id']) && err('id不能为空');
						!isset($appConf['type']) && err('type不能为空');
						!isset($appConf['author']) && err('author不能为空');
						!isset($appConf['name']) && err('name不能为空');
						!isset($appConf['intro']) && err('intro不能为空');
						!isset($appConf['price']) && err('price不能为空');
						!isset($appConf['home']) && err('home不能为空');
						!isset($appConf['version']) && err('version不能为空');
						!isset($appConf['limit']) && err('limit不能为空');
						ret($appConf['id']);
					}
				}
				err('上传失败');
			}
		}elseif($tab === 'submit'){
			$id = get(4,'str');
			if(!$id) jump('admin/app/publish');
			$confPath = EXT.'app/tmp/conf.php';
			//判断缓存中有没有数据，针对刷新当前页面
			if(is_file($confPath)){
				$publishPath = EXT.'app/publish/'.$id;
				//删除旧数据
				$util->delete($publishPath);
				//创建新数据
				$util->cut(EXT.'app/tmp/',$publishPath);
			}
			$confPath = EXT.'app/publish/'.$id.'/conf.php';
			if(!is_file($confPath)) jump('admin/app/publish');
			$app = include $confPath;
			$html = getApi('publish-view',['tab'=>$tab,'app'=>$app]);
			include $appTpl->view('app');
			exit;
		}
		$html = getApi('publish',['tab'=>$tab]);
		include $appTpl->view('app');
	}
	//发布新应用
	elseif($appPage == 'publish-submit'){
		if($method == 'POST'){
			$id = post('id','str');
			$publishPath = EXT.'app/publish/'.$id;
			$confPath = $publishPath.'/conf.php';
			$iconPath = $publishPath.'/icon.png';
			!is_file($confPath) && err('应用程序不存在');
			$view = include $confPath;

			$info = [];
			$info['id'] = $id;
			$info['type'] = $view['type'];
			$info['author'] = $view['author'];
			$info['name'] = post('name','str');
			$info['intro'] = post('intro','str');
			$info['price'] = post('price','float',0);
			$info['home'] = post('home','str');
			$info['version'] = post('version','str');
			$info['limit'] = post('limit','str');
			if(save($confPath,$info)){
				$sx = sx($publishPath);
				$icon = file_get_contents($iconPath);
				$res = getApi('publish-upload',['sx'=>$sx,'icon'=>$icon,'conf'=>$info,'content'=>post('content','str')]);
				$res['error'] && err($res['message']);
				$util->delete($publishPath);
				ret($res['message']);
			}
			err('发布失败，请检查');
		}

	}
	//应用编辑
	elseif($appPage == 'editor'){
		$id = get(3,'str');
		if($method == 'POST'){
			$name = post('name','str');
			$version = post('version','str');
			$limit = post('limit','str');
			$home = post('home','str');
			$intro = post('intro','str');
			$price = post('price','float',0);
			$content = post('content','str');
			$res = getApi('editorForm',[
				'id'=>$id,
				'name'=>$name,
				'home'=>$home,
				'intro'=>$intro,
				'price'=>$price,
				'version'=>$version,
				'limit'=>$limit,
				'content'=>$content,
			]);
			$res['error'] ? err($res['message']) : msg($res['message']);
		}
		$html = getApi('editor',['id'=>$id]);
		include $appTpl->view('app');
	}
	//应用上架 下架 删除
	elseif($appPage == 'put' || $appPage == 'pull' || $appPage == 'del'){
		$type = get(3,'str');
		$id = get(4,'str');
		$res = getApi($appPage,['type'=>$type,'id'=>$id]);
		$res['error'] ? err($res['message']) : msg($res['message']);
	}
	//应用主图上传到本地
	elseif($appPage == 'upload'){
		$id = get(3,'str');
		if($method == 'POST'){
			if(isset($_FILES['file'])){
				$file = $_FILES['file'];
				if($file['type'] !== 'image/png') err('只能上传png格式的主图哦');
				$img = file_get_contents($file['tmp_name']);
				$util->createFile(EXT.'app/publish/'.$id.'/icon.png',$img);
				unlink($file['tmp_name']);
				msg('上传成功');
			}
		}
		err('上传失败');
	}
	//应用主图上传到官网并发布
	elseif($appPage == 'upload-publish'){
		$id = get(3,'str');
		if($method == 'POST'){
			if(isset($_FILES['file'])){
				$file = $_FILES['file'];
				if($file['type'] !== 'image/png') err('只能上传png格式的主图哦');
				$img = file_get_contents($file['tmp_name']);
				unlink($file['tmp_name']);
				$res = getApi('upload',['id'=>$id,'img'=>$img]);
				$res['error'] ? err($res['message']) : msg($res['message']);
			}
		}
		err('上传失败');
	}
	//系统
	elseif($appPage == 'system'){
		$html = getApi('system');
		include $appTpl->view('app');
	}
	//安装
	elseif($appPage == 'install'){
		$id = get(3,'str');
		$res = getApi('download',['id'=>$id]);
		$res['error'] && err($res['message']);
		unsx($res['data']['sx'],ROOT.$res['data']['type'].'/'.$id.'/');
		$tpl->compile();
		msg('安装成功');
	}
	// exit;
}
?>