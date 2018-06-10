<?php
//工具类验证码
/*$code="回到收费后以欧普匹配以后";
echo mb_substr($code,5,1);die;
如果验证码出现汉字，不要把汉字字符串直接当数组取$code[1]，会乱码！
要么用mb_substr($code,5,1)，要么用汉字一维数组！
 */
class CaptchaTool
{
    private $width;
    private $height;
    private $fontSize;
    private $codelen;
    private $image;

    public function generate($fontSize = 0, $width = 0, $height = 0, $codelen = 0)
    {
        $this->width = empty($width) ? 145 : $width;
        $this->height = empty($height) ? 24 : $height;
        $this->fontSize = empty($fontSize) ? 14 : $fontSize;
        $this->codelen = empty($codelen) ? 4 : $codelen;
        $i = mt_rand(1, 5);
        $this->image = imagecreatefromjpeg("./framework/tool/captcha/captcha_bg{$i}.jpg");
        $prepareStr = "ADF14SEDFG43";
        $length = strlen($prepareStr);
        $code = '';
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        for ($i = 0; $i < $this->codelen; ++$i) {
            $posx = $i * $this->width / 4 + $this->fontSize / 2;
            $posy = $this->height / 2 + $this->fontSize / 2;
            if (mt_rand(0, 1) == 1) {
                $color = $white;
            } else {
                $color = $black;
            }
            $code .= $text = substr($prepareStr, mt_rand(0, $length - 1), 1);
            imagettftext($this->image, $this->fontSize, 0, $posx, $posy, $color, './framework/tool/font/ygyxsziti2.0.ttf', $text);
        }
        @session_start(); //虽然已经开启，但是为了严谨性，加上！
        $_SESSION['captcha_code'] = $code;
        $this->confuse();
        $this->output();

    }

    public function confuse($num = 10)
    {
        for ($i = 1; $i <= $num; ++$i) {
            $color = imagecolorallocate($this->image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($this->image, mt_rand(1, $this->width), mt_rand(1, $this->height - $this->fontSize), mt_rand(1, $this->width), mt_rand(1, $this->height - $this->fontSize), $color);
            //雪花功能
            /* imagestring($this->image, $i, mt_rand(1,$this->width), mt_rand(1,$this->height-$this->fontSize), '*', $color);*/
        }
    }

    public function output()
    {
        ob_clean();
        header('content-type:image/jpg;charset=utf-8');
        imagejpeg($this->image);
        imagedestroy($this->image);
    }

    public function checkCaptcha($code)
    {
        @session_start();
        /*var_dump($code);
        var_dump($_SESSION['captcha_code']);*/
        return $code == $_SESSION['captcha_code'];
    }

}
