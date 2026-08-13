<?php
/**
 * 模板编译类
 * 创建：2018-11-05
 * 更新：2023-06-14
 */
class Tpl{
	private $theme_path;
	private $theme_name;
	private $theme_compile;
	private $path;
	public function __construct($conf = []){
		$this->theme_path = $conf['path'];
		$this->theme_name = $conf['name'];
		$this->theme_compile = isset($conf['compile'])?$conf['compile']:true;
		$this->path = ROOT.$this->theme_path.$this->theme_name.'/';
	}
	/**
	 * 返回编译后的模板路径
	 * @param string $fileName
	 * @param bool $isPath 是否为绝对路径
	 * @return string
	 */
	public function view($fileName,$isPath = false){
		$compileFile = $this->path.'compile/'.$fileName.'.php';
		if($isPath){
			$info = pathinfo($fileName);
			$compileFile = $this->path.'compile/'.$info['filename'].'.php';
		}
		//判断是否自动编译
		if ($isPath || $this->theme_compile || !is_file($compileFile)) {
			//以防传过来的$fileName是多文件夹格式
			!is_dir(dirname($compileFile)) && @mkdir(dirname($compileFile), 0777, true);
			$themeFile = $isPath ? $fileName : $this->path.$fileName.'.php';
			if (!is_file($themeFile)) return false;
			//编译文件是否存在，模板文件修改时间是否大于编译文件的修改时间
			if ($isPath || !is_file($compileFile) || filemtime($themeFile) > filemtime($compileFile)) {
				$content = $this->replace(file_get_contents($themeFile));
				file_put_contents($compileFile, $content);
			}
		}
		$_SESSION['includeTheme'] = 1;
		return $compileFile;
	}
	/**
	 * 编译整个模板
	 * @param bool $path
	 * @param bool $compilePath
	 */
	public function compile($path = false, $compilePath = false){
		if (!$path) $this->delCompile();
		$isOne = $path;
		$path = $path ? $path : $this->path;
		$compilePath = $compilePath ? $compilePath : $this->path.'compile/';
		$len = mb_strlen($path);
		$list = glob($path.'*', GLOB_NOSORT);
		foreach ($list as $name) {
			$fileName = mb_substr($name, $len);
			if (!$isOne && $fileName === 'compile') continue;
			if (is_dir($name)) {
				$this->compile($name.'/', $compilePath.$fileName.'/');
			}else{
				$files = pathinfo($name);
				if ($files['extension'] == 'php') {
					!is_dir($compilePath) and mkdir($compilePath, 0777, true);
					file_put_contents($compilePath.$fileName, $this->replace(file_get_contents($name)));
				}
			}
		}
	}
	/**
	 * 删除编译文件
	 */
	public function delCompile(){
		global $util;
		$path = $this->path.'compile/';
		if (is_dir($path)) {
			$util->delete($path);
		}
	}
	/**
	 * 数组初始化 $conf.title = $conf['title']
	 * @param string $v
	 * @return string
	 */
	public function arrInit($v){
		if (strpos($v, '.') === false) return $v;
		$v = preg_replace_callback('/(\'[^\']+\')|("[^"]+")/',function($a){
			return str_replace('.', '@tpl@', $a[0]);
		}, $v);
		$v = preg_replace_callback('/(?<!\'|"|\d)\.([\w\-]+\.?)+/',function($a){
			$a[0] = explode('.', $a[0]);
			array_shift($a[0]);
			return '[\''.implode('\'][\'', $a[0]).'\']';
		}, $v);
		return str_replace('@tpl@', '.', $v);
	}
	/**
	 * 正则处理
	 * @param string $v
	 * @return string
	 */
	public function replace($v){
		//处理第一行exit限制
		$v = preg_replace("/^<\?php\s+exit\([\w\W]*?\);\s*\?>/i", '', $v);
		//处理转义
		$v = str_replace(['@','\{', '\}'], ['-@-','@_', '_@'], $v);
		//处理多行表达式
		$v = preg_replace_callback('/\{\{([\w\W]*?)\}\}/is',function($m){
			return '<?php '.$this->arrInit($m[1]).';?>';
		}, $v);
		//处理变量和常量
		$v = preg_replace_callback(['/\{(\$.+?)\}/','/\{#\s*(.+?)\}/'],function($m){
			return '<?php echo '.$this->arrInit($m[1]).';?>';
		}, $v);
		//处理扩展钩子 <!-- hook.head_footer --> 和 // hook.head_footer
		$v = preg_replace_callback(['/<\!-- hook\.(\w+) -->/','/\/\/\s*hook\.(\w+)/'],function($m){
			return '<?php foreach($hook[\''.$m[1].'\'] as $fn) echo $fn();?>';
		}, $v);
		//处理if单行形式
		$v = preg_replace_callback('/\{if\s+(.+?)\}/',function($m){
			if (strpos($m[1], '?') === false && strpos($m[1], ':') === false) return $m[0];
			$m[1] = $this->arrInit($m[1]);
			$m[1] = preg_replace_callback('/(\'[^\']+\')|("[^"]+")/',function($a){
				return str_replace(['?',':'], ['@1@','@2@'], $a[0]);
			}, $m[1]);
			$m[1] = preg_replace_callback('/(.+)\?(.+):(.+)/',function($a){
				return '<@1@php echo '.$a[1].'@1@'.$a[2].'@2@'.$a[3].';@1@>';
			}, $m[1]);
			$m[1] = preg_replace_callback('/(.+)\?(.+)/',function($a){
				return '<@1@php if('.$a[1].') echo '.$a[2].';@1@>';
			}, $m[1]);
			$m[1] = preg_replace_callback('/(.+):(.+):(.+)/',function($a){
				$a[2] = str_replace('"', '\\"', $a[2]);
				$a[3] = str_replace('"', '\\"', $a[3]);
				return '<@1@php echo '.$a[1].'@1@"'.$a[2].'"@2@"'.$a[3].'";@1@>';
			}, $m[1]);
			$m[1] = preg_replace_callback('/(.+):(.+)/',function($a){
				$a[2] = str_replace('"', '\\"', $a[2]);
				return '<@1@php if('.$a[1].') echo "'.$a[2].'";@1@>';
			}, $m[1]);
			$m[1] = str_replace(['@1@','@2@'], ['?',':'], $m[1]);
			return $m[1];
		}, $v);
		//处理简易foreach index key item
		$v = preg_replace_callback('/\{foreach\s+([^\s]+)\}/',function($m){
			return '<?php $index = -1; foreach ((array)'.$this->arrInit($m[1]).' as $key => $item){ $index++;?>';
		}, $v);
		//处理if elseif foreach
		$v = preg_replace_callback('/\{(if|elseif|foreach)\s+(.+?)\}/',function($m){
			$m[1] = $m[1] === 'elseif' ? '}'.$m[1] : $m[1];
			return '<?php '.$m[1].' ('.$this->arrInit($m[2]).'){ ?>';
		}, $v);
		//处理前台模板include
		$v = preg_replace_callback('/\{include\s+tpl\s+(.*?)\}/i',function($m){
			return '<?php include TPLPATH.\'compile/'.$m[1].'.php\';?>';
		}, $v);
		//处理后台模板include
		$v = preg_replace_callback('/\{include\s+admin\s+(.*?)\}/i',function($m){
			return '<?php include LIB.\'admin/compile/'.$m[1].'.php\';?>';
		}, $v);
		//处理普通include
		$v = preg_replace_callback('/\{include\s+(.*?)\}/i',function($m){
			$this->view($m[1]);
			return '<?php include \''.$m[1].'.php\';?>';
		}, $v);
		//处理url伪静态，支持变量
		$v = preg_replace_callback('/\{url\s+(.*?)\}/i',function($m){
			$m[1] = preg_replace('/(\$\w+(\.[\w\-]+)*)/', '{$1}', $m[1]);
			$m[1] = $this->arrInit($m[1]);
			return '<?php echo URL."'.$m[1].'";?>';
		}, $v);
		$v = str_replace(['@_', '_@', '-@-', '{/if}', '{else}', '{/foreach}'], ['{', '}', '@', '<?php } ?>', '<?php } else {?>', '<?php } ?>'], $v);
		return $v;
	}
}
?>