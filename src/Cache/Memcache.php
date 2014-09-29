<?php
namespace Cujo\Content\Cache;

use Cujo\Content\Content;

class Memcache extends Cache
{
    protected $memcache;

    public function __construct($content, \Memcache $memcache, $prefix = null, $ttl = null)
    {
        parent::__construct($content, $prefix, $ttl);
        $this->memcache = $memcache;
    }

    protected function createCache(\Content $content, $prefix, $ttl)
    {
        return new Memcache($content, $this->memcache, $prefix, $ttl);
    }

    protected function getFromCache($key)
    {
        return $this->memcache->get($key);
    }

    protected function addToCache($key, $value, $ttl)
    {
        $this->memcache->add($key, $value, $ttl);
    }

    protected function deleteFromCache($key)
    {
        $this->memcache->delete($key);
    }
}
