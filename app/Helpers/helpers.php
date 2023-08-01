<?php

if (!function_exists('getAssetFilePath'))
{
    function getAssetFilePath($fileName)
    {
        $appEnv = config('app.app_env');
        $onServ = ($appEnv === 'webserver') ? 'public/' : '';

        return asset($onServ . $fileName);
    }
}

?>