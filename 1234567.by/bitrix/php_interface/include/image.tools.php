<?
class CWsImageTools {

    var $disp_width_max=150;                    // used when displaying watermark choices
    var $disp_height_max=80;                    // used when displaying watermark choices
    var $edgePadding = 20;                        // used when placing the watermark near an edge
    var $quality=100;                           // used when generating the final image
    var $default_watermark='/i/watermark.png';  // the default image to use if no watermark was chosen
    var $waterMarkWidth = 108;
    var $waterMarkHeight = 33;
    var $imageCacheDir = "/images/cache/";
    var $waterMarkDir = "/images/watermark/";
    // be sure that the other options we need have some kind of value
    var $saveAs = "jpeg";
    var $vPosition = "center";
    var $hPosition = "center";
    var $wmSize = "0.5";

    public function SetWaterMark( $sorceImg, $w = "", $h = "", $xPosition = 0, $yPosition = 0, $orientation = "")
    {
        if(strpos($sorceImg, $_SERVER["DOCUMENT_ROOT"]) === false)
            $sorceImg = $_SERVER["DOCUMENT_ROOT"].$sorceImg;

        if(!file_exists($sorceImg))
            return false;

        $sorceName = basename($sorceImg);
        $size = getimagesize($sorceImg);
        $xPosition = (int) $xPosition;

        if($size[2]==2 || $size[2]==3)
        {
            // it was a JPEG or PNG image, so we're OK so far
            $original=$sorceImg;
            $target_name=$sorceName;

            $target = $sorceImg;
            $filePath = dirname($sorceImg)."/";
            $targetPath =  str_replace($_SERVER["DOCUMENT_ROOT"], "", $target); //$this->imageCacheDir.$target_name;

            $origInfo = getimagesize($original);
            $origWidth = $origInfo[0];
            $origHeight = $origInfo[1];

            // устанавливаем параметры позиционирования и исходный файл водяного знака
            if(strlen($orientation) > 0)
                $this->orientation = $orientation == "vert" ? "vert" : "gor";

            /*$bSet = $this->SetWatermarkParams($origWidth, $origHeight);

            if(!$bSet)
                return false;
*/
            $watermark = $_SERVER["DOCUMENT_ROOT"].$this->default_watermark;
            $wmTarget = $watermark.'.tmp';

            // размеры водяного знака
            $arWaterMarkSize = getimagesize($watermark);
            $scaleWmKoef = $arWaterMarkSize[0] / $arWaterMarkSize[1];

            // расчитать исходя из размеров исходного изображения
            $this->waterMarkWidth = $arWaterMarkSize[0];
            $this->waterMarkHeight = $arWaterMarkSize[1];

            /*if($arWaterMarkSize[0] > $size[0]) {
                $this->waterMarkWidth = $size[0];
                $this->waterMarkHeight = $this->waterMarkWidth / $scaleWmKoef;
            } else {*/
                $this->waterMarkWidth = $size[0] * $this->wmSize;
                $this->waterMarkHeight = $this->waterMarkWidth / $scaleWmKoef;
            //}

            $waterMarkDestWidth = $this->waterMarkWidth;
            $waterMarkDestHeight = $this->waterMarkHeight;

            // OK, we have what size we want the watermark to be, time to scale the watermark image
            $this->resize_png_image($watermark, $waterMarkDestWidth, $waterMarkDestHeight, $wmTarget);

            // get the size info for this watermark.
            $wmInfo = getimagesize($wmTarget);

            $waterMarkDestWidth = $wmInfo[0];
            $waterMarkDestHeight = $wmInfo[1];

            $differenceX = $origWidth - $waterMarkDestWidth;
            $differenceY = $origHeight - $waterMarkDestHeight;

            $placementX = $xPosition = 0;
            // where to place the watermark?
            switch($this->hPosition){
                // find the X coord for placement
                case 'left':
                    $placementX = 0;
                    break;
                case 'center':
                    $placementX =  round($differenceX / 2);
                    break;
                case 'right':
                    $placementX = $origWidth - $waterMarkDestWidth - $xPosition;
                    break;
            }

            switch($this->vPosition){
                // find the Y coord for placement
                case 'top':
                    $placementY = $this->edgePadding + $yPosition;
                    break;
                case 'center':
                    $placementY =  round($differenceY / 2);
                    break;
                case 'bottom':
                    $placementY = $origHeight - $waterMarkDestHeight - $yPosition;
                    break;
            }

            if($size[2]==3)
                $resultImage = imagecreatefrompng($original);
            else
                $resultImage = imagecreatefromjpeg($original);
            imagealphablending($resultImage, TRUE);

            $finalWaterMarkImage = imagecreatefrompng($wmTarget);
            $finalWaterMarkWidth = imagesx($finalWaterMarkImage);
            $finalWaterMarkHeight = imagesy($finalWaterMarkImage);

            imagecopy($resultImage,
                $finalWaterMarkImage,
                $placementX,
                $placementY,
                0,
                0,
                $finalWaterMarkWidth,
                $finalWaterMarkHeight
            );

            if($size[2]==3){
                imagealphablending($resultImage,FALSE);
                imagesavealpha($resultImage,TRUE);
                imagepng($resultImage,$target,$this->quality);
            }else{
                imagejpeg($resultImage,$target,$this->quality);
            }

            imagedestroy($resultImage);
            imagedestroy($finalWaterMarkImage);
        }

        if($unlinkSorce)
            unlink($sorceImg);

        if(file_exists($target))
            return str_replace($_SERVER["DOCUMENT_ROOT"], "", $targetPath);

        return false;
    }

