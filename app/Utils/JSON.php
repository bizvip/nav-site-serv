<?php

/******************************************************************************
 * Copyright (c) 2023 A. C.                                                   *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use Hyperf\Codec\Exception\InvalidArgumentException;
use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;

final class JSON
{
    public static function isValid(string $json, int $depth = 512): bool
    {
        //simdjson_is_valid(string $json = false, int $depth = 512): bool
        return \simdjson_is_valid($json, $depth);
    }

    public static function encode($data, int $flags = null, int $depth = 512,): string
    {
        if ($data instanceof Jsonable) {
            return (string)$data;
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        try {
            $json = null === $flags
                ? \json_encode($data, $flags | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR, $depth)
                : \json_encode($data, JSON_THROW_ON_ERROR | $flags, $depth);
        } catch (\Throwable $exception) {
            throw new InvalidArgumentException($exception->getMessage(), (int)$exception->getCode(), $exception);
        }

        return $json;
    }

    public static function decode(string $json, bool $assoc = true, int $depth = 512, int $flags = 0): mixed
    {
        //simdjson_decode(string $json, bool $associative = false, int $depth = 512): mixed
        return \simdjson_decode($json, $assoc, $depth);
    }

    public static function jsonKeyExists(string $json, string $key, int $depth = 512): bool
    {
        //simdjson_key_exists(string $json, string $key, int $depth = ?): bool
        return \simdjson_key_exists($json, $key);
    }

    public static function jsonKeyValue(string $json, string $key, bool $assoc = false, int $depth = 512): mixed
    {
        //simdjson_key_value(string $json,string $key,bool $associative = false,int $depth = 512): mixed
        return \simdjson_key_value($json, $key, $assoc, $depth);
    }

    public static function jsonKeyCount(string $json, string $key, int $depth = 512, bool $throwIfUncountable = false): int
    {
        //simdjson_key_count(string $json,string $key,int $depth = 512,bool $throw_if_uncountable = false): int
        return \simdjson_key_count($json, $key, $depth, $throwIfUncountable);
    }
}
