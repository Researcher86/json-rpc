<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\History;
use App\Services\JsonRpcException;
use Illuminate\Routing\Controller as BaseController;

class WeatherController extends BaseController
{
    public function getByDate($data)
    {
        if (!isset($data['date']) || !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data['date'])) {
            throw new JsonRpcException('Invalid params', JsonRpcException::INVALID_PARAMS);
        }

        $result = History::where('date_at', $data['date'])->get()->toArray();
        return $result[0] ?? [];
    }

    public function getHistory($data)
    {
        if ($data['lastDays'] < 0) {
            throw new JsonRpcException('Invalid params', JsonRpcException::INVALID_PARAMS);
        }

        $result = History::orderBy('date_at', 'desc')->take($data['lastDays'])->get()->toArray();
        return $result ?? [];
    }
}
