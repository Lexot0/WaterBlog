<?php
/**
 * 上传类
 * 创建：2018-11-05
 * 更新：2023-04-26
 */
class Upload{
    private $file;         //文件信息
    private $fileList;     //文件列表
    private $inputName;    //标签名称
    private $uploadPath;   //上传路径
    private $fileMaxSize;  //最大尺寸
    private $uploadFiles;  //上传文件
    private $allowExt;     //允许上传的文件类型
    public $domain;        //上传成功的链接是否使用域名
    public $path;
    public $imgThumb = false;
    public $fileNameType = 'time';
    public $fileName = false;

    /**
     * 构造函数
     * @param string $inputName input标签的name属性
     * @param string $uploadPath 文件上传路径
     * @param string $fileName 文件名称
     */
    public function __construct($inputName, $uploadPath, $fileName=false){
        $this->allowExt = explode('|','jpg|jpeg|png|gif|bmp|exe|flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid|rar|zip|tar|gz|7z|bz2|cab|iso|chm|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|md|torrent');
        $this->domain = true;
        $this->inputName = $inputName;
        $this->uploadPath = substr($uploadPath,-1)=='/'?$uploadPath:$uploadPath.'/';
        $this->uploadPath = substr($this->uploadPath,0,1)=='/'?substr($this->uploadPath,1):$this->uploadPath;
        $this->path = ROOT.$this->uploadPath;
        $this->imgThumb=false;
        $this->fileNameType='time';
        $this->fileName=$fileName;
        $this->fileList = [];
        $this->file = [
            'name' => null,
            'type' => null,
            'tmp_name' => '',
            'size' => null,
            'errno' => null,
            'error' => null
        ];
    }

    /**
     * 设置上传成功后的文件名称类型
     * @param string $type 名称类型
     */
    public function setNameType($type){
        $type = $type?$type:'time';
        $this->fileNameType = $type;
    }

    /**
     * 设置允许上传的文件后缀格式
     * @param array|string|bool $allowExt 可以为数组或字符串，字符串的扩展名以“|”分割，如果为false，则不限制上传类型
     */
    public function setAllowExt($allowExt){
        if($allowExt){
            if(is_array($allowExt)){
                $this->allowExt = $allowExt;
            }else{
                $this->allowExt = explode('|',$allowExt);
            }
        }else{
            $this->allowExt = false;
        }  
    }

    /**
     * 设置允许上传的文件大小
     * @param int $fileMaxSize 文件大小
     */
    public function setMaxSize($fileMaxSize){
        $this->fileMaxSize = $fileMaxSize * 1024 * 1024;
    }

    /**
     * 获取上传成功的文件数组
     * @return mixed
     */
    public function getUploadFiles(){
        return $this->uploadFiles;
    }

    /**
     * 得到文件上传的错误信息
     * @return array|mixed
     */
    public function getErrorMsg(){
        if (count($this->fileList) == 0) {
            return $this->file['error'];
        } else {
            $errArr = [];
            foreach ($this->fileList as $item) {
                if($item['error']){
                    array_push($errArr, $item['error']);
                }
            }
            return $errArr;
        }
    }

    /**
     * 初始化文件参数
     * @param bool $isList
     */
    private function initFile($isList){
        if ($isList) {
            foreach ($_FILES[$this->inputName] as $key => $item) {
                for ($i = 0; $i< count($item); $i++) {
                    if ($key == 'error') {
                        $this->fileList[$i]['error'] = null;
                        $this->fileList[$i]['errno'] = $item[$i];
                    } else {
                        $this->fileList[$i][$key] = $item[$i];
                    }
                }
            }
        } else {
            $this->file['name'] = $_FILES[$this->inputName]['name'];
            $this->file['type'] = $_FILES[$this->inputName]['type'];
            $this->file['tmp_name'] = $_FILES[$this->inputName]['tmp_name'];
            $this->file['size'] = $_FILES[$this->inputName]['size'];
            $this->file['errno'] = $_FILES[$this->inputName]['error'];
        }
    }

    /**
     * 上传错误检查
     * @param $errno
     * @return null|string
     */
    private function errorCheck($errno){
        switch ($errno) {
            case UPLOAD_ERR_OK:
                return null;
            case UPLOAD_ERR_INI_SIZE:
                return '文件尺寸超过服务器限制';
            case UPLOAD_ERR_FORM_SIZE:
                return '文件尺寸超过表单限制';
            case UPLOAD_ERR_PARTIAL:
                return '只有部分文件被上传';
            case UPLOAD_ERR_NO_FILE:
                return '没有文件被上传';
            case UPLOAD_ERR_NO_TMP_DIR:
                return '找不到临时文件夹';
            case UPLOAD_ERR_CANT_WRITE:
                return '文件写入失败';
            case UPLOAD_ERR_EXTENSION:
                return '上传被扩展程序中断';
        }
    }

