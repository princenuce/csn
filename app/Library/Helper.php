<?php

namespace App\Library;

use Request;

class Helper {
    public static $noindex            = 0;
    public static $noadsense          = 0;
    public static $template           = 'template-pc';
    public static $is_detect_template = 0;
    public function __construct() {}

    public static function template() {
        if (self::$is_detect_template) {
            return self::$template;
        }

        if (preg_match('#g.net#i', Request::server('HTTP_HOST'))) {
            self::$template = 'template-mobile';
        }

        return self::$template;
    }

    public static function link($id, $slug) {
        return '/bai-hat/' . $slug . '-' . $id . '.html';
    }

    public static function curl_shell($url) {
        $cmd = 'curl -Ls -m 3 "' . $url . '"';
        return shell_exec($cmd);
    }

    public static function nonuni($str) {
        $str = html_entity_decode(trim($str), ENT_QUOTES, 'UTF-8');
        $A   = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            ''  => '[^a-z0-9-\s\)\(\]\[]',
        );
        $B = array(
            '-' => '[\s\W]+',
            ''  => '[^A-z0-9\-]',
        );

        foreach ($A as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        $str = trim($str);
        foreach ($B as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return strtolower(trim($str, '-'));
    }

    public static function format_size($size) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log((Int) $size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
