<?php

namespace TNQSoft\CommonBundle\Util;

class Utility
{
    /**
     * Generate Slug from string Unicode
     *
     * @param string $str
     * @return string
     */
    static public function slugify($str) {
    	$tmp = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    	$tmp = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $tmp);
    	$tmp = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $tmp);
    	$tmp = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $tmp);
    	$tmp = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $tmp);
    	$tmp = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $tmp);
    	$tmp = preg_replace("/(đ)/", 'd', $tmp);
    	$tmp = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $tmp);
    	$tmp = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $tmp);
    	$tmp = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $tmp);
    	$tmp = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $tmp);
    	$tmp = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $tmp);
    	$tmp = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $tmp);
    	$tmp = preg_replace("/(Đ)/", 'D', $tmp);
    	$tmp = strtolower(trim($tmp));
    	//$tmp = str_replace('-','',$tmp);
    	$tmp = str_replace(' ', '-', $tmp);
    	$tmp = str_replace('_', '-', $tmp);
    	$tmp = str_replace('.', '', $tmp);
    	$tmp = str_replace("'", '', $tmp);
    	$tmp = str_replace('"', '', $tmp);
    	$tmp = str_replace('"', '', $tmp);
    	$tmp = str_replace('"', '', $tmp);
    	$tmp = str_replace("'", '', $tmp);
    	$tmp = str_replace('̀', '', $tmp);
    	$tmp = str_replace('&', '', $tmp);
    	$tmp = str_replace('@', '', $tmp);
    	$tmp = str_replace('^', '', $tmp);
    	$tmp = str_replace('=', '', $tmp);
    	$tmp = str_replace('+', '', $tmp);
    	$tmp = str_replace(':', '', $tmp);
    	$tmp = str_replace(',', '', $tmp);
    	$tmp = str_replace('{', '', $tmp);
    	$tmp = str_replace('}', '', $tmp);
    	$tmp = str_replace('?', '', $tmp);
    	$tmp = str_replace('\\', '', $tmp);
    	$tmp = str_replace('/', '', $tmp);
    	$tmp = str_replace('quot;', '', $tmp);
    	return $tmp;
    }

    /**
     * Generate random string
     *
     * @param  int  $length
     * @return string
     */
    static public function stringRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }


    /**
     * Delete all file and sub folder
     *
     * @param string $dirPath
     */
    static public function deleteDirectory($dirPath) {
        if (is_dir($dirPath)) {
            $objects = scandir($dirPath);
            foreach ($objects as $object) {
                if ($object != "." && $object !="..") {
                    if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                        static::deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dirPath);
        }
    }

    /**
     * Get IP
     *
     * @return string
     * @SuppressWarnings(PHPMD)
     */
    static public function getIP()
    {
        $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ipList = explode(',', $_SERVER[$key]);
                return $ipList[0];
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (gethostbyname(gethostname()));
    }

    /**
     * Check age over 18
     *
     * @param  string  $birthday with format like yyyy-mm-dd
     * @return boolean
     */
    static public function isOver18($birthday)
    {
        if(null === $birthday) {
            return false;
        }
        $birthDate = strtotime('+18 years', strtotime($birthday));
        if($birthDate < time())  {
            return true;
        }

        return false;
    }

    /**
     * Get GUID
     *
     * @return string
     */
    static public function getGUID() {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12);
            return $uuid;
        }
    }

    /**
     * Format Duration
     *
     * @param  integer $duration
     * @return string
     */
    static public function formatDuration($duration)
    {
        $result = gmdate("H\\h i\\m s\\s", $duration);

        if ($duration < 60) {
            $result = gmdate("s\\s", $duration);
        } elseif ($duration > 60 && $duration < 3600) {
            $result = gmdate("i\\m s\\s", $duration);
        }

        return preg_replace('/(0)([0-9])/i', '$2', $result);
    }
}
