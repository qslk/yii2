<?php
namespace common\components\helper;

use Yii;
use yii\log\FileTarget;
use yii\log\Logger;

/**
 * 基础助手类
 *
 * File: BaseHelper.php
 * Created by PhpStorm.
 * User: zhulizhen 852417273@qq.com
 * Date: 2016/07/27
 * Time: 14:48
 */
class BaseHelper
{
    public static $currencyData;

    /**
     * 获取绝对路径
     *
     * @param $alias
     * @return mixed
     */
    public static function getAlias($alias)
    {
        return str_replace('@', dirname(Yii::$app->basePath) . '/', $alias);
    }

    /**
     * 格式化页面的Url
     * 以 / 开头, 以 .html 结尾
     * @param $pageUrl
     * @return string
     * @User DuYue 61196135@qq.com
     */
    public static function formatPageUrl($pageUrl)
    {
        $pageUrl = '/' . trim($pageUrl, '/');

        $temp = explode('.', $pageUrl);
        $pageUrl = $temp[0] . '.html';

        return $pageUrl;
    }

    /**
     * 获取cookie中语言标识
     *
     * @param bool|FALSE $isFormat
     * @return mixed|string
     */
    public static function getLangCode($isFormat = FALSE)
    {
        $langCode = strtolower(Yii::$app->language);

        if ($isFormat)
        {
            return str_replace('-', '_', $langCode);
        }

        return $langCode;
    }

    /**
     * 截取字符串
     * @param $string
     * @param $length
     * @param bool $append
     * @param string $endCode
     * @return array|string
     */
    public static function subString($string, $length, $append = TRUE, $endCode = '')
    {
        $string = htmlspecialchars_decode($string);

        if (strlen($string) <= $length)
        {
            return $string;
        }

        $i = 0;
        $stringLast = [];

        while ($i < $length)
        {
            $stringTMP = substr($string, $i, 1);

            if (ord($stringTMP) >= 224)
            {
                $stringTMP = substr($string, $i, 3);
                $i = $i + 3;
            }
            elseif (ord($stringTMP) >= 192)
            {
                $stringTMP = substr($string, $i, 2);
                $i = $i + 2;
            }
            else
            {
                $i = $i + 1;
            }

            $stringLast[] = $stringTMP;
        }

        $stringLast = implode("", $stringLast);

        if ($append)
        {
            $stringLast .= "...";
        }

        if (!empty($endCode))
        {
            $stringLast .= $endCode;
        }

        return ($stringLast);
    }

    /**
     * 隐藏字符串的一部分 用*代替
     * @param $str
     * @param int $leftLength 左边保留长度
     * @param int $rightLength 右边保留长度
     * @param string $replaceChar 替换成的字符串
     * @return string
     */
    public static function replaceStrToStar($str, $leftLength = 3, $rightLength = 3, $replaceChar = '*')
    {
        $strLength = strlen($str);

        // 验证 是否为  Email  显示@以及后面的部分 左侧部分 默认显示 3位数 123***@qq.com
        if (filter_var($str, FILTER_VALIDATE_EMAIL))
        {
            $pattern = '/^([a-zA-Z0-9_\.\-]{1,})+(@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4})/i';
            $emailPreSuffix = preg_replace($pattern, '${1}', $str);  //@ 前面部分
            $emailPreSuffixLength = strlen($emailPreSuffix);
            $emailSuffix = preg_replace($pattern, '${2}', $str);   // @ 后面部分
            if ($emailPreSuffixLength <= $leftLength)
            {
                return str_repeat($replaceChar, $emailPreSuffixLength) . $emailSuffix;
            }
            else
            {
                //截取 前3个字符
                $leftStr = substr($str, 0, $leftLength);
                $middleStrLength = $emailPreSuffixLength - $leftLength;

                return $leftStr . str_repeat($replaceChar, $middleStrLength) . $emailSuffix;
            }
        }
        else
        {
            if ($leftLength >= $strLength)
            {
                return str_repeat($replaceChar, $strLength);
            }
            else if ($rightLength >= $strLength)
            {
                return str_repeat($replaceChar, $strLength);
            }
            if (($leftLength + $rightLength) >= $strLength)
            {
                return str_repeat($replaceChar, $strLength);
            }
            else
            {
                $leftStr = substr($str, 0, $leftLength);
                $rightStr = substr($str, ($strLength - $rightLength), $rightLength);
                $middleStr = substr($str, ($leftLength), ($strLength - ($rightLength + $leftLength)));

                return $leftStr . str_repeat($replaceChar, strlen($middleStr)) . $rightStr;
            }
        }
    }

