<?php
class ImageTool
{
    public $error_info;
    /**
     * 生成缩略图，补白
     *
     * @param $src_file
     * @param $max_w;
     * @param $max_h;
     *
     * @return 缩略图的图片地址。失败false！
     */
    public function makeThumb($src_file, $max_w, $max_h)
    {
        //判断原图片是否存在
        if (!file_exists($src_file)) {
            $this->error_info = '源文件不存在';
            return false;
        }
//var_dump($src_file);die;
        //计算原图的宽高
        $src_info = getimagesize($src_file);
        $src_w = $src_info[0]; //原图宽
        $src_h = $src_info[1]; //原图高

        //在增加一个判断！
        //如果原图尺寸小于范围（缩略图尺寸）
        if ($src_w < $max_w && $src_h < $max_h) {
            //则不用判断，直接用原图的
            $dst_w = $src_w;
            $dst_h = $src_h;
        } else {
            //比较 宽之比 与 高之比
            if ($src_w / $max_w > $src_h / $max_h) {
                //宽应该缩放的多
                $dst_w = $max_w; //缩略图的宽为范围的宽
                $dst_h = $src_h / $src_w * $dst_w; //按照原图的宽高比将求出缩略图高
            } else {
                $dst_h = $max_h;
                $dst_w = $src_w / $src_h * $dst_h;
            }
        }
        //取得原始文件的路径与名字
        $src_dir = dirname($src_file); //取得路径中目录部分
        $src_basename = basename($src_file); //取得路径中文件名部分,默认含后缀

        //文件名点前的字符+'_thumb'+点后的字符！
        //strrpos ()  字符中目标出现的最后位置！返回数字！  strrchr（）返回字符中目标出现到最后的字符串！包含目标.！ 返回string
        $suffix = strrchr($src_basename, '.');
        $end = substr($suffix, 1);
        $imagecreatefunc = 'imagecreatefrom' . $end;
        //创建画布
        $src_img = $imagecreatefunc($src_file); //基于已有图片创建
        //缩略图的大小一致！
        $dst_img = imagecreatetruecolor($max_w, $max_h); //创建一个新的画布

        //为缩略图确定颜色,蓝色
        $white = imagecolorallocate($dst_img, 255, 255, 255);
        imagefill($dst_img, 0, 0, $white); //填充

        //采样，拷贝，修改大小。注意放置的位置！
        $dst_x = ($max_w - $dst_w) / 2;
        $dst_y = ($max_h - $dst_h) / 2;
        imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

        //导出

        $thumb_file = substr($src_basename, 0, strrpos($src_basename, '.')) . '_thumb' . $suffix;
        $imagefunc = 'image' . $end;
        $imagefunc($dst_img, $src_dir . DS . $thumb_file);

        //销毁
        imagedestroy($dst_img);
        imagedestroy($src_img);

        //返回缩略图的保存路径
        return basename($src_dir) . '/' . $thumb_file;
    }
}
