<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\JsonRpcClient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{
    private JsonRpcClient $client;

    public function __construct(JsonRpcClient $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $params = $this->getHistory();
        return view('welcome', ['data' => $params]);
    }

    public function show(Request $request)
    {
        $params = $this->getHistory();
        $data = $this->client->send('weather.getByDate', ['date' => $request->post('date')], isset($_COOKIE['XDEBUG_SESSION']));
        $params = array_merge($params, $this->buildParams($data));

        return view('welcome', ['data' => $params]);
    }

    public function getHistory(): array
    {
        $data = $this->client->send('weather.getHistory', ['lastDays' => 30], isset($_COOKIE['XDEBUG_SESSION']));
        $params['history'] = $this->buildParams($data);
        return $params;
    }

    private function buildParams($data)
    {
        $params = [];
        if (isset($data['result'])) {
            $params['result'] = $data['result'];
            if (empty($data['result'])) {
                $params['error'] = 'Not found';
            }
        }

        if (isset($data['error'])) {
            $params['error'] = $data['error'];
        }

        return $params;
    }
}
