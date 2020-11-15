<?php

declare(strict_types=1);

namespace App\Http\Response;

class JsonRpcResponse
{
    const JSON_RPC_VERSION = '2.0';

    public static function success($result, int $id = null)
    {
        return [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'result'  => $result,
            'id'      => $id,
        ];
    }

    public static function error($error)
    {
        return [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'error'  => $error,
            'id'      => null,
        ];
    }
}
