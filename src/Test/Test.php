<?php


namespace Nacos\Test;


use Nacos\NacosClient;
use Nacos\NacosConfigs;

class Test
{
    public function TestGetConfigs(){
//        $nacosConfigs = new NacosConfigs(new NacosClient());
        $NacosClient = new NacosClient();
        print_r($NacosClient->ip);die();
        $configs = $nacosConfigs->getConfigs('psi');
        var_dump($configs);
    }
}
//echo 11;
//$test = new Test();
//$test->TestGetConfigs();

$NacosClient = new NacosClient('1231',11);
print_r($NacosClient);die();