<?php
namespace Kwasett\LaravelCommon\Utils;

use Illuminate\Support\Facades\Log;

class ProjectLog
{

    static function debug($message)
    {
        Log::debug(self::initialData(). "] ".$message);
    }


    public static function info($message)
    {
        Log::info(self::initialData(). "] ".$message);
    }


    public static function error($message)
    {
        Log::error(self::initialData(). "] ".$message);
    }


    public static function critical($message)
    {
        Log::critical(self::initialData(). "] ".$message);
    }


    public static function initialData()
    {
        return request(ProjectConstants::TRACE_ID);
    }

}
