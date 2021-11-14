<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Image Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnHelperImage extends AnObject
{
    /**
     * Parses a string based size convention to width and height.
     *
     * @param string|array $size
     *
     * @return array
     */
    public static function parseSize($size)
    {
        $height = null;
        $width = null;

        if (count((array) $size) == 2 || strpos($size, 'x')) {
            list($width, $height) = is_array($size) ? $size : explode('x', $size);
        } else {
            $size = (array) $size;
            $width = (int) $size[0];
        }

        return array($width,$height);
    }

    /**
     * Resizes an image using the passed size and return the resized image resource.
     *
     * @param resource $image The image resource
     * @param string   $size  The image size
     *
     * @return resource
     */
    public static function resize($image, $size)
    {
        if (!$image) {
            return false;
        }

        list($width, $height) = self::parseSize($size);

        if ($height == 'auto' && $width == 'auto') {
            return false;
        }

        $o_wd = imagesx($image);
        $o_ht = imagesy($image);

        $x = $y = 0;
        if ($height == 'auto') {
            $height = round(($width / $o_wd) * $o_ht);
        } elseif ($width == 'auto') {
            $width = round(($height / $o_ht) * $o_wd);
        } elseif ($width && !$height) {
            //make square image
            $height = $width;
            if ($o_wd > $o_ht) {
                $x = ceil(($width - $height) / 2);
                $o_wd = $o_ht;
            } elseif ($o_ht > $o_wd) {
                $y = ceil(($height - $width) / 2);
                $o_ht = $o_wd;
            }
        } else {
            $w = round($o_wd * $height / $o_ht);
            $h = round($o_ht * $width / $o_wd);

            if (($height - $h) < ($width - $w)) {
                $width = &$w;
            } else {
                $height = &$h;
            }
        }

        $tmp = imageCreateTrueColor($width, $height);
        imagesavealpha($tmp, true);
        $color = imagecolorallocatealpha($tmp, 0x00, 0x00, 0x00, 127);
        imagefill($tmp, 0, 0, $color);
        imagecopyresampled($tmp, $image, 0, 0, $x, $y, $width, $height, $o_wd, $o_ht);

        return $tmp;
    }

    /**
     * Outputs an image to the desired image type.
     *
     * @param  resource $image The image resource
     * @param  string   $type  The image mimetype
     *
     * @return string
     */
    public static function output($image, $type)
    {
        $parts = explode('/', $type);
        $func = 'image'.strtolower($parts[1]);
        if (!function_exists($func)) {
            return;
        }

        $args = null;

        switch ($parts[1]) {
            case 'jpeg':
                $args = array($image, null, 100);
                break;
            case 'png':
                $args = array($image, null, 9);
                break;
            case 'gif':
                $args = array($image, null);
                break;
            default:
                return;
        }
        ob_start();
        call_user_func_array($func, $args);
        $image = ob_get_clean();

        return $image;
    }
}
