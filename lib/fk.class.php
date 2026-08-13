<?php
/**
 * fk标记语言类
 * 创建：2019-11-05
 * 更新：2022-09-22
 */
class fk {
	private $arr = [];
	private $table = [];
	private $tables = 0;
	private $tableThead = false;
	private $list = [];
	private $code = false;
	private $color = ['R','O','Y','G','C','B','P','D'];
	public $title = '';
	public $version = '';
	public $html = '';
	public $htmls = false;
	public $td;
	public $var;
	public $col;
	public $tdNum;
	public $mark = [
		'i'=>'fk-italic',	'b'=>'fk-bold',
		's'=>'fk-del',		'u'=>'fk-underline',
		'R'=>'fk-color-R',	'O'=>'fk-color-O',
		'Y'=>'fk-color-Y',	'G'=>'fk-color-G',
		'C'=>'fk-color-C',	'B'=>'fk-color-B',
		'P'=>'fk-color-P',
		'LR'=>'fk-tag fk-tag-LR', 'LO'=>'fk-tag fk-tag-LO', 'LY'=>'fk-tag fk-tag-LY', 'LG'=>'fk-tag fk-tag-LG', 'LC'=>'fk-tag fk-tag-LC', 'LB'=>'fk-tag fk-tag-LB', 'LP'=>'fk-tag fk-tag-LP', 'LD'=>'fk-tag fk-tag-LD', 'DR'=>'fk-tag fk-tag-DR', 'DO'=>'fk-tag fk-tag-DO', 'DY'=>'fk-tag fk-tag-DY', 'DG'=>'fk-tag fk-tag-DG', 'DC'=>'fk-tag fk-tag-DC', 'DB'=>'fk-tag fk-tag-DB', 'DP'=>'fk-tag fk-tag-DP', 'DD'=>'fk-tag fk-tag-DD',
		't'=>'fk-title',
		'ts'=>'fk-title-s',
		'tu'=>'fk-title-u',
		'tl'=>'fk-title-l',
		'tc'=>'fk-title-c',
		'tr'=>'fk-title-r',
		'@'=>'fk-mail',
		'/'=>'fk-italic',
		'%'=>'fk-bold',
		'_'=>'fk-underline',
		'>'=>'fk-blockquote',
		'>R'=>'fk-blockquote fk-blockquote-R',
		'>O'=>'fk-blockquote fk-blockquote-O',
		'>Y'=>'fk-blockquote fk-blockquote-Y',
		'>G'=>'fk-blockquote fk-blockquote-G',
		'>C'=>'fk-blockquote fk-blockquote-C',
		'>B'=>'fk-blockquote fk-blockquote-B',
		'>P'=>'fk-blockquote fk-blockquote-P',
		'#'=>'fk-h2',
		'#1'=>'fk-h1',
		'#1-'=>'fk-h1 fk-u',
		'#2'=>'fk-h2',
		'#2-'=>'fk-h2 fk-u',
		'#3'=>'fk-h3',
		'#3-'=>'fk-h3 fk-u',
		'#4'=>'fk-h4',
		'#4-'=>'fk-h4 fk-u',
		'#5'=>'fk-h5',
		'#5-'=>'fk-h5 fk-u',
		'#6'=>'fk-h6',
		'#6-'=>'fk-h6 fk-u',
		'sup'=>'fk-sup',
		'sub'=>'fk-sub',
		'left'=>'fk-left',
		'center'=>'fk-center',
		'right'=>'fk-right',
	];
	
