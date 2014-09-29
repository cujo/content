<?php
namespace Cujo\Content\Cache;

use Cujo\Content\Content;

class Apc extends Cache
{
    protected function getFromCache($key)
    {
        $success = false;
        $value = apc_fetch($key, $success);
        return $success ? $value : false;
    }

    protected function addToCache($key, $value, $ttl)
    {
        apc_add($key, $value, $ttl);
    }

    protected function deleteFromCache($key)
    {
        apc_delete($key);
    }
}
