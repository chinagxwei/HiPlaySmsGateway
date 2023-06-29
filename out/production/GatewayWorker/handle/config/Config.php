<?php

namespace handle\config;

class Config
{
    const DEBUG = false;

    const API_DEBUG_HOST = "https://hhw.hhw1.cn";

    const API_PRODUCTION_HOST = "https://hhw.hhw1.cn";

    const Version = 'v1';

    public static function getHost()
    {
        return self::DEBUG ? self::API_DEBUG_HOST : self::API_PRODUCTION_HOST;
    }
}