<?php

namespace Nacos;

use Nacos\Exceptions\NacosNamingNoAliveInstance;
use Nacos\Utils\RandomByWeightSelector;

class NacosNaming
{
    /**
     * @var NacosClient
     */
    protected $client;

    public function __construct(NacosClient $client)
    {
        $this->client = $client;
    }

    public function selectOneHealthyInstance($serviceName)
    {
        $list = $this->client->getInstanceList($serviceName);

        if (count($list->hosts) === 0) {
            throw new NacosNamingNoAliveInstance("$serviceName no alive instnace");
        }

        return RandomByWeightSelector::select($list->hosts);
    }

    /**
     * @return NacosClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
