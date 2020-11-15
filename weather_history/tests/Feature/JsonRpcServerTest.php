<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Response\JsonRpcResponse;
use App\Models\History;
use Tests\TestCase;

class JsonRpcServerTest extends TestCase
{
    public function testGetByDate()
    {
        $history = History::first()->toArray();

        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getByDate',
            'params' => ['date' => $history['date_at']]
        ]);

        $response->assertJsonFragment(['result' => $history]);
    }

    public function testGetByDateNotFound()
    {
        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getByDate',
            'params' => ['date' => '1978-01-01']
        ]);

        $response->assertJsonFragment(['result' => []]);
    }

    public function testGetByDateFail()
    {
        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getByDate',
            'params' => ['date' => '1978-01-']
        ]);
        $response->assertJsonFragment(['error' => 'Invalid params']);

        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getByDate',
            'params' => ['date' => '']
        ]);
        $response->assertJsonFragment(['error' => 'Invalid params']);
    }

    public function testGetByDateNotMethod()
    {
        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getByDate2',
            'params' => ['date' => '1978-01-']
        ]);

        $response->assertJsonFragment(['error' => 'Method not found']);

        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather2.getByDate',
            'params' => ['date' => '1978-01-']
        ]);

        $response->assertJsonFragment(['error' => 'Method not found']);
    }

    public function testGetByDateNotContext()
    {
        $response = $this->postJson('/api/data', []);
        $response->assertJsonFragment(['error' => 'Parse error']);
    }

    public function testGetHistory()
    {
        $lastDays = 3;
        $historyArray = History::orderBy('date_at', 'desc')->take($lastDays)->get()->toArray();

        $response = $this->postJson('/api/data', [
            'jsonrpc' => JsonRpcResponse::JSON_RPC_VERSION,
            'id' => time(),
            'method' => 'weather.getHistory',
            'params' => ['lastDays' => $lastDays]
        ]);

        $response->assertJsonFragment(['result' => $historyArray]);
    }
}
