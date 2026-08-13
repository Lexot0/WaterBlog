<?php
/**
 * 操纵文件类
 * 例子：
 * fileUtil::createDir('a/1/2/3');             建立文件夹，建一个a/1/2/3文件夹
 * fileUtil::createFile('b/1/2/3');            建立文件，在b/1/2/文件夹下面建一个3文件
 * fileUtil::createFile('b/1/2/3.exe');        建立文件，在b/1/2/文件夹下面建一个3.exe文件
 * fileUtil::copy('b/1/2/3.exe','b/b/');       复制文件，建立一个b/b文件夹，并把3.exe文件复制进去
 * fileUtil::copy('b/1/2/3.exe','b/b/4.exe');  复制文件，建立一个b/b文件夹，并把3.exe文件复制进去，并重命名为4.exe
 * fileUtil::copy('b/1/2/','b/b/');            复制文件夹，建立一个b/b文件夹，并把b/1/2/文件夹下的所有文件复制进去
 * fileUtil::cut('a.exe','b/c/');              剪贴文件，建立一个b/c文件夹,并把a.exe剪贴到b/c文件夹中
 * fileUtil::cut('a.exe','b/c/d.exe');         剪贴文件，建立一个b/c文件夹,并把a.exe剪贴到b/c文件夹中，并重命名为d.exe
 * fileUtil::cut('a/','b/c/');                 剪贴文件夹，建立一个b/c文件夹,并把a文件夹剪贴到b/c文件夹中
 * fileUtil::delete('d');                      删除文件或文件夹
 * fileUtil::rename('b/1/2/3.exe','4.exe');    重命名文件或文件夹
 */
class fileUtil {
	/**
	 * 新建文件夹
	 * @param string $path
	 * @return bool
	 */
	public function createDir($path) {
		$path = mb_convert_encoding($path,'UTF-8');
		return is_dir($path) ? true : mkdir($path,0777,true);
	}

	/**
	 * 新建文件
	 * @param string $path 
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool|int
	 */
	public function createFile($path, $content='', $overwrite = true) {
		$path = mb_convert_encoding($path,'UTF-8');
		//目标路径不存在则创建
		if(strpos($path,'/') !== false){
			$dir = substr($path,0,strripos($path,'/'));
			if(!is_dir($dir)) mkdir($dir,0777,true);
		}
		return $overwrite || !is_file($path) ? file_put_contents($path, $content) : false;
	}

	/**
	 * 复制
	 * @param string $source 结尾为/，复制该文件下的所有文件，其它为文件名或文件夹名
	 * @param string $dest 结尾为/，复制到该文件夹下，其它重命名为文件
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function copy($source, $dest, $overwrite = true) {
		$source = mb_convert_encoding($source,'UTF-8');
		$dest = mb_convert_encoding($dest,'UTF-8');
		if(!is_file($source) && !is_dir($source) || $source === $dest) return false;
		if(is_file($source)){
			//目标路径不存在则创建
			if(strpos($dest,'/') !== false){
				$dir = substr($dest,0,strripos($dest,'/'));
				if(!is_dir($dir)) mkdir($dir,0777,true);
			}
			if(substr($dest,-1) === '/'){
				$dest .= basename($source);
			}
			return $overwrite || !is_file($dest) ? copy($source, $dest) : false;
		}
		//如果源地址为文件夹，通过递归来实现复制该文件夹下的所有文件夹
		else{
			if(substr($source,-1) !== '/') $source .= '/';
			if(substr($dest,-1) !== '/') $dest .= '/';
			function run($source, $dest, $overwrite){
				$dir = opendir($source);
				if(!is_dir($dest)) mkdir($dest,0777,true);
				while(false !== ($file = readdir($dir)) ) {
					$s = $source.$file;
					$d = $dest.$file;
					if (($file != '.') && ($file != '..')) {
						if(is_dir($s)) {
							run($s,$d,$overwrite);
						}else{
							if($overwrite || !is_file($d)) copy($s,$d);
						}
					}
				}
				closedir($dir);
			}run($source, $dest, $overwrite);
		}
	}

	/**
	 * 剪贴
	 * @param string $source
	 * @param string $dest
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function cut($source, $dest, $overwrite = true){
		fileUtil::rename($source, $dest, $overwrite);
		// fileUtil::copy($source, $dest, $overwrite);
		// fileUtil::delete($source);
	}

	/**
	 * 删除
	 * @param string $path 结尾如果为/,删除该文件下的所有文件
	 * @return bool
	 */
	public function delete($path) {
		if(is_dir($path)){
			$dh=opendir($path);
			while ($file=readdir($dh)) {
				if($file!='.' && $file!='..') {
					$fullpath=$path.'/'.$file;
					if(is_dir($fullpath)) {
						fileUtil::delete($fullpath);
					} else {
						unlink($fullpath);
					}
				}
			}
			closedir($dh);
			return rmdir($path);
		}elseif(is_file($path)){
			return unlink($path);
		}
		return false;
	}

	/**
	 * 重命名
	 * @param string $path
	 * @param string $name
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function rename($source,$dest,$overwrite = true) {
		$source = mb_convert_encoding($source,'UTF-8');
		$dest = mb_convert_encoding($dest,'UTF-8');
		if(substr($source,-1) === '/') $source = substr($source,0,-1);
		if(substr($dest,-1) === '/') $dest = substr($dest,0,-1);
		//目标路径不存在则创建
		if(strpos($dest,'/') !== false){
			$dir = substr($dest,0,strripos($dest,'/'));
			if(!is_dir($dir)) mkdir($dir,0777,true);
		}
		return $overwrite || (!is_file($dest) && !is_dir($dest)) ? rename($source,$dest) : false;
	}
}
?>