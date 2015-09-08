<?php

/**
 * 常用小工具类
 *
 * @author soft456@gmail.com
 * @datetime 2014-07-13
 *
 * @copyright  Copyright (c) 2014 Hubei BOSHENG Digital Education Services Co., Ltd.
 */
class Com_Tools {

    /**
     *  检测一个弱类型字符串是否有值，或者是否长度是否大于多少位——utf-8
     * @param string $str
     * @param int $len 长度
     * @return boolean
     */
    public static function isStringHavValue($str, $len = 1) {
        $strongTypeStr = self::toUtf8((string) $str);

        return (strlen($strongTypeStr) >= $len);
    }

    /**
     *  身份证号码末尾大小写转换
     * 
     * @param string $idCards 身份证号码
     * @return string 转换后的号码
     */
    public static function idCardsConvertCase($idCards) {

        $idCardsLastLetter = substr($idCards, -1);
        if (strtolower($idCardsLastLetter) !== 'x') {
            return FALSE;
        }

        $baseIdcards = substr($idCards, 0, strlen($idCards) - 1);

        return (120 === ord($idCardsLastLetter)) ? $baseIdcards . 'X' : $baseIdcards . 'x';
    }

    /**
     *  将一维数组插入到指定位置的二维数组中
     * 
     * @param array $rs 被插入的二维数组,必须是索引数组，即带有数字索引的数组
     * @param array $insertRs 待插入的一位数组
     * @param int $pos 插入位置
     * @return boolean | array
     */
    public static function arrayInsertArray($rs, $insertRs, $pos) {
        //参数检测
        if (!$rs || !$insertRs || !is_array($rs) || !is_array($insertRs)) {
            return FALSE;
        }

        //第一个参数不是二维数组
        if (!self::isMultiArray($rs)) {
            return FALSE;
        }

        //获取key
        $rsKey = array_unique(array_merge(array_keys($rs[0]), array_keys($insertRs)));

        //与被插入数组KEY一致的插入数组封装
        $newInsertRs = array();
        foreach ($rsKey as $value) {
            $newInsertRs[$value] = isset($insertRs[$value]) ? $insertRs[$value] : '';
        }

        //如果数组个数少于插入点，直接追加在最后即可
        if (count($rs) < $pos) {
            $rs[] = $newInsertRs;
            return $rs;
        }

        //插入到指定位置
        $retData = array();
        foreach ($rs as $key => $value) {
            if ($pos != $key) {
                $retData[] = $value;
                continue;
            }

            $retData[] = $newInsertRs;
            $retData[] = $value;
        }

        return $retData;
    }

    /**
     *  生成随机密码
     * 
     * @return string
     */
    public static function makeRandPwd() {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $cnt = strlen($chars);
        //$chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';

        $code = $chars[mt_rand(23, $cnt - 10)] //大写
                . $chars[mt_rand(0, $cnt - 1)]
                . $chars[mt_rand(0, $cnt - 1)]
                . $chars[mt_rand(0, 22)] //小写
                . $chars[mt_rand(0, $cnt - 1)]
                . $chars[mt_rand(0, $cnt - 1)]
                . $chars[mt_rand($cnt - 10, $cnt - 1)]
                . $chars[mt_rand(0, $cnt - 1)];

        return $code;
    }

    /**
     *  将三维数组降级成二维，
     *  必须第三维数组的key一样。
     * 
     * @param array $data
     * @return array
     */
    public static function arrayThreeToTwo($data) {
        $ret = array();
        foreach ($data as $value) {
            if (self::isMultiArray($value)) {
                $ret = array_merge($ret, $value);
                continue;
            }

            $ret[] = $value;
        }
        return $ret;
    }

    /**
     *  检测是否JSON串
     * @param string $str
     * @return boolean
     */
    public static function isJsonStr($str) {
//        json_decode($str);
//        return (json_last_error() == JSON_ERROR_NONE);

        return is_null(json_decode($str));
    }

    /**
     *  将数组中没有子数组的项按子数组个数填充完整;
     * 
     * @param array $rs
     * @return array
     */
    public static function arrayPaddedBySubArray($rs) {
        $ret = $oneRs = $twoRs = $subRs = array();
        //分离子数组
        foreach ($rs as $key => $value) {
            if (!is_array($value)) {
                $oneRs[$key] = $value;
            } else {
                $twoRs[$key] = $value;
            }
        }

        //按二维数组填充
        $i = 0;
        foreach ($twoRs as $key => $value) {
            foreach ($value as $subV) {
                if (in_array($subV, $subRs)) {
                    continue;
                }
                $subRs[] = $subV;
                $currRs = is_array($subV) ? $subV : array($key => $subV);
                $ret[$i] = array_merge($oneRs, $currRs);
                $i++;
            }
        }

        return $ret ? $ret : $rs;
    }

