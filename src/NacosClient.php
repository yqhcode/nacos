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
    public string $ip;

    /** 端口
     * @var int
     */
    public int $port;

    /**
     * @var string
     */
    public string $namespace;

    protected int $timeout = NacosConfig::DEFAULT_TIME_OUT;

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





    protected function assertResponse(Response $resp, $expected, $message)
    {
        $actual = $resp->getBody()->__toString();
        if ($expected !== $actual) {
            throw new NacosRequestException("$message, actual: {$actual}");
        }
    }


    /**
     * 注册一个实例到服务
     * @param ServiceInstance $instance
     * @return bool
     */
    public function createInstance(ServiceInstance $instance): bool
    {
        $instance->validate();
        $resp = $this->request('POST', NacosConfig::NACOS_INSTANCE, ['form_params' => $instance->toCreateParams()]);
        $this->assertResponse($resp, 'ok', "NacosClient create service instance fail");

        return true;
    }

    /**
     * 删除服务下的一个实例
     * @param string $serviceName
     * @param string $ip
     * @param int $port
     * @param string|null $clusterName
     * @param string|null $namespaceId
     * @return bool
     */
    public function deleteInstance(string $serviceName, string $ip, int $port, string $clusterName = null, string $namespaceId = null): bool
    {
        $query = array_filter(compact('serviceName', 'ip', 'port', 'clusterName', 'namespaceId'));
        $resp = $this->request('DELETE', NacosConfig::NACOS_INSTANCE, ['query' => $query]);
        $this->assertResponse($resp, 'ok', "NacosClient delete service instance fail");

        return true;
    }

    public function updateInstance(ServiceInstance $instance): bool
    {
        $instance->validate();
        $resp = $this->request('PUT', NacosConfig::NACOS_INSTANCE, ['form_params' => $instance->toUpdateParams()]);
        $this->assertResponse($resp, 'ok', "NacosClient update service instance fail");

        return true;
    }

    /**
     * 查询服务下的实例列表
     *
     * @param string $serviceName 服务名
     * @param string|null $namespaceId 命名空间ID
     * @param string[] $clusters 集群名称
     * @param bool $healthyOnly 是否只返回健康实例
     * @return ServiceInstanceList
     */
    public function getInstanceList(
        string $serviceName,
        string $namespaceId = null,
        array $clusters = [],
        bool $healthyOnly = false
    )
    {
        $query = array_filter([
            'serviceName' => $serviceName,
            'namespaceId' => $namespaceId,
            'clusters' => join(',', $clusters),
            'healthyOnly' => $healthyOnly,
        ]);

        $resp = $this->request('GET', NacosConfig::NACOS_INSTANCE_LIST, [
            'http_errors' => false,
            'query' => $query,
        ]);
        $data = json_decode($resp->getBody(), JSON_OBJECT_AS_ARRAY);

        if (404 === $resp->getStatusCode()) {
            throw new NacosNamingNotFound(
                "service not found: $serviceName",
                404
            );
        }

        return new ServiceInstanceList($data);
    }

    /**
     * 查询一个服务下个某个实例详情
     *
     * @param string $serviceName 服务名
     * @param string $ip 实例IP
     * @param int $port 实例端口
     * @param string|null $namespaceId 命名空间 id
     * @param string|null $cluster 集群名称
     * @param bool $healthyOnly 是否只返回健康实例
     * @return ServiceInstance
     */
    public function getInstance(string $serviceName, string $ip, int $port, string $namespaceId = null, string $cluster = null, bool $healthyOnly = false)
    {
        $query = array_filter(compact(
            'serviceName',
            'ip',
            'port',
            'namespaceId',
            'cluster',
            'healthyOnly'
        ));

        $resp = $this->request('GET', NacosConfig::NACOS_INSTANCE, ['query' => $query]);
        $data = json_decode($resp->getBody(), JSON_OBJECT_AS_ARRAY);
        $data['serviceName'] = $data['service'];

        return new ServiceInstance($data);
    }

    /**
     * 发送实例心跳
     * @param string $serviceName
     * @param BeatInfo $beat
     * @return BeatResult
     */
    public function sendInstanceBeat(string $serviceName, BeatInfo $beat): BeatResult
    {
        $formParams = [
            'serviceName' => $serviceName,
            'beat' => json_encode($beat),
        ];

        $resp = $this->request('PUT', NacosConfig::NACOS_INSTANCE_BEAT, ['form_params' => $formParams]);
        $array = json_decode($resp->getBody(), JSON_OBJECT_AS_ARRAY);

        $result = new BeatResult();
        $result->clientBeatInterval = $array['clientBeatInterval'];
        return $result;
    }
}
