<?php

namespace Nacos;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Nacos\Config\NacosConfig;
use Nacos\Exceptions\NacosConfigNotFound;
use Nacos\Exceptions\NacosNamingNotFound;
use Nacos\Exceptions\NacosRequestException;
use Nacos\Models\BeatInfo;
use Nacos\Models\BeatResult;
use Nacos\Models\Config;
use Nacos\Models\ServiceInstance;
use Nacos\Models\ServiceInstanceList;

class NacosClient
{


    /** 服务地址
     * @var string
     */
    public $ip;

    /** 端口
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $namespace;

    protected  $timeout = NacosConfig::DEFAULT_TIME_OUT;

    public function __construct(string $ip = NacosConfig::DEFAULT_IP, int $port = NacosConfig::DEFAULT_PORT)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @param string $namespace
     * @return static
     */
    public function setNamespace(string $namespace): NacosClient
    {
        $this->namespace = $namespace;
        return $this;
    }

    /** 长轮训等待时间
     * @param int $timeout
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    /** 提交请求
     * @param string $method 请求类型
     * @param string $path 请求路径
     * @param array $options 参数
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $path, array $options = [])
    {
        if (!isset($options['timeout'])) {
            $options['timeout'] = $this->timeout;
        }

        $client = new Client();
        $requestUrl = "{$this->ip}:{$this->port}{$path}";  //请求路径
        try {
            $res = $client->request($method, $requestUrl, $options);
        } catch (RequestException $exception) {
            throw new NacosRequestException("{$method} {$requestUrl} request fail", $exception->getCode(), $exception);
        }

        return $res;
    }





    public function assertResponse(Response $resp, $expected, $message)
    {
        $actual = $resp->getBody()->__toString();
        if ($expected !== $actual) {
            throw new NacosRequestException("$message, actual: {$actual}");
        }
    }


   
}