    /**
     * 获得客户端IP地址
     * @return string|NULL
     */
    public static function getClientIp()
    {
        $arr_ip_header = [
            "HTTP_CDN_SRC_IP",
            "HTTP_PROXY_CLIENT_IP",
            "HTTP_WL_PROXY_CLIENT_IP",
            "HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "REMOTE_ADDR",
        ];

        $clientIp = "";
        foreach ($arr_ip_header as $key)
        {
            if (!empty($_SERVER[$key]) && strtolower($_SERVER[$key]) != "unknown")
            {
                $clientIp = $_SERVER[$key];
                break;
            }
        }
        if (FALSE !== strpos($clientIp, ","))
        {
            $clientIp = preg_replace("/,.*/", "", $clientIp);
        }

        return $clientIp;
    }

    /**
     * 合并多个数组
     * 遇到相同的键 后面的数组值覆盖前面的数组值
     *
     * @param $a
     * @param $b
     * @return mixed
     */
    public static function merge($a, $b)
    {
        $args = func_get_args();

        $res = array_shift($args);
        while (!empty($args))
        {
            $next = array_shift($args);
            if (!empty($next))
            {
                foreach ($next as $k => $v)
                {
                    if (is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    {
                        $res[$k] = self::merge($res[$k], $v);
                    }
                    else
                    {
                        $res[$k] = $v;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * 加密密码
     * @param $password
     * @return string
     */
    public static function encryptionPassword($password)
    {
        return md5($password);
    }

    /**
     * 加密函数
     * @param string $txt 需要加密的字符串
     * @param string $key 密钥
     * @return string 返回加密结果
     */
    public static function encrypt($txt, $key = '')
    {
        if (empty($txt))
        {
            return $txt;
        }

        if (empty($key))
        {
            $key = md5(Yii::$app->params['md5Key']);
        }

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $nh1 = rand(0, 64);
        $nh2 = rand(0, 64);
        $nh3 = rand(0, 64);
        $ch1 = $chars{$nh1};
        $ch2 = $chars{$nh2};
        $ch3 = $chars{$nh3};
        $nhnum = $nh1 + $nh2 + $nh3;
        $knum = 0;
        $i = 0;

        while (isset($key{$i}))
        {
            $knum += ord($key{$i++});
        }

        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $txt = base64_encode(time() . '_' . $txt);
        $txt = str_replace(array ('+', '/', '='), array ('-', '_', '.'), $txt);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = strlen($txt);
        $klen = strlen($mdKey);

        for ($i = 0; $i < $tlen; $i++)
        {
            $k = $k == $klen ? 0 : $k;
            $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
            $tmp .= $chars{$j};
        }

        $tmplen = strlen($tmp);
        $tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);

        return $tmp;
    }

    /**
     * 解密函数
     * @param string $txt 需要解密的字符串
     * @param string $key 密匙
     * @return string 字符串类型的返回结果
     */
    public static function decrypt($txt, $key = '', $ttl = 0)
    {
        if (empty($txt))
        {
            return $txt;
        }

        if (empty($key))
        {
            $key = md5(Yii::$app->params['md5Key']);
        }

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $knum = 0;
        $i = 0;
        $tlen = @strlen($txt);

        while (isset($key{$i}))
        {
            $knum += ord($key{$i++});
        }

        $ch1 = @$txt{$knum % $tlen};
        $nh1 = strpos($chars, $ch1);
        $txt = @substr_replace($txt, '', $knum % $tlen--, 1);
        $ch2 = @$txt{$nh1 % $tlen};
        $nh2 = @strpos($chars, $ch2);
        $txt = @substr_replace($txt, '', $nh1 % $tlen--, 1);
        $ch3 = @$txt{$nh2 % $tlen};
        $nh3 = @strpos($chars, $ch3);
        $txt = @substr_replace($txt, '', $nh2 % $tlen--, 1);
        $nhnum = $nh1 + $nh2 + $nh3;
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = @strlen($txt);
        $klen = @strlen($mdKey);

        for ($i = 0; $i < $tlen; $i++)
        {
            $k = $k == $klen ? 0 : $k;
            $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});

            while ($j < 0)
            {
                $j += 64;
            }

            $tmp .= $chars{$j};
        }

        $tmp = str_replace(array ('-', '_', '.'), array ('+', '/', '='), $tmp);
        $tmp = trim(base64_decode($tmp));

        if (preg_match('/\d{10}_/s', substr($tmp, 0, 11)))
        {
            if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl))
            {
                $tmp = NULL;
            }
            else
            {
                $tmp = substr($tmp, 11);
            }
        }

        return $tmp;
    }

    /**
     * 生成手机验证码
     * @param string $length
     * @return string
     * @author: Fei <xliang.fei@gmail.com>
     */

    public static function make_rand_code($length = "6")
    {
        $result = "";
        for ($i = 0; $i < $length; $i++)
        {
            $result .= rand(0, 9);
        }

        return $result;
    }

    /**
     * curl获取页面数据
     * @param string $url
     * @return string
     */
    public static function getCurlContent($url = '')
    {
        if (empty($url))
        {
            return '';
        }

        //初始化
        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        //返回获得的数据
        return $output;
    }

    /**
     * 请求字符串拼接
     * @param $queryData
     * @param bool $encoding
     * @return string
     */
    public static function buildQuery($queryData, $encoding = TRUE)
    {
        $res = '?';
        $count = count($queryData);
        $i = 0;

        foreach ($queryData as $k => $v)
        {
            if ($encoding === TRUE)
            {
                $v = urlencode($v);
            }
            if ($i < $count - 1)
            {
                $res .= $k . '=' . $v . '&';
            }
            else
            {
                $res .= $k . '=' . $v;
            }

            $i++;
        }

        return $res;
    }

    /**
     * @description 传入协调世界时的时间戳，和时区。返回相应的时间戳
     * @param int $worldTime 协调世界时
     * @param string $timezone 时区标识。默认东八区
     * @return int 时区的时间戳
     */
    public static function getSiteZoneTime($worldTime, $timezone = 'GMT+8_77')
    {
        $zoneInfo = explode("_", $timezone);
        //$zone1 = substr($zoneInfo['0'],0,3);
        $zone2 = substr($zoneInfo['0'], 3);// 与协调世界时，相差时间（正负时间）
        $timeInfo = explode(':', $zone2);
        $addTime = $timeInfo['0'] * 3600;
        if (isset($timeInfo['1']))
        {
            if ($timeInfo['0'])
            {
                $addTime += $timeInfo['1'] * 60;
            }
            else
            {
                $addTime -= $timeInfo['1'] * 60;
            }
        }

        return $worldTime + $addTime;
    }

    /**
     * 拷贝文件夹
     * @param $src
     * @param $dst
     */
    public static function copyDir($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);

        while (FALSE !== ($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($src . '/' . $file))
                {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                    continue;
                }
                else
                {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }

    /**
     * @description 删除文件夹和文件下的内容
     * @param string $dirName 删除的文件夹路径
     * @param boolean $isDeleteDir 是否删除当前目录
     */
    public static function deleteDirFile($dirName, $isDeleteDir = TRUE)
    {
        if ($handle = opendir($dirName))
        {
            while (FALSE !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                    $dirName = rtrim($dirName, '/');

                    if (is_dir("{$dirName}/{$file}"))
                    {
                        self::deleteDirFile("$dirName/{$file}");
                    }
                    else
                    {

                        unlink("{$dirName}/{$file}");
                    }
                }
            }

            if ($isDeleteDir)
            {
                rmdir($dirName);
            }

            closedir($handle);
        }
    }

    /**
     * 判断是否是移动设备
     * @return bool
     */
    public static function isMobile()
    {
        //return FALSE;

        // 如果定义了移动端标识
        if (defined('YII_WAP') && YII_WAP == 'wap')
        {
            return TRUE;
        }

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return TRUE;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为FALSE,否则为TRUE
            return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientKeywords = array ('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');

            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientKeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return TRUE;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 优化显示日期时间
     *
     * @param $theTime
     * @return bool|string
     * @User DuYue 61196135@qq.com
     */
    public static function timeTran($theTime)
    {
        $nowTime = time();
        $showTime = is_numeric($theTime) ? $theTime : strtotime($theTime);
        $dur = $nowTime - $showTime;
        if ($dur < 0)
        {
            return $theTime;
        }
        else
        {
            if ($dur < 60)
            {
                $unit = $dur > 1 ? LangHelper::t('seconds_ago', 'common') : LangHelper::t('second_ago', 'common');

                return $dur . ' ' . $unit;
            }
            else
            {
                if ($dur < 3600)
                {
                    $dur = floor($dur / 60);
                    $unit = $dur > 1 ? LangHelper::t('minutes_ago', 'common') : LangHelper::t('minute_ago', 'common');

                    return $dur . ' ' . $unit;
                }
                else
                {
                    if ($dur < 86400)
                    {
                        $dur = floor($dur / 3600);
                        $unit = $dur > 1 ? LangHelper::t('hours_ago', 'common') : LangHelper::t('hour_ago', 'common');

                        return $dur . ' ' . $unit;
                    }
                    else
                    {
                        if ($dur < 259200)
                        {//3天内
                            $dur = floor($dur / 86400);
                            $unit = $dur > 1 ? LangHelper::t('days_ago', 'common') : LangHelper::t('day_ago', 'common');

                            return $dur . ' ' . $unit;
                        }
                        else
                        {
                            return date('Y-m-d H:i:s', $theTime);
                        }
                    }
                }
            }
        }
    }

    /**
     * 格式化折扣 Discount
     * 返回小数：例如：0.85(85折)
     *
     * @param number $discount
     * @param bool $isDivision 是否除以1000
     * @return float|int
     * @User DuYue 61196135@qq.com
     */
    public static function formatDiscount($discount, $isDivision = TRUE)
    {
        $discount = empty($discount) ? 1000 : $discount;

        return $isDivision ? $discount / 1000 : $discount;
    }

    /**
     * 格式化金额
     *
     * @param int $price |人民币 分
     * @param string $currency 币种 USD|CNY...   不传则根据当前语言自动获取币种
     * @param string $rate |汇率   不传则根据当前币种汇率
     * @param bool $isFormat 是否格式化
     * @param bool $isDivision 是否除以100
     * @return string
     */
    public static function formatCur($price, $currency = 'CNY', $rate = '', $isFormat = TRUE, $isDivision = TRUE)
    {
        $currency = empty($currency) ? 'CNY' : $currency;
        self::$currencyData = empty(self::$currencyData) ? RedisHelper::getCurrencyCache($currency) : self::$currencyData;

        if (empty($rate))
        {
            $rate = self::$currencyData['rate'];
        }

        $price = $price * $rate;

        // 是否除以100
        if ($isDivision)
        {
            $price = $price / 100;
            $price = sprintf('%.2f', $price);
        }

        if ($isFormat)
        {
            $price = sprintf('%.2f', $price);
            $price = str_replace('.', self::$currencyData['decimal_point'], $price);
            $price = number_format($price, 2, self::$currencyData['decimal_point'], self::$currencyData['thousands_point']);

            // 左边币种符号
            $symbolLeft = empty(self::$currencyData['symbol_left']) ? '' : '<span class="cur-l">' . self::$currencyData['symbol_left'] . '</span>';
            // 右边币种符号
            $symbolRight = empty(self::$currencyData['symbol_right']) ? '' : '<span class="cur-r">' . self::$currencyData['symbol_right'] . '</span>';

            $price = '<span class="cur">' . $price . '</span>';

            return $symbolLeft . $price . $symbolRight;
        }

        return $price;
    }

    /**
     * 写入调试日志（用于测试）
     * @param $logFileName
     * @param $logData
     */
    public static function writeLogData($logFileName, $logData)
    {
        if (empty($logFileName))
        {
            return;
        }

        $logFile = Yii::$app->getRuntimePath() . '/logs/' . $logFileName . '.log';

        $log = new FileTarget();
        $log->logFile = $logFile;
        $message = PHP_EOL . $logFileName . '||||||: ' . $logData;

        $log->messages[] = [$message, Logger::LEVEL_INFO, 'application', YII_BEGIN_TIME];
        $log->export();
    }

    /**
     * 文件大小格式化
     *
     * @param int $byte 文件大小，单位为byte
     * @param int $dec
     * @return string
     * @User DuYue 61196135@qq.com
     */
    public static function fileSizeFormat($byte = 0, $dec = 1)
    {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $pos = 0;
        while ($byte >= 1024)
        {
            $byte /= 1024;
            $pos++;
        }
        $result['size'] = round($byte, $dec);
        $result['unit'] = $unit[$pos];

        return $result['size'] . $result['unit'];
    }

    /**
     * 转换成 树形数组
     * @param $typeArray
     * @param int $pid
     * @return array
     */
    public static function getTree($typeArray, $pid = 0)
    {
        $treeArray = array ();
        if (is_array($typeArray))
        {
            $array = array ();
            foreach ($typeArray as $key => $item)
            {
                $array[$item['id']] = &$typeArray[$key];
            }
            foreach ($typeArray as $key => $item)
            {
                $parentID = $item['parentID'];
                if ($pid == $parentID)
                {
                    $treeArray[] = &$typeArray[$key];
                }
                else
                {
                    if (isset($array[$parentID]))
                    {
                        $parent = &$array[$parentID];
                        $parent['child'][] = &$typeArray[$key];
                    }
                }
            }
        }

        return $treeArray;
    }

}