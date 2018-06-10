<?php

class UploadTool
{
    private $path = UPLOAD_DIR;
    private $error;
    private $size = 1048576; //上传大小
    private $type = array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', ''); //允许类型
    public $info = array(); //允许类型
    public function __construct($path = '', $size = '', $type = '')
    {
        $this->path = empty($path) ? $this->path : $path;
        $this->size = empty($size) ? $this->size : $size;
        $this->type = empty($type) ? $this->type : $type;
    }

    public function up()
    {
        $arr = $this->resetArr();
        foreach ($arr as $name => $item) {
            foreach ($item as $value) {
                if ($this->filter($value)) { //为真就上传
                    $this->move($value, $name);
                } else {
                    //文件出错信息
                    var_dump($this->error);
                    exit;
                }
            }
        }
        return true;

    }

    private function resetArr()
    {
        $arr = array();
        foreach ($_FILES as $name => $item) { //单文件上传  $item  二维数组
            if (is_array($item['name'])) {
                foreach ($item['name'] as $key => $value) {
                    $arr[$name][] = array(
                        'name' => $value,
                        'type' => $item['type'][$key],
                        'size' => $item['size'][$key],
                        'error' => $item['error'][$key],
                        'tmp_name' => $item['tmp_name'][$key],
                    );
                }
            } else {
                $arr[$name][] = $item;
            }
        }
        return $arr;
    }

    public function filter($value)
    {
        switch (true) {
            case $value['error'] == 1:
                $value['error'] = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                $this->error = $value;
                return false;
                break;

            case $value['error'] == 2:
                $value['error'] = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                $this->error = $value;
                return false;
                break;

            case $value['error'] == 3:
                $value['error'] = '文件只有部分被上传';
                $this->error = $value;
                return false;
                break;

            case $value['error'] == 4:
                $value['error'] = '没有文件被上传';
                $this->error = $value;
                return false;
                break;

            case !in_array($value['type'], $this->type):
                $value['error'] = '类型不正确';
                $this->error = $value;
                return false;
                break;

            case $value['size'] > $this->size:
                $value['error'] = '上传文件超过限制';
                $this->error = $value;
                return false;
                break;

            case !is_uploaded_file($value['tmp_name']):
                $value['error'] = '请正确上传文件';
                $this->error = $value;
                return false;
                break;
            default:
                return true;
                break;
        }
    }

    public function move($value, $name)
    {
        $dir = $this->path . date('YmdH') . DS;
        if (!file_exists($dir)) { //检查文件或者目录是否存在
            mkdir($dir, 0777, true); //递归创建文件
            chmod($dir, 0777); //更改文件权限
        }

        $filename = $name . '_' . date('is') . mt_rand(1, 99999); //文件名称
        $suffix = strrchr($value['type'], DS);
        $suffix = str_replace(DS, '.', $suffix);
        $path = $dir . $filename . $suffix; //文件保存路径
        $imgsrc = substr($path, strpos($path, 'app'));

        if (move_uploaded_file($value['tmp_name'], $path)) {
            //返回完整的数组信息
            $this->info[$name]['savepath'][] = $path;
            $this->info[$name]['imgsrc'][] = addslashes($imgsrc);
            return true;
        } else {
            $value['error'] = '文件上传失败！';
            $this->error = $value;
            return false;
        }

    }

}
