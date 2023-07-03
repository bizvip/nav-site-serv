<?php
/******************************************************************************
 * Copyright (c) 2023 A. C.                                                   *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

/**
 * 空間換時間
 */
final class Lang
{
    private static $ccs2t = null;

    private static $ccs2tw = null;

    public function __construct()
    {
        if (!self::$ccs2t) {
            self::$ccs2t = \opencc_open('s2tw.json');
        }
        if (null === self::$ccs2tw) {
            self::$ccs2tw = \opencc_open('s2twp.json');
        }
    }

    /**
     * // s2t.json 简体到繁体
     * // t2s.json 繁体到简体
     * // s2tw.json 简体到台湾正体
     * // tw2s.json 台湾正体到简体
     * // s2hk.json 简体到香港繁体（香港小学学习字词表标准）
     * // hk2s.json 香港繁体（香港小学学习字词表标准）到简体
     * // s2twp.json 简体到繁体（台湾正体标准）并转换为台湾常用词汇
     * // tw2sp.json 繁体（台湾正体标准）到简体并转换为中国大陆常用词汇
     */
    public function s2t(string $str): string
    {
        return \opencc_convert($str, self::$ccs2t);
    }

    public function s2tw(string $str): string
    {
        return \opencc_convert($str, self::$ccs2tw);
    }
}