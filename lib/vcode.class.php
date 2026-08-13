<?php
/**
 * 验证码类
 * 创建：2022-10-15
 * 更新：2023-08-09
 */
class vcode {
	private $width;   //宽
	private $height;  //高
	private $num;	  //数量
	private $code;    //验证码
	private $img;     //图像的资源
	function __construct($width=80, $height=25, $num=4) {
		$this->width = $width;
		$this->height = $height;
		$this->num = $num;
		$this->code = $this->createCode();
	}
	//获取字符的验证码
	function getCode() {
		return $this->code;
	}
	//输出图像
	function outImg() {
		$this->createBack();
		$this->outString();
		$this->printImg();
	}
	//创建背景
	private function createBack() {
		//创建资源
		$this->img = imagecreatetruecolor($this->width, $this->height);
		//设置背景填充
		imagefill($this->img, 0, 0, imagecolorallocate($this->img, 255, 255, 255));
	}
	//画字
	private function outString() {
		for($i=0; $i<$this->num; $i++) {
			$color= imagecolorallocate($this->img, rand(0, 50), rand(0, 50), rand(0, 50));
			$x = intval(3+($this->width/$this->num)*$i);
			$y = rand(0,8);
			imagechar($this->img, 5, $x, $y, $this->code[$i], $color);
		}
	}
	//输出图像
	private function printImg() {
		if (imagetypes() & IMG_GIF) {
			header('Content-type: image/gif');
			imagegif($this->img);
		} elseif (function_exists('imagejpeg')) {
			header('Content-type: image/jpeg');
			imagegif($this->img);
		} elseif (imagetypes() & IMG_PNG) {
			header('Content-type: image/png');
			imagegif($this->img);
		} else {
			die('此PHP服务器不支持图像');
		}
	}
	//生成验证码字符串
	private function createCode() {
		$str = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY';
		$code = '';
		for($i=0; $i < $this->num; $i++) {
			$code .=$str[rand(0, strlen($str)-1)];	
		}
		return $code;
	}
	//用于自动销毁图像资源
	function __destruct() {
		imagedestroy($this->img);
	}
}
?>