	//构造方法， 三个参数
	function __construct($fk='') {
		$this->fk($fk."\n"); //调用自己的方法
	}
	//获取html
	public function html(){
		return $this->html;
	}
	//获取目录的arr
	public function catalog($fk){
		$fk = explode("\n",$fk);
		$arr = [];
		$arr['catalog'] = [];
		$isTitle = false;
		foreach ($fk as $v) {
			$vs = trim($v);
			//内容前的空白行不做处理
			if(!$vs) continue;
			//第一行如果为文档名称，进行处理
			if(!$isTitle){
				$isTitle = true;
				preg_match("/(.*?)\s*(v\d+(.\d+)*)*$/i",$vs,$title);
				$arr['title'] = $title[1];
				$arr['version'] = isset($title[2])?$title[2]:'';
				continue;
			}
			if(substr($vs,0,3)=='---') continue;
			$this->catalogReg($v);
		}
		$arr['catalog'] = $this->htmlCatalog($this->list);
		return $arr;
	}
	private function catalogReg($str) {
		if(!strlen(trim($str))) return;
		//针对列表的分级
		preg_match_all("/^([\s]+).+?/",$str,$matches);
		$tab = isset($matches[1][0])?count(str_split($matches[1][0])):0;
		if($tab) $tab = intval($tab/2);//两个空格一级
		$this->list = $this->addChild($this->list,['content'=>trim($str),'level'=>$tab,'type'=>1],$tab);
		return;
	}
	private function htmlCatalog($arr){
		$html = '<ul>';
		foreach ($arr as $v) {
			$html .= '<li>';
			$html .= '<a href="javascript:;">'.$v['content'].'</a>';
			if(isset($v['child'])) $html .= $this->htmlChild($v['child']);
			$html .= '</li>';
		}
		$html .=  '</ul>';
		return $html;
	}
	//中英文字符串打散为数组
	private function str2arr($str){
		$length = mb_strlen($str, 'utf-8');
		$arr = [];
		for ($i=0; $i<$length; $i++){
			$arr[] = mb_substr($str, $i, 1, 'utf-8');
		}
		return $arr;
	}
	//获取字符串的宽度
	private function getWidth($str){
		//每个字母占据的宽度不一样，空格=3，汉字=11
		$w = [
			'mw'=>11,
			'ABCDEFGHIJKLMNOPQRSTUVWXYZ'=>8,
			'abdeghnopqeuvxy0123456789'=>7,
			'crstz.'=>5,
			'fijl'=>3,
		];
		$arr = $this->str2arr($str);
		$width = 0;
		foreach ($arr as $value) {
			$_width = $width;
			foreach ($w as $k => $v) {
				if(strpos($k,$value) !== false){
					$width += $v;
					break;
				}elseif($value == ' '){
					$width += 3;
					break;
				}elseif(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$value)){
					$width += 12;
					break;
				}
			}
			if($_width === $width){
				$width += 10;
			}
		}
		return $width;
	}
	//徽章title|content color
	private function badge($content,$color='default'){
		if(!$content) return '';
		$p1 = explode('|', trim($content));
		$p2 = trim($color);
		$key = $p1[0];
		$keyLen = $this->getWidth($key);
		$keyWidth = $keyLen + 10;
		$width = $keyWidth;
		$_SESSION['badge'] = isset($_SESSION['badge'])?++$_SESSION['badge']:0;
		$id = $_SESSION['badge'];
		$value = $p1[0];
		if(isset($p1[1])){
			$value = $p1[1];
			$valueLen = $this->getWidth($value);
			$valueWidth = $valueLen + 10;
			$valueX = $keyWidth + 5;
			$width = $keyWidth + $valueWidth;
			$valueBG = $width - $keyWidth;
		}
		$color = $p2;
		$color = empty($color) ? 'default' : $color;
		$colorList = ['red' => '#f1716c','orange' => '#f57e00','yellow' => '#fac413','green' => '#26bc5c','cyan' => '#8cd03d','blue' => '#768fe7','purple' => '#9e83e1','gray' => '#b2b2b2','black' => '#555','default' => '#f1716c'];
		$color = isset($colorList[$color]) ? $colorList[$color] : '#'.$color;
		if(isset($p1[1])){
			$rect = '<rect width="'.$keyWidth.'" height="20" fill="#555"/><rect x="'.$keyWidth.'" width="'.$valueBG.'" height="20" fill="'.$color.'"/><rect width="'.$width.'" height="20" fill="url(#l'.$id.')"/>';
			$text = '<text x="'.$valueX.'" y="14" fill="#fff" textLength="'.$valueLen.'">'.$value.'</text>';
		}else{
			$rect = '<rect width="'.$keyWidth.'" height="20" fill="'.$color.'"/><rect width="'.$width.'" height="20" fill="url(#l'.$id.')"/>';
			$text = '';
		}
		$svg = '
		<svg width="'.$width.'" height="20" role="img" aria-label="'.$key.': '.$value.'">
			<title>'.$key.': '.$value.'</title>
			<linearGradient id="l'.$id.'" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient>
			<clipPath id="c'.$id.'"><rect width="'.$width.'" height="20" rx="3" fill="#fff"/></clipPath>
			<g clip-path="url(#c'.$id.')">'.$rect.'</g>
			<g fill="#fff" text-anchor="start" font-family="Verdana,Geneva,DejaVu Sans,sans-serif" text-rendering="geometricPrecision" font-size="11"><text x="5" y="14" fill="#fff" textLength="'.$keyLen.'">'.$key.'</text>'.$text.'</g>
		</svg>';
		return preg_replace("/[\r\n]+/",'',$svg); 
	}

	//循环遍历
	private function fk($fk){
		//不渲染fk的注释
		$fk=preg_replace('/(?<!\\\)\[!.*?\](?<!\\\)/is','',$fk);
		$fk=str_replace("\r",'',$fk);
		$fk = explode("\n",$fk);
		$html='<div class="fk">';
		foreach ($fk as $v) {
			$this->fkReg($v);
		}
		$isTitle = false;
		foreach ($this->arr as $h) {
			if(!$h && !$isTitle) continue;
			//第一行如果为文档名称，进行处理
			if($h && !$isTitle){
				$isTitle = true;
				preg_match('/^《(.+)》$/i',$h,$match);
				if(isset($match[1]) && $match[1]){
					preg_match('/(.*?)\s*(v\d+(.\d+)*)*$/i',trim($match[1]),$title);
					$this->title = $title[1];
					$this->version = isset($title[2])?$title[2]:'';
					$h = '<p class="fk-title">'.$this->title.'<span class="fk-version">'.$this->version.'</span></p>';
				}
			}
			$html .= $h ? $h."\n" : "\n";
		}
		$html .='</div>';
		//多行代码展示
		$html = preg_replace_callback('/``(\w+)(.*?)``/s', function ($m) {
			return '<pre class="code-'.$m[1].'">'.htmlentities($this->codeInit($m[2])).'</pre>';
		}, $html);
		//转义符
		$html=preg_replace('/\\\([^\s]?)/','$1',$html);
		//表格内的竖线
		$html = str_replace('__@vline__','|',$html);
		$this->html = $html;
	}
	//格式化代码
	private function codeInit($code){
		$code = preg_replace('/\t/','    ',$code);
		$code = explode("\n", $code);
		if(count($code)>0){
			$str = '';
			foreach ($code as $v) {
				if(strlen(trim($v))){
					$str = $v;
					break;
				}
			}
			if(strlen($str)){
				if(preg_match('/^\s+/', $str, $m)){
					if($m[0]){
						foreach ($code as &$v) {
							$v = preg_replace('/^\s{0,'.strlen($m[0]).'}/', '', $v, 1);
						}
					}
				}
			}
		}
		return implode("\n", $code);
	}
	//向最后一个子元素的child中添加数组
	private function addChild($list,$arr,$tab=0){
		$s = count($list)-1;
		if(!$tab){
			array_push($list,$arr);
			return $list;
		}
		if(isset($list[$s]['child']) && $list[$s]['child']){
			$list[$s]['child'] = $this->addChild($list[$s]['child'],$arr,$tab-1);
		}else{
			if($s<0){
				array_push($list,$arr);
			}else{
				$list[$s]['child'] = [$arr];
			}
		}
		return $list;
	}
	//将列表数组格式化为html
	private function htmlChild($arr){
		if($arr[0]['type']===1){
			$type = 'ul';
		}elseif($arr[0]['type']===2){
			$type = 'ol';
		}
		$html = '<'.$type.'>';
		foreach ($arr as $v) {
			$li = '<li>';
			$str = $v['content'];
			$a = substr($str,0,1);
			if(in_array($a,$this->color)){
				$li = '<li class="fk-color-'.$a.'">';
				$str = substr($str,1);;
			}
			$html .= $li;
			$html .= $str;
			if(isset($v['child'])){
				$html .= $this->htmlChild($v['child']);
			}
			$html .= '</li>';
		}
		$html .=  '</'.$type.'>';
		return $html;
	}
	//根据标记获取样式
	public function getStyle($t){
		$arr = explode('.', $t);
		$class = [];
		$style = '';
		foreach ($arr as $v) {
			//是否全部存在，不存在说明语法错误，当作tag处理
			if(isset($this->mark[$v])){
				$class[]=$this->mark[$v];
			}else{
				$a = substr($v,0,1);
				if($a == '~'){
					$style .= 'font-size:'.substr($v,1).'px;';
				}elseif($a == '#'){
					$style .= 'color:'.$v.';';
				}
			}
		}
		$class = implode(' ', $class);
		return ($class ? ' class="'.$class.'"':'').($style ? ' style="'.$style.'"':'');
	}
	//表格转为html
	private function tableHtml(){
		if($this->table){
			$html = '<table>';
			$right = false; //存储右对齐的key
			$center = false;
			$width = [];
			if($this->tableThead){
				$html .= '<thead>';
					$html .= '<tr>';
					foreach ($this->table[0] as $k => $v) {
						$str = trim($v);
						$w = '';
						if(preg_match('/^([\d]+[\w%]*)(\s+|\-)/',$str,$match)){
							$w = $match[1];
							$width[] = ['cols' => $k,'width' => $w];
							$str=preg_replace('/^\d+[\w%]*\s*/s','',$str);
						}
						if($w){
							if(preg_match('/^\d+$/',$w)) $w .= 'px';
							$w = ' style="width:'.$w.'"';
						}
						if(substr($str,0,1) == '-' && substr($str,-1) == '-'){
							$center[]=$k;
							$html .= '<th class="fk-table-center"'.$w.'>'.substr(substr($str,1),0,-1).'</th>';
						}elseif(substr($str,-1) == '-'){
							$right[]=$k;
							$html .= '<th class="fk-table-right"'.$w.'>'.substr($str,0,-1) .'</th>';
						}elseif(substr($str,0,1) == '-'){
							$html .= '<th class="fk-table-left"'.$w.'>'.substr($str,1).'</th>';
						}else{
							$html .= '<th class="fk-table-left"'.$w.'>'.$str.'</th>';
						}
					}
					$html .= '</tr>';
				$html .= '</thead>';
				array_splice($this->table,0,1);
				$this->tableThead = false;
			}
			$html .= '<tbody>';
			foreach ($this->table as $v) {
				$html .= '<tr>';
				foreach ($v as $k => $value) {
					$color = '';
					//背景色和前景色处理
					if(preg_match('/^(\s*\[(#\w{3,6})\.?(#\w{3,6})?\])/',$value,$match)){
						$color = ' style="background:'.(isset($match[3])?$match[2].';color:'.$match[3].'"':(isset($match[2])?$match[2]:'').'"');
						$value = str_replace($match[1],'',$value);
					}
					//处理多行代码段
					$code = [];
					$value = preg_replace_callback('/``\w+.*?``/s',function ($m)use(&$code) {
						$code[] = $m[0];
						return '__@vcode'.(count($code)-1).'__';
					},$value);
					//多行
					$value_arr = explode("\n", $value);
					if(count($value_arr)>1){
						array_splice($value_arr, count($value_arr)-1,1);
						$value = implode('</p><p>',$value_arr);
					}
					$value = preg_replace_callback('/__@vcode(\d+)__/', function ($m)use($code) {
						return $code[$m[1]];
					}, $value);
					$align = 'fk-table-left';
					if($center && in_array($k,$center)) $align = 'fk-table-center';
					if($right && in_array($k,$right)) $align = 'fk-table-right';
					$td = '<td class="'.$align.'"'.$color.'>';
					$html .= $td.'<p>'.$value.'</p>'.'</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
			$this->table = [];
			$this->arr[] = $html;
		}
	}

	//正则文本
	private function fkReg($str) {
		if($str === '[html'){
			$this->htmls = '';
			return;
		}
		if($this->htmls !== false){
			if($str === ']'){
				$this->arr[] = $this->htmls;
				$this->htmls = false;
			}else{
				$this->htmls .= $str."\n";
			}
			return;
		}
		//处理变量
		$str=preg_replace_callback('/(?<!\\\)\[\.([^\]]+?)\](?<!\\\)/',function($m){
			if(isset($this->var[$m[1]])) return $this->var[$m[1]];
			return $m[0];
		},$str);
		//针对列表的分级
		preg_match_all('/^([\s]+).+?/',$str,$matches);
		$tab = isset($matches[1][0])?count(str_split($matches[1][0])):0;
		if($tab) $tab = intval($tab/2);//两个空格一级
		//是否需要用到p标签
		$p = false;
		//去除前后空格的纯文本
		$text=trim($str);
		if(!$text){
			if($this->list){
				$this->arr[] = $this->htmlChild($this->list);
				$this->list = [];
			}
			$this->tableHtml();
			return $this->arr[] = false;
		}
		//简写-直接作用整行，不需要闭合，如果需要输出下列关键字，需要在前面加'#'
		$a1 = $text[0];//获取第一个字符
		$a2 = substr($text,0,2);//获取前两个字符
		$a3 = substr($text,0,3);//获取前三个字符
		$b1 = trim(substr($text,1));//从第二个字符开始到最后一个字符
		$b2 = trim(substr($text,2));//从第三个字符开始到最后一个字符
		$c = substr($text,1,1);//获取第二个字符
		if($a1 == '['){
			//处理变量
			if(preg_match('/^\[\$([^\]]+)\]/',$str,$match)){
				$var = trim($match[1]);
				$this->var[$var] = trim(substr($str,strlen($match[1])+3));
				return;
			}
			//处理标记符
			if(preg_match('/^\[([^\]]+)\s+([^\]]+)\](.*)/',$text,$match)){
				$style = $this->getStyle($match[1]);
				if($style) $text='<span'.$style.'>'.$match[2].'</span>'.$match[3];
			}
			elseif(preg_match('/^\[(.*?)\]/',$text,$match)){
				$style = $this->getStyle($match[1]);
				if($style){
					$text = trim(substr($text,strlen($match[1])+2));
					$text='<p'.$style.'>'.$text.'</p>';
				}
			}
		}
		//列表
		if($a2 != '- ' && $a2 != '* '){
			if($this->list){
				$this->arr[] = $this->htmlChild($this->list);
				$this->list = [];
			}
		}
		//代码段
		if($this->tables){
			if($this->code){
				if($a2 == '``'){
					$this->code=false;
					$this->col = explode("\n", $this->col);
					array_pop($this->col);
					$this->col = implode("\n", $this->col);
					return $this->col .= $str."\n";
				}
				return $this->col .= $str."\n";
			}
			if($a2 == '``'){
				$this->code=true;
				$text=preg_replace('/[`\s]+/','``',$text);
				$text=preg_replace('/(\w+)`+/','$1',$text);
				return $this->col .= $text."\n";
			}
		}else{
			if($this->code){
				if($a2 == '``'){
					$this->code=false;
					return $this->arr[] = $a2;
				}
				return $this->arr[] = $str;
			}
			if($a2 == '``'){
				$this->code=true;
				$text=preg_replace('/[`\s]+/','``',$text);
				$text=preg_replace('/(\w+)`+/','$1',$text);
				return $this->arr[] = $text;
			}
		}
		if($text == '[table'){
			$this->tables = 1;
			$this->td = [];
			$this->col = '';
			return;
		}
		//块级元素处理
		if($a1=='>'){
			$c = substr($b1,0,1);
			$str = substr($b1,1);
			if(in_array($c,$this->color)){
				$text='<blockquote class="fk-blockquote-'.$c.'">'.$str.'</blockquote>';
			}else{
				$text='<blockquote>'.$b1.'</blockquote>';
			}
		}else{
			preg_match('/^(#\w{3,})\s+(.+)/',$text,$match);
			if($match){
				$text='<p style="color:'.$match[1].'">'.$match[2].'</p>';
			}else{
				$u = substr($text,2,1)=='-'?' class="fk-u"':'';
				$b2 = substr($text,2,1)=='-'?substr($text,3):$b2;
				if($a2=='#1'){
					$text='<h1'.$u.'>'.$b2.'</h1>';
				}elseif($a2=='#2'){
					$text='<h2'.$u.'>'.$b2.'</h2>';
				}elseif($a2=='#3'){
					$text='<h3'.$u.'>'.$b2.'</h3>';
				}elseif($a2=='#4'){
					$text='<h4'.$u.'>'.$b2.'</h4>';
				}elseif($a2=='#5'){
					$text='<h5'.$u.'>'.$b2.'</h5>';
				}elseif($a2=='#6'){
					$text='<h6'.$u.'>'.$b2.'</h6>';
				}elseif($a2=='# '){
					$text='<h2 class="fk-u">'.$b2.'</h2>';
				}elseif($a2=='=='){
					return $this->arr[] = '<hr/>';
				}else{
					$p = true;
				}
			}
		}
		
		//行内元素处理
		$text=preg_replace_callback('/(?<!\\\)`(.+?)(?<!\\\)`/',function($m){
			$code = trim($m[1]);
			$code=preg_replace('/\[/','__@left__',$code);
			$code=preg_replace('/\]/','__@right__',$code);
			if($code) return '<code>'.htmlspecialchars($code).'</code>';
			return '';
		},$text);
		$text=preg_replace_callback('/(?<!\\\)\[(.+?)\s+(.+?)(?<!\\\)\]/', function($m){
			$style = $this->getStyle($m[1]);
			if($style) return '<span'.$style.'>'.$m[2].'</span>';
			return $m[0];
		}, $text);
		$text=preg_replace('/(?<!\\\)\[(#.+?)\s+(.+?)\](?<!\\\)/is','<span style="color:$1">$2</span>',$text);
		$text=preg_replace('/(?<!\\\)\[~(\d+)\s+(.+?)\](?<!\\\)/is','<span style="font-size:$1px">$2</span>',$text);
		$text=preg_replace_callback('/(?<!\\\)\[icon\s+([^\s\]]+)\s*([^\s\]]*)\](?<!\\\)\((.+?)\)/is',function($m){
			return '<a href="'.$m[3].'" target="_blank">'.$this->badge($m[1],$m[2]).'</a>';
		},$text);
		$text=preg_replace_callback('/(?<!\\\)\[icon\s+([^\s\]]+)\s*([^\s\]]*)\](?<!\\\)/i',function($m){
			return $this->badge($m[1],$m[2]);
		},$text);
		$text=preg_replace('/(?<!\\\)\[@\s+(.+?)\](?<!\\\)/is','<a href="mailto:$1">$1</a>',$text);
		$text=preg_replace('/(?<!\\\)\[img\s+([^\s\]]*)\s*([^\s\]]*)\s*([^\s\]]*)\](?<!\\\)\((.+?)\)/is','<a href="$4" target="_blank"><img src="$1" alt="$2" title="$3"/></a>',$text);
		$text=preg_replace('/(?<!\\\)\[img\s+([^\s\]]*)\s*([^\s\]]*)\s*([^\s\]]*)\s*([^\s\]]*)\s*([^\s\]]*)\](?<!\\\)/is','<img src="$1" alt="$2" title="$3" width="$4" height="$5"/>',$text);
		$text=preg_replace('/(?<!\\\)\[file\s+([^\s\]]*)\s*([^\s\]]*)\s*([^\s\]]*)\](?<!\\\)/is','<a href="$1" download="$2" title="$3"/>$2</a>',$text);
		//url
		$text=preg_replace('/(?<!\\\)\[([^\s\]]+)\](?<!\\\)\(([^\s\]]*)\s*([^\s\]]*)\)/is','<a href="$2" title="$3" target="_blank">$1</a>',$text);
		$text=preg_replace_callback('/(?<!\\\)\((http[s]?:\/\/[^\s]+)\s*(((?!\s).)*)\)/i', function($m){
			if($m[1]) return '<a href="'.$m[1].'" title="'.$m[2].'" target="_blank">'.$m[1].'</a>';
			return $m[0];
		}, $text);
		//普通标签
		$text=preg_replace("/(?<!\\\)\[(?!#)(.+?)(?<!\\\)\]/i","<span class='fk-tag'>$1</span>",$text);
		$text = str_replace('__@left__','[',$text);
		$text = str_replace('__@right__',']',$text);
		//列表
		if($a2 == '- ' || $a2 == '* '){
			if($a2 == '- ')$type = 1;
			if($a2 == '* ')$type = 2;
			$this->list = $this->addChild($this->list,['content'=>trim(substr($text,2)),'level'=>$tab,'type'=>$type],$tab);
			return;
		}
		//表格
		$rows = preg_replace('/\\\\\\|/','__@vline__',$text);
		$td = explode('|',$rows);
		$tdNum = count($td);
		if($a1 == '|' && $tdNum>1){
			array_splice($td,0,1); //删除首段
			$this->table[] = $td;
			return;
		}
		//多行内容表格
		if($this->tables){
			if($tdNum>1){
				$this->table[] = $td;
				$this->tdNum = $tdNum;
				return;
			}
			//表头分割
			if($a3 == '---' && !$this->tableThead){
				$this->tableThead = true;
				return;
			}
			//列分割
			if(preg_match('/^\-{2,}$/',$text)){
				$this->td[] = $this->col;
				$this->col = '';
				if($this->tableThead){
					if(count($this->td) === $this->tdNum){
						$this->table[] = $this->td;
						$this->td = [];
						return;
					}
				}else{
					$this->table[] = $this->td;
					$this->td = [];
				}
				return;
			}
			if($text == ']'){
				$this->td[] = $this->col;
				$this->table[] = $this->td;
				$this->td = [];
				$this->tables = 0;
				$this->tableHtml();
			}else{
				$this->col .= $text."\n";
			}
			return;
		}
		if($this->table){
			//判断是否有分割线-有的话设置表头thead
			if($a2 == '--'){
				$this->tableThead = true;
				return;
			}
			$this->tableHtml();
		}
		if(empty($text)){
			$text=false;
			$p = false;
		}
		if($p && $text) $text='<p>'.$text.'</p>';
		return $this->arr[] = $text;
	}
}
?>