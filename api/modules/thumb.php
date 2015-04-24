<?php
/**
 * Created by PhpStorm.
 * User: cookie
 * Date: 4/22/15
 * Time: 6:43 PM
 */
class thumb
{
    function Create($thumbWidth, $path) {
        $fullpath = Config::$upload_dir . $path;

        if(is_dir($fullpath))
            return;


        $isImage = $this->getSupportedMime($fullpath);

        if($isImage) {

            if($thumbWidth == 'full')
                return $this->Full($fullpath);

            $img = $this->imageCreateFromAny($fullpath);
            $img = $this->resizeImage($img, $thumbWidth);

            header_remove();
            header('Content-Type: image/jpeg');

            imagejpeg($img);

            imagedestroy($img);
            exit;
            //an image
        }


        return "Unsupported Format";

    }


    function getSupportedMime($path) {
        try {
            $supportedExt = ['jpg', 'gif', 'png', 'bmp'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if(!in_array($ext, $supportedExt))
                return false;

            $type = exif_imagetype($path); // [] if you don't have exif you could use getImageSize()

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $mime = 'image/jpeg';
                    break;
                case IMAGETYPE_GIF:
                    $mime = 'image/gif';
                    break;
                case IMAGETYPE_PNG:
                    $mime = 'image/png';
                    break;
                case IMAGETYPE_BMP:
                    $mime = 'image/bmp';
                default:
                    $mime = false;
            }
            return $mime;
        }
        catch(Exception $e) {
            return false;
        }
    }


    function Full($path)
    {
        $mime = $this->getSupportedMime($path);
        if ($mime) {
            header('Content-type: ' . $mime);
            header('Content-length: ' . filesize($path));

            $file = @ fopen($path, 'rb');
            if ($file) {
                fpassthru($file);
            }
        }

        exit;
    }

    function resizeImage($img, $thumbWidth)
    {
        $width = imagesx( $img );
        $height = imagesy( $img );

        // calculate thumbnail size
        $new_width = $thumbWidth;
        $new_height = floor( $height * ( $thumbWidth / $width ) );

        $thumb_img = imagecreatetruecolor( $new_width, $new_height );
        // copy and resize old image into new image
        imagecopyresized( $thumb_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

        return $thumb_img;
    }

    function imageCreateFromAny($filepath) {
        $type = $this->getSupportedMime($filepath);

        if (!$type)
            return false;

        switch ($type) {
            case "image/gif" :
                $im = imageCreateFromGif($filepath);
                break;
            case "image/jpeg" :
                $im = imageCreateFromJpeg($filepath);
                break;
            case "image/png" :
                $im = imageCreateFromPng($filepath);
                break;
            case "image/bmp" :
                $im = imageCreateFromBmp($filepath);
                break;
        }
        return $im;
    }
}