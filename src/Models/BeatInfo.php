<?php

namespace Nacos\Models;

class BeatInfo implements \JsonSerializable
{
    /**
     * 端口
     * @var int
     */
    public int $port;

    /**
     * 本地服务ip
     * @var string
     */
    public string $ip;

    /**
     *  权重
     * @var double
     */
    public float $weight;

    /** 服务名
     * @var string
     */
    public string $serviceName;

    /** 保护阈值---
     * @var string
     */
    public string $cluster;

    /**
     * 元数据
     * @var array
     */
    public array $metadata;

    /** 临时实例
     * @var bool
     */
    public bool $scheduled;

    public function jsonSerialize()
    {
        return array_filter([
            'port' => $this->port,
            'ip' => $this->ip,
            'weight' => $this->weight,
            'serviceName' => $this->serviceName,
            'cluster' => $this->cluster,
            'metadata' => $this->metadata,
            'scheduled' => $this->scheduled,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
