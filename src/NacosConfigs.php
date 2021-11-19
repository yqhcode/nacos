<?php
/**
 * Nacos配置
 */
namespace Nacos;

use Nacos\Config\NacosConfig;
use Nacos\Exceptions\NacosConfigNotFound;
use Nacos\Exceptions\NacosException;
use Nacos\Exceptions\NacosRequestException;
use Nacos\Models\Config;
use Nacos\Utils\PropertiesConfigParser;
use Nacos\NacosClient;

class NacosConfigs
{
    /**
     * @var NacosClient
     */
    protected NacosClient $client;

    public function __construct(NacosClient $client)
    {
        $this->client = $client;
    }

    /**
     * 获取配置项
     * @param string $dataId
     * @param string $group
     * @return string
     * @throws NacosConfigNotFound|\GuzzleHttp\Exception\GuzzleException
     */
    public function getConfigs(string $dataId, string $group = NacosConfig::DEFAULT_GROUP)
    {
        echo 22;
        $query = [
            'dataId' => $dataId,
            'group' => $group,
        ];

        if ($this->client->namespace) {
            $query['tenant'] = $this->client->namespace;
        }

        $resp = $this->client->request('GET', NacosConfig::NACOS_CONFIGS, [
            'http_errors' => false,
            'query' => $query
        ]);

        if (NacosConfigNotFound::NOT_FOUND === $resp->getStatusCode()) {
            throw new NacosConfigNotFound(
                "获取配置->无法找到资源, dataId:{$dataId} group:{$group} tenant:{$this->client->namespace}",
                NacosConfigNotFound::NOT_FOUND
            );
        }

        return $resp->getBody()->__toString();
    }
    /**
     * 发布配置
     * @param string $dataId
     * @param string $group
     * @param $content
     * @return bool
     * @throws NacosRequestException
     */
    public function publishConfig(string $dataId, string $group, $content)
    {
        $formParams = [
            'dataId' => $dataId,
            'group' => $group,
            'content' => $content,
        ];

        if ($this->namespace) {
            $formParams['tenant'] = $this->namespace;
        }

        $resp = $this->request('POST', NacosConfig::NACOS_CONFIGS, ['form_params' => $formParams]);
        $this->assertResponse($resp, 'true', "NacosClient update config fail");

        return true;
    }

    /**
     * 删除配置
     * @param string $dataId
     * @param string $group
     * @return bool
     * @throws NacosRequestException
     */
    public function removeConfig(string $dataId, string $group = NacosConfig::DEFAULT_GROUP): bool
    {
        $query = [
            'dataId' => $dataId,
            'group' => $group,
        ];

        if ($this->namespace) {
            $query['tenant'] = $this->namespace;
        }

        $resp = $this->request('DELETE', NacosConfig::NACOS_CONFIGS, ['query' => $query]);
        $this->assertResponse($resp, 'true', "NacosClient delete config fail");

        return true;
    }


    /**
     * 监听配置
     * @param Config[] $configs
     * @param int $timeout 长轮训等待事件，默认 30 ，单位：秒
     * @return Config[]
     */
    public function listenConfig(array $configs, int $timeout = 30): array
    {
        $configStringList = [];
        foreach ($configs as $cache) {
            $items = [$cache->dataId, $cache->group, $cache->contentMd5];
            if ($cache->namespace) {
                $items [] = $cache->namespace;
            }
            $configStringList[] = join(NacosConfig::WORD_SEPARATOR, $items);
        }
        $configString = join(NacosConfig::LINE_SEPARATOR, $configStringList) . NacosConfig::LINE_SEPARATOR;

        $resp = $this->request('POST', NacosConfig::NACOS_CONFIGS_LISTENER, [
            'timeout' => $timeout + $this->timeout,
            'headers' => ['Long-Pulling-Timeout' => $timeout * 1000],
            'form_params' => [
                'Listening-Configs' => $configString,
            ],
        ]);

        $respString = $resp->getBody()->__toString();
        if (!$respString) {
            return [];
        }

        $changed = [];
        $lines = explode(NacosConfig::LINE_SEPARATOR, urldecode($respString));
        foreach ($lines as $line) {
            $parts = explode(NacosConfig::WORD_SEPARATOR, $line);
            $c = new Config();
            if (count($parts) === 3) {
                list($c->dataId, $c->group, $c->namespace) = $parts;
            } elseif (count($parts) === 2) {
                list($c->dataId, $c->group) = $parts;
            } else {
                continue;
            }
            $changed[] = $c;
        }
        return $changed;
    }
























    /**
     * 获取配置内容并解析，现仅支持 properties 格式
     *
     * @param string $dataId
     * @param string $group
     * @param string $format
     * @return array
     */
    public function getParsedConfigs(string $dataId, string $group = NacosConfig::DEFAULT_GROUP, string $format = 'properties'): array
    {
        $content = $this->client->getConfig($dataId, $group);

        if (!$format) {
            $format = array_slice(explode('.', $dataId), -1)[0];
        }

        if ($format === 'properties') {
            return PropertiesConfigParser::parse($content);
        }

        throw new NacosException('Unsupported config format');
    }

    /**
     * @return NacosClient
     */
    public function getClient(): NacosClient
    {
        return $this->client;
    }
}
