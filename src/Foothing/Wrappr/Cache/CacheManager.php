<?php namespace Foothing\Wrappr\Cache;


class CacheManager {
    public static $EXPIRE = 3600;

    public function put($key, $value, $expire = null) {
        //\Log::debug("Wrappr: cache $key");
        return \Cache::put("wrappr:$key", $value, $expire ?: self::$EXPIRE);
    }

    public function get($key) {
        //return null;
        return \Cache::get("wrappr:$key");
    }

    public function has($key) {
        return \Cache::has($key);
    }
}