    /**
     *  拼凑社区用户头像地址
     * 
     * @param string $fileName 用户上传或默认头像图片文件名
     * @param int $size 头衔尺寸规格 160 64 40 16
     * @return string
     */
    public static function getAvatarPath($fileName, $size) {
        $info = pathinfo($fileName);
        $path = (FALSE === strpos($fileName, 'info-')) ? HTTP_MFS_IMG : (HTTP_UI . 'common/image/');
        $path = str_replace('https', 'http', $path);
        return $path . $info['filename'] . '-' . $size . '.' . $info['extension'];
    }

    /**
     * 根据生日返回星座
     * @param string $sr
     * @return string
     */
    public static function getConstellationByBirthday($birthday) {
        $names = array('魔羯座', '水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座',
            '狮子座', '处女座', '天秤座', '天蝎座', '射手座', '魔羯座');
        $times = array(
            '0101' => '0119', '0120' => '0218', '0219' => '0320', '0321' => '0419', '0420' => '0520',
            '0521' => '0621', '0622' => '0722', '0723' => '0822', '0823' => '0922', '0923' => '1023',
            '1024' => '1121', '1122' => '1221', '1222' => '1231'
        );

        $i = 0;
        foreach ($times as $s => $e) {
            if ($birthday >= $s && $birthday <= $e) {
                break;
            }
            $i++;
        }
        return $i >= 13 ? null : $names[$i];
    }

    /**
     *  检测字符串是否UTF8
     * 
     * @param string $str
     * @return boolean
     */
    public static function isUtf8($str) {
        return ($str === iconv('UTF-8', 'UTF-8//IGNORE', $str));
    }

    /**
     *  递归转换成utf8编码
     * 
     * @param string|array $data
     * @return string|array
     */
    public static function toUtf8($data) {
        //字符串
        if (!is_array($data)) {
            if ($data === iconv('UTF-8', 'UTF-8//IGNORE', $data)) {
                return $data;
            }

            return self::getUTFString($data);
        }

        foreach ($data as &$value) {
            $value = self::toUtf8($value);
        }

        return $data;
    }

    /**
     * 编码转换成 Gbk
     * 
     * @param $data string
     * @return string
     */
    public static function getGbkString($string) {
        $encoding = mb_detect_encoding($string, array('ASCII', 'GB2312', 'UTF-8', 'BIG5'));
        return mb_convert_encoding($string, 'GBK', $encoding);
    }

