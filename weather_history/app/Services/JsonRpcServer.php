<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Response\JsonRpcResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerInterface;

class JsonRpcServer
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(Request $request)
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                throw new JsonRpcException('Parse error', JsonRpcException::PARSE_ERROR);
            }

            [$controllerName, $method] = explode('.', $content['method']);
            $controllerName = 'App\Http\Controllers\\' . (ucfirst($controllerName) . 'Controller');

            Log::debug('Handle context', [$content]);
            Log::debug('Handle controller name', [$controllerName]);
            Log::debug('Handle method', [$method]);

            if (!class_exists($controllerName) || !method_exists($controllerName, $method)) {
                throw new JsonRpcException('Method not found', JsonRpcException::METHOD_NOT_FOUND);
            }

            $controller = $this->container->get($controllerName);
            $result = $controller->{$method}(...[$content['params']]);

            return JsonRpcResponse::success($result, $content['id']);
        } catch (JsonRpcException $e) {
            return JsonRpcResponse::error($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error', ['exception' => $e]);
            return JsonRpcResponse::error('Internal error');
        }
    }
}
