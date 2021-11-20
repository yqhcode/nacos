<?php

namespace Nacos\Models;

class BeatInfo implements \JsonSerializable
{
    /**
     * 端口
     * @var int
     */
    public $port;

    /**
     * 本地服务ip
     * @var string
     */
    public $ip;

    /**
     *  权重
     * @var double
     */
    public $weight;

    /** 服务名
     * @var string
     */
    public $serviceName;

    /** 保护阈值---
     * @var string
     */
    public $cluster;

    /**
     * 元数据
     * @var array
     */
    public $metadata;

    /** 临时实例
     * @var bool
     */
    public $scheduled;

    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

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