    /**
     * 编码转换成 utf-8
     * 
     * @param $data string
     * @return string
     */
    public static function getUTFString($string) {
        $encoding = mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'BIG5'));
        return mb_convert_encoding($string, 'utf-8', $encoding);
    }

    /**
     *  替换字符串中的预替换变量为数组中的值
     * @example $str = '<a href="{URL}">{NAME}</a>';
     *           $data = array(
     *                  'URL'  => 'http://www.dodoedu.com',
     *                  'NAME' => '多多社区'
     *           );
     *           返回值：<a href="http://www.dodoedu.com">多多社区</a>
     * 
     * @param string $str 
     * @param array $data
     * @return string
     */
    public static function replaceVar($str, $data) {
        if (!$str || !$data) {
            return $str;
        }

        $rs = array();
        if (!preg_match_all('/{(.*?)}/', $str, $rs)) {
            return $data;
        }

        foreach ($rs[1] as $value) {
//            ECHO '<BR>{' . $value . '}'.' === '. $data[$value].'<BR>';
            $data[$value] && $str = str_replace('{' . $value . '}', $data[$value], $str);
        }

        return $str;
    }

    /**
     * 判断是否为身份证
     * 
     * @param string $str
     * @return boolean
     */
    public static function isCards($str) {
        $len = strlen((string) $str);
        if (!in_array($len, array(15, 18))) {
            return false;
        }
        return true;
    }

    /**
     * 
     * @param array $data 要排序的二维数组
     * @param string $fieldName 要排序的字段名
     * @param int $sortString SORT_ASC | SORT_DESC
     * @return array
     */
    public static function arrayMultiSortByOnField(array $data, $fieldName, $sortString = SORT_ASC) {
        if (!$data) {
            return $data;
        }

        $fieldRs = array();
        foreach ($data as $key => $value) {
            $fieldRs[$key] = $value[$fieldName];
        }

        array_multisort($fieldRs, $sortString, $data);

        return $data;
    }

    /**
     *  输出调试信息
     * 
     * @param string|array $msg 要输出的数组或字符串
     * @param string $title 信息提示标题
     * @param string $spaceChar 换行符
     * @param boolean $isVarDump 是否var_dump方式输出
     */
    public static function debug($msg, $title = null, $spaceChar = '<br>', $isVarDump = FALSE) {

        $titleMsg = $title ? $title . ' ==> ' : '';
        echo $spaceChar . $titleMsg;

        if ($isVarDump) {
            var_dump($msg);
        } else {
            if (is_array($msg)) {
                print_r($msg);
            } else {
                echo $msg;
            }
        }

        echo $spaceChar;
    }

    /**
     * 返回邮箱的域名地址
     * 
     * @param string $email
     * @return string
     */
    public static function getMailHost($email) {
        $ps = strpos($email, '@') + 1;
        $mailHost = substr($email, $ps, strlen($email) - $ps);
        $ret = ('gmail.com' == $mailHost) ? 'google.com' : $mailHost;
        return 'mail.' . $ret;
    }

    /**
     * file_get_contents 方式获取接口数据，入口参数GET
     *
     * @param $aRs 调用接口时的参数
     * @return 返回JSON数据串或错误码
     */
    public static function getRemoteData($url, $timeOut = 5, $post = null) {
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 5
            )
        );

        if (is_array($post)) {
            ksort($post);
            $param = http_build_query($post, '', '&');
            $url = $url . '?' . $param;
        }

        $context = stream_context_create($opts);
        $ret = file_get_contents($url, false, $context);
        //匹配是否是正则串
        $isJsonStr = preg_match("/^(\[\{(.*?):(.*?)\}\])|(\{(.*?):(.*?)\})$/", $ret);
        $retRs = $isJsonStr ? json_decode($ret, TRUE) : FALSE;
        return $retRs;
    }

    /**
     * 根据当前时间生成一个唯一识别码
     * @return string 21位字符串 
     */
    public static function makeGuid() {
        list($s1, $s2) = explode(' ', microtime());
        $times = str_replace('.', '', $s2 . $s1);
        return $times . mt_rand(0, 9) . mt_rand(0, 9);
    }

    /**
     * 获取当前的微妙级时间戳数字串
     * @return string 19位字符串 
     */
    public static function getNumByTime($onlySecond = false) {
        list($s1, $s2) = explode(' ', microtime());
        $retStr = $onlySecond ? $s2 : $s2 . $s1;
        return str_replace('.', '', $retStr);
    }

    /**
     * 生成带IP地址的ticket 
     * 
     * @return string
     */
    public static function makeTicket() {
        return md5(Cola_Request::clientIp() . self::makeGuid());
    }

    /**
     * 生成唯一标识的数字，用来产生邀请码
     *
     * @return string
     */
    public static function guid() {
        return crc32(strtoupper(md5(uniqid(mt_rand(), true))));
    }

    /**
     * 对象转数组 
     * @param object $obj
     * @return array
     */
    public static function object2array($obj) {
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ((array) $arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? self::object2array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * 将数组转换成JSON串,并base64编码，便于在URL传递
     * 
     * @param array $msgRs
     * @return string
     */
    public static function encodeStr($msgRs) {
        return base64_encode(json_encode($msgRs));
    }

    /**
     * 对 encodeStr 的解码
     * 
     * @return array
     */
    public static function decodeStr($encodeStr) {
        return json_decode(base64_decode($encodeStr), TRUE);
    }

    /**
     *  去掉数组等于某个值的项
     * 
     * @param array $rs
     * @param string $value
     * @return array
     */
    public static function array_del_value($rs, $value) {
        $ret = $rs;
        foreach ($rs as $k => $v) {
            if ($v == $value) {
                unset($ret[$k]);
            }
        }
        return $ret;
    }

    /**
     * 检测数组1中的值是不是均不在数组二中。或者在，但是值都为空。
     * 
     * @param array $rs 可以是一维数组，判断值；如果为二维数组，判断key；
     * @param array $formRs
     * @return boolean
     */
    public static function isAllEmpty($rs, $formRs) {
        $ret = TRUE;
        $keyName = isset($rs[0]) ? 'v' : 'k';
        foreach ($rs as $k => $v) {
            $ret = $ret && (!isset($formRs[$$keyName]) OR ! $formRs[$$keyName]);
        }
        return $ret;
    }

    /**
     * 检查身份证号码
     * @param string $idCards 15位或18位身份证号码
     * @param string $len 是否可以是15位号码。
     * @return string | boolean 失败返回FALSE，成功返回一个18位的身份证号
     */
    public static function isIdCardsCorrect($idCards, $len = 'both') {
        if (strlen($idCards) == 15 && $len == 'both') {
            //当$len不等于'both'时，15位号码无效
            $truenum = substr($idCards, 0, 6) . '19' . substr($idCards, 6);
            //为返回18位号码作准备。
            $preg = "/^[\\d]{8}((0[1-9])|(1[0-2]))((0[1-9])|([12][\\d])|(3[01]))[\\d]{3}$/";
        } else if (strlen($idCards) == 18) {
            $truenum = substr($idCards, 0, 17);
            $preg = "/^[\\d]{6}((19[\\d]{2})|(200[0-8]))((0[1-9])|(1[0-2]))((0[1-9])|([12][\\d])|(3[01]))[\\d]{3}[0-9xX]$/";
        } else {
            return false;
        }
        if (!preg_match($preg, $idCards)) {
            return false; //完成正则表达式检测
        }

        /* -----------以下计算第18位验证码------------- */
        $nsum = substr($truenum, 0, 1) * 7;
        $nsum = $nsum + substr($truenum, 1, 1) * 9;
        $nsum = $nsum + substr($truenum, 2, 1) * 10;
        $nsum = $nsum + substr($truenum, 3, 1) * 5;
        $nsum = $nsum + substr($truenum, 4, 1) * 8;
        $nsum = $nsum + substr($truenum, 5, 1) * 4;
        $nsum = $nsum + substr($truenum, 6, 1) * 2;
        $nsum = $nsum + substr($truenum, 7, 1) * 1;
        $nsum = $nsum + substr($truenum, 8, 1) * 6;
        $nsum = $nsum + substr($truenum, 9, 1) * 3;
        $nsum = $nsum + substr($truenum, 10, 1) * 7;
        $nsum = $nsum + substr($truenum, 11, 1) * 9;
        $nsum = $nsum + substr($truenum, 12, 1) * 10;
        $nsum = $nsum + substr($truenum, 13, 1) * 5;
        $nsum = $nsum + substr($truenum, 14, 1) * 8;
        $nsum = $nsum + substr($truenum, 15, 1) * 4;
        $nsum = $nsum + substr($truenum, 16, 1) * 2;
        $yzm = 12 - $nsum % 11;

        if ($yzm == 10) {
            $yzm = 'x';
        } elseif ($yzm == 12) {
            $yzm = '1';
        } elseif ($yzm == 11) {
            $yzm = '0';
        }
        /* ----------18位验证码计算完成------------- */
        if (strlen($idCards) == 18) {
            if (strtolower(substr($idCards, 17, 1)) != $yzm) {
                return false;
            }
        }
        return $truenum . $yzm;
    }

    /**
     * 生成等长的数字，不够的前面加0
     * 
     * @param string $num 要生成等长的字符
     * @param int $length 总长度
     * @param boolean $addLat 填充到尾部
     * @param string $padChar 填充的字符
     * @return string
     */
    public static function makeIsometricNumb($num, $length, $addLat = FALSE, $padChar = 0) {
        $numLen = strlen((string) $num);
        if ($numLen >= $length) {
            return $num;
        }

        $padStr = str_repeat($padChar, $length - $numLen);
        return $addLat ? $num . $padStr : $padStr . $num;
    }

    /**
     * 将二维数组特定下标的值转换成一维数组
     * 
     * @param array $rs
     * @param string $key
     * @return array
     */
    public static function arrayTwoToOne($rs, $key) {
        $ret = array();
        foreach ($rs as $k => $v) {
            isset($v[$key]) && $ret[$k] = $v[$key];
        }
        //去重、重新排下标
        return array_merge(array_unique($ret));
    }

    /**
     * 将二维数组特定下标的值转换成一维数组K/V形式的一维数组
     * 
     * @param array $rs
     * @param array $keyRs
     * @return array
     */
    public static function arrayTwoToOne_new($rs, $keyRs, $key) {
        $ret = array();
        foreach ($rs as $v) {
            if (isset($v[$key])) {
                foreach ($keyRs as $subV) {
                    $ret[$v[$key]][$subV] = $v[$subV];
                }
            }
        }
        return $ret;
    }

    public static function getSubfieldTable($data, $colNum, $tableWidth) {
        $iRows = 3;
        $i = 0;
        $sTabStr = $sTdStr = null;
        $iProws = round(664 / $iRows);
        $iCnt = count($this->data['subject_list']);

        foreach ($this->data['subject_list'] as $k => $v) {

            $sTdStr = '<INPUT TYPE="checkbox" NAME="subject_id[]" id="subject_id_' . $k . '" value="' . (int) $k . '" />';
            $sTdStr = $sTdStr . '<label for="subject_id_' . $k . '">' . $v . '</label>';

            if (($i % $iRows) == 0) {
                $sTabStr = $sTabStr . '<tr ><td height="22" width="' . $iProws . '" bgcolor="#ffffff">' . $sTdStr . '</td>';
            } elseif (($i % $iRows) == $iRows - 1) {
                $sTabStr = $sTabStr . '<td width="' . $iProws . '" bgcolor="#ffffff">' . $sTdStr . '</td></tr>';
            } else {
                $sTabStr = $sTabStr . '<td width="' . $iProws . '" bgcolor="#ffffff">' . $sTdStr . '</td>';
            }

            //如果是最后一个单元格，则判断并显示剩下的单元格，让表格显示完整。
            if ($i == ($iCnt - 1)) {
                $max_num = $iRows - 1 - $i % $iRows;
                for ($n = 1; $n <= $max_num; $n++) {
                    $sTabStr = $sTabStr . '<td align=middle bgcolor="#ffffff">&nbsp;</td>';
                }
                $sTabStr = $sTabStr . "</tr>";
            }
            $i++;
        }
    }

    /**
     *  将二维数组转换成一维数组，相同的key的值，用逗号分隔返回
     * 
     * @param array $rs
     * @return array
     */
    public static function arrayTwoImpode($rs, $splitChar = ',') {
        $ret = array();
        foreach ($rs as $value) {
            foreach ($value as $subK => $subV) {
                $ret[$subK][] = $subV;
            }
        }

        foreach ($ret as &$v) {
            $v = implode($splitChar, $v);
        }
        return $ret;
    }

    /**
     * 检测是否全部为中文
     * 
     * @param string $str
     * @param int $mbLength 至少多少个汉字
     * @return boolean
     */
    public static function isAllChineseCharacterForUtf8($str, $mbLength = null) {
        if (preg_match('/^[\x{4e00}-\x{9FA5}()]+$/u', $str)) {
            return $mbLength ? mb_strlen($str, 'utf-8') >= $mbLength : true;
        }

        return FALSE;
    }

    /**
     * 获取字符串末尾的所有数字字符串
     * 
     * @param string $str
     * @return int
     */
    public static function getSuffixNumber($str) {
        preg_match_all('/D*([0-9]+)$/', $str, $ret);
        return isset($ret[1][0]) ? $ret[1][0] : 0;
    }

    /**
     * 拆分中英文混合字符串
     * 
     * @param string $str
     * @return array
     */
    public static function strSplit($str) {
        preg_match_all('/./u', $str, $ret);
        return $ret ? $ret[0] : array();
    }

    /**
     * 判断值是否存在于二维数组组的特定key的值中
     * 
     * @param string $str
     * @param array $rs
     * @param string $key 与二维数组中的特定key的值比较
     * @return boolean
     */
    public static function inTwoArrayByKey($str, $rs, $key) {
        foreach ($rs as $value) {
            if (isset($value[$key]) && ($str == $value[$key])) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 按当前时间取名文件名
     * 
     * @return string
     */
    public static function makeFileNameByTime() {
        list($s1, $s2) = explode(' ', microtime());
        $s1 = str_replace('.', '', $s1);
        $val = date('Y') . '_' . date('m') . '_' . date('d') . '_' . $s1;
        return $val;
    }

    /**
     *  检测一个数组是否为二维数组
     * 
     * @param array $rs
     * @return boolean
     */
    public static function isMultiArray($rs) {
        foreach ($rs as $value) {
            if (is_array($value)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     *  检测多维数组是不是都为空
     * 
     * @param array $data
     * @return boolean true为空
     */
    public static function isMultiArrayEmpty($data) {
        $ret = TRUE;

        if (!is_array($data)) {
            return TRUE;
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                return self::isMultiArrayEmpty($value);
            } else if ($value) {
                return FALSE;
            }
        }

        return $ret;
    }

    /**
     *  检测key是否在数组中存在，而且值不为空。
     * 
     * @param string $key
     * @param array $data
     * @param $oneDimensionalKey 一维数组下标key名称。
     * @return boolean key存在
     */
    public static function isHaveValue($key, $data, $oneDimensionalKey = NULL) {
        if (!is_array($data)) {
            return FALSE;
        }

        //$ret[0] 的特殊情况
        if (NULL !== $oneDimensionalKey) {
            return (isset($data[$oneDimensionalKey][$key]) && $data[$oneDimensionalKey][$key]) ? TRUE : FALSE;
        }

        return (isset($data[$key]) && $data[$key]) ? TRUE : FALSE;
    }

}