    /**
     * 上传文件校验
     * @param array $file
     * @throws Exception
     */
    private function fileCheck($file){
        //文件上传过程是否顺利
        if ($file['errno'] != 0) {
            $error = $this->errorCheck($file['errno']);
            throw new Exception($error);
        }
        //文件尺寸是否符合要求
        if (!empty($this->fileMaxSize) && $file['size'] > $this->fileMaxSize) {
            throw new Exception('文件尺寸超过' . ($this->fileMaxSize / 1024) . 'KB');
        }
        //文件类型是否符合要求
        if($this->allowExt){
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $this->allowExt)) {
                throw new Exception('不符合要求的文件类型');
            }
        }
        //文件上传方式是否为HTTP
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new Exception('文件不是通过HTTP方式上传的');
        }
        //文件是否可以读取
        if (!filesize($file['tmp_name'])) {
            throw new Exception('文件损坏');
        }
        //检查上传路径是否存在
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0777, true);
        }
    }

    /**
     * 单文件上传，成功返回true
     * @return bool
     */
    public function singleFile(){
        $this->initFile(false);
        try{
            $this->fileCheck($this->file);
            $info = pathinfo($this->fileList['name']);
            $ext = $info['extension'];
            if($this->fileName){
                $name = $this->fileName; 
            }else{
                if ($this->fileNameType == 'name') {
                    $name = $info['basename'];
                }else{
                    $name = date('Ymdhis').'1.'.$ext;
                }
            }
            if(move_uploaded_file($this->file['tmp_name'], $this->path.$name)) {
                $t=$this->imgThumb;
                $this->uploadFiles = [
                    'uploadName'=>$this->file['name'],
                    'newName'=>$name,
                    'ext'=>$ext,
                    'url'=> ($this->domain ? getHost().HOME : HOME).$this->uploadPath . $name,
                    'size'=>$this->file['size'],
                    'thumb'=>$t?imgThumb($this->path.$name,$t['width'],$t['height'],$t['clip'],$t['pre']):''
                ];
            }else{
                throw new Exception('文件上传失败');
            }
        }catch(Exception $e){
            $this->file['error'] = $e->getMessage();
            if(is_file($this->file['tmp_name']))@unlink($this->file['tmp_name']);
        }
        return empty($this->file['error']) ? true : false;
    }

    /**
     * 多文件上传，全部成功返回true
     * @return bool
     */
    public function multiFile(){
        $this->initFile(true);
        $this->uploadFiles = [];
        foreach($this->fileList as $k=>$v){
            try {
                $this->fileCheck($v);
                $info = pathinfo($v['name']);
                $ext = $info['extension'];
                if($this->fileName){
                    $name = $this->fileName; 
                }else{
                    if ($this->fileNameType == 'name') {
                        $name = $info['basename'];
                    }else{
                        $name = date('Ymdhis').$k.'.'.$ext;
                    }
                }
                if (move_uploaded_file($v['tmp_name'], $this->path.$name)){
                    $t=$this->imgThumb;
                    $this->uploadFiles[] = [
                        'uploadName'=>$v['name'],
                        'name'=>$name,
                        'ext'=>$ext,
                        'url'=>($this->domain ? getHost().HOME : HOME).$this->uploadPath.$name,
                        'size'=>$v['size'],
                        'thumb'=>$t?imgThumb($this->path.$name,$t['width'],$t['height'],$t['clip'],$t['pre']):''
                    ];
                } else {
                    throw new Exception('文件上传失败');
                }
            } catch (Exception $e) {
                $this->fileList[$k]['error'] = $e->getMessage();
                if (is_file($v['tmp_name']))@unlink($v['tmp_name']);
            }
        }
        foreach ($this->fileList as $item) {
            if (!empty($item['error']))return false;
        }
        return true;
    }

    /**
     * 设置生成缩略图：
     * @param int $width 缩略图宽度
     * @param int $height 缩略图高度
     * @param bool $clip true:裁剪缩略 false:全图等比例缩略
     * @param string $pre 缩缩略图前缀
     */
    public function setImgThumb($width=300, $height=300, $clip=true, $pre='thumb_'){
        $this->imgThumb=['width'=>$width,'height'=>$height,'clip'=>$clip,'pre'=>$pre];
    }
}
?>