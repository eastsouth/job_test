<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public const CODE_SUCCESS = 200;
    public const CODE_VALIDATION = 422;
    public const CODE_NOT_FOUND = 404;
    public const CODE_SERVER_ERROR = 500;

    /**
     * 成功信息返回
     */
    public static function successMeta(): array
    {
        return ['code' => self::CODE_SUCCESS, 'message' => 'success'];
    }

    /**
     * 错误信息返回
     */
    public static function error(int $code, string $message): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => null,
        ]);
    }
}