    function resize_png_image($img,$newWidth,$newHeight,$target){
        $srcImage=imagecreatefrompng($img);
        if($srcImage==''){
            return FALSE;
        }
        $srcWidth=imagesx($srcImage);
        $srcHeight=imagesy($srcImage);
        $percentage=(double)$newWidth/$srcWidth;
        $destHeight=round($srcHeight*$percentage)+1;
        $destWidth=round($srcWidth*$percentage)+1;
        if($destHeight > $newHeight){
            // if the width produces a height bigger than we want, calculate based on height
            $percentage=(double)$newHeight/$srcHeight;
            $destHeight=round($srcHeight*$percentage)+1;
            $destWidth=round($srcWidth*$percentage)+1;
        }
        $destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
        if(!imagealphablending($destImage,FALSE)){
            return FALSE;
        }
        if(!imagesavealpha($destImage,TRUE)){
            return FALSE;
        }
        if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
            return FALSE;
        }
        if(!imagepng($destImage,$target)){
            return FALSE;
        }
        imagedestroy($destImage);
        imagedestroy($srcImage);
        return TRUE;
    }

    function SetWatermarkParams($w, $h)
    {
        $arWatermarks = array(
            "150_300",
            "300_500",
            "500_800"
        );
        $arWatermarkSize = array(
            array(
                "gor" => array(108, 33),
                "vert" => array(22, 110)
            ),
            array(
                "gor" => array(150, 33),
                "vert" => array(37, 152)
            ),
            array(
                "gor" => array(207, 61),
                "vert" => array(46, 208)
            )
        );

        $arHPosition = array(
            "left",
            "center",
            "right"
        );
        $arVPosition = array(
            "top",
            "center",
            "bottom"
        );

        if(!isset($this->orientation))
            $orientation = $w >= $h ? "gor" : "vert";
        else
            $orientation = $this->orientation;

        $sideSize = $orientation == "gor" ? $w : $h;

        $keyIndex = false;

        if($sideSize > 150 and $sideSize <= 300)
            $keyIndex = 0;
        elseif($sideSize > 300 and $sideSize <= 500)
            $keyIndex = 1;
        elseif($sideSize > 500 and $sideSize <= 800)
        {
            // устанавливаем случаное расположение водяного знака
            /*$rIndex = rand(0,2);
            $this->vPosition = $arVPosition[$rIndex];
            $rIndex = rand(0,2);
            $this->hPosition = $arHPosition[$rIndex];*/

            $this->edgePadding = 40;
            $keyIndex = 2;
        }
        if($keyIndex !== false)
        {
            $waterMark1 = $this->waterMarkDir.$arWatermarks[$keyIndex]."_".$orientation.".png";
            $waterMark1 = "/i/watermark.png";

            $arSizes = getimagesize($_SERVER["DOCUMENT_ROOT"].$waterMark1);
            // параметры изображения
            $this->waterMarkWidth = $w;
            $this->waterMarkHeight = $h;

            if(file_exists($_SERVER["DOCUMENT_ROOT"].$waterMark1))
            {
                $this->default_watermark = $waterMark1;
                return true;
            }
        }

        return false;
    }

    /**
     * Функция масшатибрования изображений с поддержкой кеширования
     * Поддерживает разные режимы работы MODE
     * MODE принимает значения: cut, in, inv, width
     * @param str $req "/thumb/".$maxWidth."x".$maxHeight."xMODE".SRC
     * @param bool $needWatermark флаг необходимости установки водяного знака
     */
    public static function Resizer($req, $needWatermark = false)
    {
        $CACHE_IMG_PATH = $_SERVER["DOCUMENT_ROOT"]."/images/cache/";
        $RETURN_IMG_PATH = "/images/cache/";

        //создает каталоги если не существуют
        CheckDirPath($CACHE_IMG_PATH);
        preg_match('/\/thumb\/([0-9]{1,4})x([0-9]{1,4})x([^\/]*)\/(.*)\.(gif|jpg|png|jpeg)/i', $req, $p);

        $path="{$_SERVER["DOCUMENT_ROOT"]}/{$p[4]}.{$p[5]}";
        $temp = preg_replace("/\//", '_', $req);

        // если изображение существует
        if (is_file ($CACHE_IMG_PATH.$temp) == true)
        {
            return $RETURN_IMG_PATH.$temp;
        }

        // Папка для сохранения файла
        $tmpF = explode("/", $p[4]);
        $subFolder = $tmpF[2]."/";
        $subFolderPath = $CACHE_IMG_PATH.$subFolder;

        CheckDirPath($subFolderPath);
        // если изображение существует в подпапках
        if (is_file ($subFolderPath.$temp) == true)
        {
            return $RETURN_IMG_PATH.$subFolder.$temp;
        }

        $RETURN_IMG_PATH = $RETURN_IMG_PATH.$subFolder;
        $CACHE_IMG_PATH = $subFolderPath;

        $i = getImageSize($path);

        if ($p[1] == 0)
            $p[1] = $p[2] / $i[1] * $i[0];
        if ($p[2] == 0)
            $p[2] = $p[1] / $i[0] * $i[1];



        if (($p[1] > $i[0] || $p[2] > $i[1]) && ($p[3]!="in" && $p[3]!="inv" && $p[3]!="trim"))
        {
            $p[1] = $i[0];
            $p[2] = $i[1];
        }


        $im = ImageCreateTrueColor($p[1], $p[2]);
        imageAlphaBlending($im, false);
        switch (strtolower ($p[5]))
        {
            case 'gif' :
                $i0 = ImageCreateFromGif ($path);
                $icolor = imagecolorallocate ($im,255,255,255);
                imagefill ($im,0,0,$icolor);
                break;
            case 'jpg' : case 'jpeg' :
            $i0 = ImageCreateFromJpeg ($path);
            $icolor = imagecolorallocate ($im,255,255,255);
            imagefill ($im,0,0,$icolor);
            break;
            case 'png' :
                $i0 = ImageCreateFromPng ($path);
                $icolor = imagecolorallocate ($im,255,255,255);
                imagefill ($im,0,0,$icolor);

                break;
        }

        switch (strtolower ($p[3]))
        {
            case 'cut' :
                $k_x = $i [0] / $p [1];
                $k_y = $i [1] / $p [2];
                if ($k_x > $k_y)
                    $k = $k_y;
                else
                    $k = $k_x;
                $pn [1] = $i [0] / $k;
                $pn [2] = $i [1] / $k;
                $x = ($p [1] - $pn [1]) / 2;
                $y = ($p [2] - $pn [2]) / 2;


                imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);

                break;

            case 'trim' :
                $bg = imagecolorallocate($i0,0xFF,0xFF,0xFF);     // Background color (yellow)
                self::ImageTrim($i0,$bg);
                //$im = $i0;
                $i[0] = imagesx($i0);
                $i[1] = imagesy($i0);

                if (($i [0] < $p [1]) && ($i [1] < $p [2]))
                {
                    $im = $i0;
                }
                else
                {
                    $k_x = $i [0] / $p [1];
                    $k_y = $i [1] / $p [2];

                    if ($k_x < $k_y)
                        $k = $k_y;
                    else
                        $k = $k_x;

                    $pn [1] = ceil($i [0] / $k);
                    $pn [2] = ceil($i [1] / $k);

                    $x = ceil(($p [1] - $pn [1]) / 2);
                    $y = ceil(($p [2] - $pn [2]) / 2);
                    $bResult = imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);
                }

                /*if(!$bResult)
                    printObj("failed. Params: ".$im."-".$i0."-".$x."-".$y."-"."0.0"."-".$pn[1]."-".$pn[2]."-".$i[0]."-".$i[1]);
                else
                    printObj("Ok. Params: ".$im."-".$i0."-".$x."-".$y."-"."0.0"."-".$pn[1]."-".$pn[2]."-".$i[0]."-".$i[1]);*/


                break;

            case 'in' :

                if (($i [0] < $p [1]) && ($i [1] < $p [2]))
                {
                    $k_x = 1;
                    $k_y = 1;

                }
                else
                {
                    $k_x = $i [0] / $p [1];
                    $k_y = $i [1] / $p [2];

                }

                if ($k_x < $k_y)
                    $k = $k_y;
                else
                    $k = $k_x;

                $pn [1] = $i [0] / $k;
                $pn [2] = $i [1] / $k;

                $x = ($p [1] - $pn [1]) / 2;
                $y = ($p [2] - $pn [2]) / 2;


                $bResult = imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);
                /*if(!$bResult)
                    printObj("failed. Params: ".$im."-".$i0."-".$x."-".$y."-"."0.0"."-".$pn[1]."-".$pn[2]."-".$i[0]."-".$i[1]);
                else
                    printObj("Ok. Params: ".$im."-".$i0."-".$x."-".$y."-"."0.0"."-".$pn[1]."-".$pn[2]."-".$i[0]."-".$i[1]);*/
                // 1 первый параметр изборажение источник
                // 2 изображение которое вставляется
                // 3 4 -х и у с какой точки будет вставятся в изображении источник
                // 5 6 - ширина и высота куда будет вписано изображение


                break;

            case 'inv' :
                $k_x = $i [0] / $p [1];
                $k_y = $i [1] / $p [2];
                if ($k_x < $k_y)
                    $k = $k_y;
                else
                    $k = $k_x;
                $pn [1] = $i [0] / $k;
                $pn [2] = $i [1] / $k;
                $x = ($p [1] - $pn [1]) / 2;
                $y = ($p [2] - $pn [2]) / 2;
                imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);



                break;

            case 'width' :
                $factor = $i[1] / $i[0]; // определяем пропорцию   height / width

                if($factor > 1.35)
                {
                    $pn[1] = $p[1];
                    $scale_factor = $i[0] / $pn[1]; // коэфффициент масштабирования
                    $pn[2] = ceil($i[1] / $scale_factor);
                    $x = 0;
                    $y = 0;
                    if(($p[2] / $pn[2]) < 0.6)
                    {
                        //echo 100 / ($pn[2] * 100) / ($p[2] *1.5);
                        $pn[2] =  (100 / (($pn[2] * 100) / ($p[2] *1.3))) * $pn[2];
                        $newKoef = $i[1] / $pn[2];
                        $pn[1] = $i[0] / $newKoef;

                        $x = ($p [1] - $pn [1]) / 2;
                        //$y = ($p [2] - $pn [2]) / 2;
                    }

                    imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);
                }
                else
                {
                    if (($i [0] < $p [1]) && ($i [1] < $p [2]))
                    {
                        $k_x = 1;
                        $k_y = 1;

                    }
                    else
                    {
                        $k_x = $i [0] / $p [1];
                        $k_y = $i [1] / $p [2];


                    }

                    if ($k_x < $k_y)
                        $k = $k_y;
                    else
                        $k = $k_x;

                    $pn [1] = $i [0] / $k;
                    $pn [2] = $i [1] / $k;

                    $x = ($p [1] - $pn [1]) / 2;
                    $y = ($p [2] - $pn [2]) / 2;
                    imageCopyResampled ($im, $i0, $x, $y, 0, 0, $pn[1], $pn[2], $i[0], $i[1]);
                }
                break;

            default : imageCopyResampled ($im, $i0, 0, 0, 0, 0, $p[1], $p[2], $i[0], $i[1]); break;
        }

        if ($p[1]==55 && $p[2] ==45 && $p[3]=="inv")
        {
            $i0 = ImageCreateFromPng ($_SERVER["DOCUMENT_ROOT"]."/img/video.png");
            imageCopyResampled ($im, $i0, 0, 0, 0, 0, $p[1], $p[2],$p[1], $p[2]);
        }

        switch (strtolower ($p[5])) {
            case 'gif' :imageSaveAlpha($im, true);  @imageGif($im, $CACHE_IMG_PATH.$temp);  break;
            case 'jpg' : case 'jpeg' :  @imageJpeg($im, $CACHE_IMG_PATH.$temp, 100); break;
            case 'png' : imageSaveAlpha($im, true); @imagePng($im, $CACHE_IMG_PATH.$temp); break;
        }

        if($needWatermark)
        {
            $watermark = new CWsImageTools();

            $srcOrientation = $i[0] > $i[1] ? "gor" : "vert";
            $tmp = $watermark->SetWaterMark($RETURN_IMG_PATH.$temp, $p[1], $p[1], $x, $y, $srcOrientation);

            if(file_exists($tmp))
                return $tmp;
        }

        return $RETURN_IMG_PATH.$temp;
    }

    function ImageTrim(&$im, $bg, $pad=null){

        // Calculate padding for each side.
        if (isset($pad)){
            $pp = explode(' ', $pad);
            if (isset($pp[3])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[3]);
            }else if (isset($pp[2])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[1]);
            }else if (isset($pp[1])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[0], (int) $pp[1]);
            }else{
                $p = array_fill(0, 4, (int) $pp[0]);
            }
        }else{
            $p = array_fill(0, 4, 0);
        }

        // Get the image width and height.
        $imw = imagesx($im);
        $imh = imagesy($im);

        // Set the X variables.
        $xmin = $imw;
        $xmax = 0;

        // Start scanning for the edges.
        for ($iy=0; $iy<$imh; $iy++){
            $first = true;
            for ($ix=0; $ix<$imw; $ix++){
                $ndx = imagecolorat($im, $ix, $iy);
                if ($ndx != $bg){
                    if ($xmin > $ix){ $xmin = $ix; }
                    if ($xmax < $ix){ $xmax = $ix; }
                    if (!isset($ymin)){ $ymin = $iy; }
                    $ymax = $iy;
                    if ($first){ $ix = $xmax; $first = false; }
                }
            }
        }

        // The new width and height of the image. (not including padding)
        $imw = 1+$xmax-$xmin; // Image width in pixels
        $imh = 1+$ymax-$ymin; // Image height in pixels

        // Make another image to place the trimmed version in.
        $im2 = imagecreatetruecolor($imw+$p[1]+$p[3], $imh+$p[0]+$p[2]);

        // Make the background of the new image the same as the background of the old one.
        $bg2 = imagecolorallocate($im2, ($bg >> 16) & 0xFF, ($bg >> 8) & 0xFF, $bg & 0xFF);
        imagefill($im2, 0, 0, $bg2);

        // Copy it over to the new image.
        imagecopy($im2, $im, $p[3], $p[0], $xmin, $ymin, $imw, $imh);

        // To finish up, we replace the old image which is referenced.
        $im = $im2;
    }
}