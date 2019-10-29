<?php defined('ABSPATH') or die("Protected By WT!");

final class WTSEC_LIBRARY_Idn
{
    private static $decodeTable = array(
        'a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4, 'f' => 5,
        'g' => 6, 'h' => 7, 'i' => 8, 'j' => 9, 'k' => 10, 'l' => 11,
        'm' => 12, 'n' => 13, 'o' => 14, 'p' => 15, 'q' => 16, 'r' => 17,
        's' => 18, 't' => 19, 'u' => 20, 'v' => 21, 'w' => 22, 'x' => 23,
        'y' => 24, 'z' => 25, '0' => 26, '1' => 27, '2' => 28, '3' => 29,
        '4' => 30, '5' => 31, '6' => 32, '7' => 33, '8' => 34, '9' => 35,
    );
    public static function idn_to_utf8($domain)
    {
        $domain = mb_strtolower($domain);
        $parts = explode('.', $domain);
        foreach ($parts as &$part) {
            $length = \strlen($part);
            if ($length < 1 || 63 < $length) {
                continue;
            }
            if (0 !== strpos($part, 'xn--')) {
                continue;
            }
            $part = substr($part, 4);
            $part = self::decodePart($part);
        }
        $output = implode('.', $parts);
        return \strlen($output) > 255 ? false : mb_strtolower($output);
    }
    private static function calculateThreshold($k, $bias)
    {
        if ($k <= $bias + 1) {
            return 1;
        }
        if ($k >= $bias + 26) {
            return 26;
        }
        return $k - $bias;
    }
    private static function adapt($delta, $numPoints, $firstTime)
    {
        $delta = (int) ($firstTime ? $delta / 700 : $delta / 2);
        $delta += (int) ($delta / $numPoints);
        $k = 0;
        while ($delta > 35 * 13) {
            $delta = (int) ($delta / 35);
            $k = $k + 36;
        }
        return $k + (int) (36 * $delta / ($delta + 38));
    }
    private static function decodePart($input)
    {
        $n = 128;
        $i = 0;
        $bias = 72;
        $output = '';
        $pos = strrpos($input, '-');
        if (false !== $pos) {
            $output = substr($input, 0, $pos++);
        } else {
            $pos = 0;
        }
        $outputLength = \strlen($output);
        $inputLength = \strlen($input);
        while ($pos < $inputLength) {
            $oldi = $i;
            $w = 1;
            for ($k = 36;; $k += 36) {
                $digit = self::$decodeTable[$input[$pos++]];
                $i += $digit * $w;
                $t = self::calculateThreshold($k, $bias);
                if ($digit < $t) {
                    break;
                }
                $w *= 36 - $t;
            }
            $bias = self::adapt($i - $oldi, ++$outputLength, 0 === $oldi);
            $n = $n + (int) ($i / $outputLength);
            $i = $i % $outputLength;
            $output = mb_substr($output, 0, $i, 'utf-8').mb_chr($n, 'utf-8').mb_substr($output, $i, $outputLength - 1, 'utf-8');
            ++$i;
        }
        return $output;
    }
}
if (!function_exists('mb_chr')) {
    function mb_chr($ord, $encoding = 'UTF-8') {
        if ($encoding === 'UCS-4BE') {
            return pack("N", $ord);
        } else {
            return mb_convert_encoding(mb_chr($ord, 'UCS-4BE'), $encoding, 'UCS-4BE');
        }
    }
}
if (!function_exists('mb_convert_encoding')) {
    function mb_convert_encoding($str, $to_encoding, $from_encoding = NULL) {
        return iconv(($from_encoding === NULL) ? mb_internal_encoding() : $from_encoding, $to_encoding, $str);
    }
}