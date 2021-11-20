<?php
namespace Nacos\Config;

class NacosConfig
{
    const DEFAULT_IP = 'http://10.66.66.175';  //服务地址
    const DEFAULT_PORT = 8848;  //端口
    const DEFAULT_TIME_OUT = 10;
    const DEFAULT_GROUP = 'DEFAULT_GROUP';
    const WORD_SEPARATOR = "\x02";
    const LINE_SEPARATOR = "\x01";


    const NACOS_CONFIGS = '/nacos/v1/cs/configs';  //获取Nacos上的配置 GET |发布配置 POST | 删除配置 DELETE
    const NACOS_CONFIGS_LISTENER = '/nacos/v1/cs/configs/listener';  //监听配置 POST

//    const NACOS_HISTORY_ACCURATE = '/nacos/v1/cs/history?search=accurate';  //查询历史版本 GET
    const NACOS_HISTORY = '/nacos/v1/cs/history';  //查询历史版本详情 GET   -----------
    const NACOS_HISTORY_PREVIOUS = '/nacos/v1/cs/history/previous';  //查询配置上一版本信息 GET  -------------

    const NACOS_INSTANCE = '/nacos/v1/ns/instance';  //注册实例 POST |注销实例 DELETE |修改实例 PUT |查询实例详情 GET
    const NACOS_INSTANCE_LIST = '/nacos/v1/ns/instance/list';  //查询实例列表 GET
    const NACOS_INSTANCE_BEAT = '/nacos/v1/ns/instance/beat';  //发送实例心跳 PUT

    const NACOS_SERVICE = '/ /v1/ns/service';  //创建服务 POST |删除服务 DELETE |修改服务 PUT |查询服务 GET
    const NACOS_SERVICE_LIST = '/nacos/v1/ns/service/list';  //查询服务列表 GET

    const NACOS_OPERATOR_SWITCHES = '/nacos/v1/ns/operator/switches';  //查询系统开关 GET |修改系统开关 PUT |
    const NACOS_OPERATOR_METRICS = '/nacos/v1/ns/operator/metrics';  //查看系统当前数据指标 GET
    const NACOS_OPERATOR_SERVERS = '/nacos/v1/ns/operator/servers';  //查看当前集群Server列表 GET
    const NACOS_RAFT_LEADER = '/nacos/v1/ns/raft/leader';  //查看当前集群leader GET
    const NACOS_HEALTH_INSTANCE = '/nacos/v1/ns/health/instance';  //更新实例的健康状态 PUT
    const NACOS_INSTANCE_METADATA_BATCH = '/nacos/v1/ns/instance/metadata/batch';  //实例元数据(Beta)批量更新 PUT |删除 DELETE|
    const NACOS_console_namespaces = '/nacos/v1/console/namespaces';  //命名空间 GET |创建 POST |修改 PUT |删除 DELETE
}