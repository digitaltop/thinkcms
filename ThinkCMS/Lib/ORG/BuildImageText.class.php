<?php

/* PHP图片加文字水印类库

  该类库暂时只支持文字水印，位置为右下角，颜色随机

  调用方法：
  1、在需要加水印的文件顶部引入类库：
  include_once 'imageClass.php';
  2、声明新类：
  $tpl=new BuildImageText;
  3、给图片水印提供参数：
  $tpl->img(图片路径,水印文字,字体路径,字体大小,字体角度);
  比如：$tpl->img('abc.jpg','这是水印文字','ziti.ttf',30,0)

 */

class BuildImageText {

    private $image;
    private $img_info;
    private $img_width;
    private $img_height;
    private $img_im;
    private $img_text;
    private $img_ttf = '';
    private $img_new;
    private $img_text_size;
    private $img_jd;
    private $img_type;
    private $R;
    private $G;
    private $B;
    private $X;
    private $Y;

    function img($img = '', $txt = '', $ttf = '', $size = 12, $jiaodu = 0, $R = 0, $G = 0, $B = 0, $X = 0, $Y = 0) {
        if (isset($img) && file_exists($img)) {//检测图片是否存在
            $this->image = $img;
            $this->img_text = $txt;
            $this->img_text_size = $size;
            $this->img_jd = $jiaodu;

            $this->R = ($R <> 0) ? $R : rand(0, 255);
            $this->G = ($G <> 0) ? $G : rand(0, 255);
            $this->B = ($B <> 0) ? $B : rand(0, 255);
            $this->X = $X;
            $this->Y = $Y;
            if (file_exists($ttf)) {
                $this->img_ttf = $ttf;
            } else {
                exit('字体文件：' . $ttf . '不存在！');
            }
            $this->imgyesno();
        } else {
            exit('图片文件:' . $img . '不存在');
        }
    }

    private function imgyesno() {

        $this->img_info = getimagesize($this->image);
        $this->img_width = $this->img_info[0]; //图片宽
        $this->img_height = $this->img_info[1]; //图片高
//检测图片类型
        switch ($this->img_info[2]) {
            case 1:$this->img_im = imagecreatefromgif($this->image);
                $this->img_type = 'gif';
                break;
            case 2:$this->img_im = imagecreatefromjpeg($this->image);
                $this->img_type = 'jpeg';
                break;
            case 3:$this->img_im = imagecreatefrompng($this->image);
                $this->img_type = 'png';
                break;
            default:exit('图片格式不支持水印');
        }

        $this->img_text();
    }

    private function img_text() {

        imagealphablending($this->img_im, true);

//设定颜色
        $color = imagecolorallocate($this->img_im, $this->R, $this->G, $this->B);
        $txt_height = $this->img_text_size;
        $txt_jiaodu = $this->img_jd;
        $ttf_im = imagettfbbox($txt_height, $txt_jiaodu, $this->img_ttf, $this->img_text);
        $w = $ttf_im[2] - $ttf_im[6];
        $h = $ttf_im[3] - $ttf_im[7];
//$w = $ttf_im[7]; 
//$h = $ttf_im[8]; 

        unset($ttf_im);

        $txt_y = ($this->Y > 0) ? $this->Y : $this->img_height - $h;
        $txt_x = ($this->X > 0) ? $this->X : $this->img_width - $w;
//$txt_y     =0;
//$txt_x     =0;



        $this->img_new = @imagettftext($this->img_im, $txt_height, $txt_jiaodu, $txt_x, $txt_y, $color, $this->img_ttf, $this->img_text);

        //@unlink($this->image); //删除图片
        $this->output($this->img_im, $this->img_type);
//
//        switch ($this->img_info[2]) {//取得背景图片的格式 
//            case 1:imagegif($this->img_im, $this->image);
//                break;
//            case 2:imagejpeg($this->img_im, $this->image);
//                break;
//            case 3:imagepng($this->img_im, $this->image);
//                break;
//            default: exit('水印图片失败');
//        }
    }

//显示图片
    static function output($im, $type = 'png', $filename = '') {
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        if (empty($filename)) {
            $ImageFun($im);
        } else {
            $ImageFun($im, $filename);
        }
        imagedestroy($im);
    }

//释放内存
    private function img_nothing() {
        unset($this->img_info);
        imagedestroy($this->img_im);
    }

}