<?php
/******************************************************************************
 * Copyright (c) M3-1-1 AChang 2023.                                          *
 ******************************************************************************/

declare(strict_types=1);

namespace App\Utils;

use App\Constants\Biz;
use Hyperf\Contract\LengthAwarePaginatorInterface;

final class Pagination
{
    public static function format(LengthAwarePaginatorInterface $paginator = null, bool $withOutList = false,): array
    {
        if (!$paginator) {
            goto re;
        }

        $list = true === $withOutList ? [] : array_map(static fn($v) => method_exists($v, 'toArray') ? $v->toArray() : (array)$v, $paginator?->items());

        re:

        return [
            'total'    => $paginator?->total(),
            'pageNum'  => $paginator?->currentPage(),
            'pageSize' => $paginator?->perPage(),
            'list'     => $list ?? [],
        ];
    }

    public static function setPageInfoFromRequest(array $pages): array
    {
        if (isset($pages['num'], $pages['size'])) {
            ['num' => $num, 'size' => $size] = $pages;
        }

        $num  = empty($num) ? Biz::DEFAULT_PAGE_NUM : (int)$num;
        $size = empty($size) ? Biz::DEFAULT_PAGE_SIZE : (int)$size;

        return compact('num', 'size');
    }

    public static function getMaxPageNum(int $total, int $perPage): int
    {
        $num = intdiv($total, $perPage);
        if ($total % $perPage > 0) {
            ++$num;
        }
        return $num;
    }
}
