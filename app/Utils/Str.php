<?php
/******************************************************************************
 * Copyright (c) 2023 A. C.                                                   *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use App\Utils\Enums\AlphabetTypeEnums;

final class Str extends \Hyperf\Stringable\Str
{
    public static function sfId()
    {

    }

    public static function firstBySeparator(string $str, string $separator = ','): string
    {
        if (\str_contains(haystack: $str, needle: $separator)) {
            return explode(separator: $separator, string: $str)[0];
        }
        return $str;
    }

    public static function singleOrExplode(string $str, string $separator = ','): array
    {
        if (\str_contains(haystack: $str, needle: $separator)) {
            return explode(separator: $separator, string: $str);
        }
        return [$str];
    }

    public static function pure(mixed $v): string|array|int|bool
    {
        if (!is_scalar($v)) {
            return $v;
        }
        if (is_int($v) || is_bool($v) || is_resource($v)) {
            return $v;
        }
        // is_array is_object
        return str_ireplace([' ', "'", "\"", "\r", "\t", "\r\n"], '', $v);
    }

    public static function getFQCNModelNameByIndex(string $index): string
    {
        return 'App\Model\\' . ucfirst(self::camel($index));
    }

    public static function idToHex($i): string
    {
        return str_pad(dechex($i), 6, 'xx', STR_PAD_LEFT);
    }

    public static function hexToId(string $hex): int
    {
        $hex = str_ireplace(['x', 'X'], '', $hex);
        return \ctype_xdigit($hex) ? hexdec($hex) : 0;
    }

    public static function idToHash(int|string|array $id, string $salt = '', int $length = 0, bool $isHex = false): string
    {
        $hi = new \Hashids\Hashids($salt, $length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        return $isHex ? $hi->encodeHex($id) : $hi->encode($id);
    }

    public static function hashToId(string $hash, string $salt = '', int $length = 0, bool $isHex = false,): string|array
    {
        $hi = new \Hashids\Hashids($salt, $length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        return $isHex ? $hi->decodeHex($hash) : $hi->decode($hash);
    }

    public static function toPassword(string $pwd): string
    {
        return \password_hash($pwd, algo: PASSWORD_ARGON2ID);
    }

    public static function verifyPassword(string $hash, string $pwd): bool
    {
        return \password_verify($pwd, $hash);
    }

    public static function genSlug(string|int|array $seed, int $length = 10, AlphabetTypeEnums $type = AlphabetTypeEnums::MIXED): string
    {
        return \substr(string: sha1(\serialize($seed)), offset: 0, length: $length);
    }

    public static function toEntities(string $text): string
    {
        try {
            $json = \json_encode(strip_tags($text), JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \Error('json decode failed 5090');
        }
        $entities = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', static function ($match) { return '&#x' . $match[1] . ';'; }, $json);

        return trim($entities, '"');
    }

    public static function timeAvailable(?string $beginTime, ?string $endTime, int $after = null): bool
    {
        $beginTime = (string)$beginTime;
        $endTime   = (string)$endTime;
        $after     = $after ?? time();

        if ('' !== $beginTime && strtotime($beginTime) > $after) {
            return false;
        }
        if ('' !== $endTime && strtotime($endTime) <= $after) {
            return false;
        }

        return true;
    }

    public static function getNameWithoutExt(string $path): string
    {
        $name = basename(path: $path, suffix: '.' . pathinfo($path)['extension']);
        if (str_contains(haystack: $name, needle: '.')) {
            return self::getNameWithoutExt($name);
        }
        return $name;
    }

    public static function replaceImageExtToJS(string $imgUrl): string
    {
        $arr = pathinfo($imgUrl);

        return $arr['dirname'] . DIRECTORY_SEPARATOR . $arr['filename'] . '.js';
    }
}
