<?php

namespace Nacos\Exceptions;

class NacosConfigNotFound extends NacosRequestException
{
    const NOT_FOUND	= 404;  //无法找到资源
    const INTERNAL_SERVER_ERROR	= 500;  //服务器内部错误
    const FORBIDDEN	= 403;  //没有权限
    const BAD_REQUEST	= 400;  //客户端请求中的语法错误

    protected $code = self::NOT_FOUND;
}
