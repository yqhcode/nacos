<?php

namespace Nacos\Models;

use Nacos\Config\NacosConfig;

class Config
{
    /**
     * @var string
     */
    public string $dataId;

    /**
     * @var string
     */
    public string $group = NacosConfig::DEFAULT_GROUP;

    /**
     * @var string
     */
    public string $contentMd5;

    /**
     * @var string
     */
    public string $namespace;
}
