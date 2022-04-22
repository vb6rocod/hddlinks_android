<?php
/**
 * JavaScriptUnpacker.php
 * @author Andrey Izman <izmanw@gmail.com>
 * @link https://github.com/mervick/JavaScriptUnpacker
 * @license MIT
 */

/**
 * Class JavaScriptUnpacker
 */
class JavaScriptUnpacker1
{
    /**
     * @var string
     */
    protected static $JS_FUNC = 'eval(function(p,a,c,k,e,';

    /**
     * @var string
     */
    protected $script;

    /**
     * @param $script
     * @return string
     */
    public static function unpack($script)
    {
        return (new self($script))->deobfuscate();
    }

    /**
     * @param $script
     */
    protected function __construct($script)
    {
        $this->script = $script;
    }

    /**
     * @return string
     */
    protected function deobfuscate()
    {
        if (self::hasPackedCode($this->script, $start) &&
            (($body = $this->findBlock('{', '}', $start + strlen(self::$JS_FUNC), $pos))) &&
            (($params = $this->findBlock('(', ')', $pos + strlen($body), $pos))) &&
            (($end = strpos($this->script, ')', $pos + strlen($params)))) &&
            (($packed = self::findString($params, 1, $pos, $quote))) &&
            (($keywords = self::findString($params, $offset = $pos + strlen($packed) + 2, $pos))) &&
            (preg_match('/^,([0-9]+),([0-9]+),$/', preg_replace('/[\x03-\x20]+/', '',
                substr($params, $offset, $pos - $offset)), $matches)))
        {
            list(, $ascii, $count) = $matches;
            $packed = str_replace('\\' . $quote, $quote, $packed);
            $decode = 'decode' . self::detectEncoding($body);
            $script = $this->$decode($packed, $ascii, $count, explode('|', $keywords));
            $script = str_replace('\\\\', '\\', $script);
            if (self::isDoubleEscaped($script)) {
                $script = str_replace(['\\\'', '\\"', '\\\\\'', '\\\\"', '\\\\'],
                    ['\'', '"', '\\\\\'', '\\\\"', '\\'],  $script);
            }
            $script = self::replaceSpecials($script);
            return substr($this->script, 0, $start) . self::unpack($script) . self::unpack(substr($this->script, $end + 1));
        }
        return $this->script;
    }

    /**
     * @param string $str
     * @return string
     */
    protected static function replaceSpecials($str)
    {
        $replace = function($str) {
            return str_replace(['\n', '\r', '\t'], ["\n", "\r", "\t"], $str);
        };
        $pieces = [];
        for ($offset = 0; ($string = self::findString($str, $offset, $pos, $quote)) !== false;) {
            $pieces[] = $replace(substr($str, $offset, $pos - $offset));
            $pieces[] = $quote . $string . $quote;
            $offset = $pos + strlen($string) + 2;
        }
        $pieces[] = $replace(substr($str, $offset));
        return implode('', $pieces);
    }

    /**
     * @param string $str
     * @return bool
     */
    protected static function isDoubleEscaped($str)
    {
        $result = true;
        foreach (["'", '"'] as $quote) {
            $matches = [];
            foreach (['', '\\'] as $i => $slash) {
                for ($matches[$i] = $j = 0, $find = "{$slash}{$quote}", $len = strlen($find);
                    ($pos = strpos($str, $find, $j)) !== false; $j = $pos + $len, $matches[$i] ++);
            }
            list($x, $y) = $matches;
            if ($x !== $y) {
                return false;
            }
            $result = $result && $x;
        }
        return $result;
    }

    /**
     * @param string $packed
     * @param int $ascii
     * @param int $count
     * @param string $keywords
     * @return string
     */
    protected function decode62($packed, $ascii, $count, $keywords)
    {
        $packed = " $packed ";
        $base = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $encode = function ($count) use (&$encode, $ascii, $base) {
            return ($count < $ascii ? '' : $encode(intval($count / $ascii))) . $base{$count % $ascii};
        };
        $split = '([^a-zA-Z0-9_])';
        while ($count--) {
            if (!empty($keywords[$count])) {
                $pattern = '/' . $split . preg_quote($encode($count)) . $split . '/';
                $packed = preg_replace_callback($pattern, function($matches) use ($keywords, $count) {
                    return $matches[1] . $keywords[$count] . $matches[2];
                }, $packed);
            }
        }
        return substr($packed, 1, -1);
    }

