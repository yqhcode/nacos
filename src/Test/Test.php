<?php
use Nacos\Models\BeatInfo;
use Nacos\NacoInstance;
use Nacos\NacosClient;
//
//        $nacosClient = new NacosClient('10.66.66.151', '8848');
//        $dataId = 'mshop.yaml';
//        $group = 'DEFAULT_GROUP';
//        $serviceInstance = new ServiceInstance();
//        $serviceInstance->ip = '10.0.10.173';
//        $serviceInstance->port = '80';
//        $serviceInstance->serviceName = 'mshop';
//        $value = $nacosClient->createInstance($serviceInstance);
//        print_r($value);

//       $client = new NacosClient();
//        $configs = new NacosConfigs(new NacosClient());
//       var_dump( $configs->getConfigs('psi'));  //huoqu
//        var_dump($configs->setConfigs('psi','yyyyyyy23yy'));  //shezhi
//        var_dump($configs->delConfigs('psi'));
//        var_dump($configs->historyConfigs('psi',true));

// zhuc

//        $instance = new ServiceInstance([
//            'ip' => '10.0.10.122',
//            'port' => 8000,
//            'serviceName' => 'yckj-psi',
//        ]);
//        $nacosinstance = new NacoInstance(new NacosClient());
//        var_dump($nacosinstance->registerInstance($instance));

//shanc

//        $nacosinstance = new NacoInstance(new NacosClient());
//        var_dump($nacosinstance->delInstance('psi','10.0.10.122','8000'));

//update
//        $instance = new ServiceInstance([
//            'ip' => '10.0.10.122',
//            'port' => '8000',
//            'serviceName' => 'yckj-psi',
//        ]);
//        $nacosinstance = new NacoInstance(new NacosClient());
//        var_dump($nacosinstance->updateInstance($instance));



//sendBeat

//while (true){
//    sleep(5);
//    $beat = new BeatInfo([
//        'ip' => '10.0.10.122',
//        'port' => 8000,
//        'serviceName' => 'yckj-psi',
//        'scheduled' => true,
//        'weight' => 1
//    ]);
//    $nacosinstance = new NacoInstance(new NacosClient());
//    var_dump($nacosinstance->sendInstanceBeat('psi',$beat));
//}