    /**
     * @param string $packed
     * @param int $ascii
     * @param int $count
     * @param string $keywords
     * @return string
     */
    protected function decode95($packed, $ascii, $count, $keywords)
    {
        $encode = function ($count) use (&$encode, $ascii) {
            return ($count < $ascii ? '' : $encode(intval($count / $ascii))) .
                mb_convert_encoding(pack('N', $count % $ascii + 161), 'UTF-8', 'UCS-4BE');
        };
        $decoded = [];
        while ($count--) {
            $encoded = $encode($count);
            $decoded[$encoded] = !empty($keywords[$count]) ? $keywords[$count] : $encoded;
        }
        foreach($decoded as $key=>$value) {    // fix by me
         $packed =str_replace($key,$value,$packed);
        };
        $code =  preg_replace_callback('/([\xa1-\xff]+)/', function($match) use ($decoded) {
            return isset($decoded[$match[1]]) ? $decoded[$match[1]] : $match[1];
        }, $packed);
        $code=str_replace("\\\\","\\",$code);   // fix by me
        return preg_replace_callback(      // fix by me
        "@\\\(x)?([0-9a-fA-F]{2})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'ISO-8859-1', 'UTF-8');
        },
        $code
    );

    }

    /**
     * @param string $buf
     * @param int $index
     * @param int $len
     * @return bool
     */
    protected static function isSlashed($buf, $index, $len)
    {
        if ($buf{$index} === '\\') {
            if ($len > 1 && $buf{$index - 1} === '\\') {
                return self::isSlashed($buf, $index - 2, $len - 2);
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $buf
     * @param int $offset
     * @param null|int $start
     * @param null|string $quote
     * @return bool|string
     */
    protected static function findString($buf, $offset, &$start=null, &$quote=null)
    {
        for ($start = $offset, $len = strlen($buf); $start < $len; $start++) {
            foreach (['"', "'"] as $quote) {
                if ($buf{$start} === $quote) {
                    for ($i = $start + 1; $i < $len; $i++) {
                        if ($buf{$i} === $quote && !self::isSlashed($buf, $i - 1, $i - $start - 1)) {
                            break;
                        }
                    }
                    if ($i === $len) {
                        return false;
                    }
                    return substr($buf, $start + 1, $i - $start - 1);
                }
            }
        }
        return false;
    }

    /**
     * @param string $open
     * @param string $close
     * @param int $offset
     * @param int $start
     * @return string|false
     */
    protected function findBlock($open, $close, $offset, &$start)
    {
        $buf = substr($this->script, $offset);
        $len = strlen($buf);
        for ($start = 0; $start < $len && $buf{$start} !== $open; $start++);
        for ($i = $start + 1, $skip = 0; $i < $len; $i++) {
            if ($buf{$i} === $close && 0 === $skip--) {
                break;
            }
            foreach (['"', "'"] as $quote) {
                if ($buf{$i} === $quote) {
                    for ($i++; $i < $len && ($buf{$i} !== $quote || $buf{$i - 1} === '\\'); $i++);
                }
            }
            if ($buf{$i} === $open) {
                $skip++;
            }
        }
        if ($start === $len || $i === $len) {
            return false;
        }
        $block = substr($buf, $start, $i - $start + 1);
        $start += $offset;

        return $block;
    }

    /**
     * @param string $body
     * @return int
     */
    protected static function detectEncoding($body)
    {
        return strpos($body, '161') ? 95 : 62;
    }

    /**
     * @param string $str
     * @param null|int $start
     * @return bool
     */
    public static function hasPackedCode($str, &$start=null)
    {
        if (($pos = strpos(strtolower(preg_replace('/[\x03-\x20]+/', '', $str)), self::$JS_FUNC)) !== false) {
            $start = -1;
            do {
                while (preg_match('/[\x03-\x20]/', $str{++$start}));
            } while (0 < $pos--);
            return true;
        }
        return false;
    }
}
