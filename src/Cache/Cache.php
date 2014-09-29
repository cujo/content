<?php
namespace Cujo\Content\Cache;

use Cujo\Content\Content;
use Cujo\Content\Proxy;

abstract class Cache extends Proxy
{
    protected $prefix;
    protected $ttl;

    public function __construct($content, $prefix = null, $ttl = null)
    {
        parent::__construct($content);
        $this->prefix = $prefix;
        $this->ttl = $ttl;
    }

    abstract protected function getFromCache($key);
    abstract protected function addToCache($key, $value, $ttl);
    abstract protected function deleteFromCache($key);

    protected function getCacheKey($key)
    {
        return $this->prefix . $key;
    }

    protected function createCache(Content $content, $prefix, $ttl)
    {
        $class = new get_class();
        return new $class($value, $prefix, $ttl);
    }

    public function get($key)
    {
        $cacheKey = $this->getCacheKey($key);
        $value = $this->getFromCache($cacheKey);
        if (false === $value) {
            $value = parent::get($key);
            $this->addToCache($cacheKey, $value, $this->ttl);
        }
        if ($value instanceof Content) {
            $value = $this->createCache($value, $cacheKey . '#', $this->ttl);
        }
        return $value;
    }

    public function set($key, $value)
    {
        $this->deleteFromCache($key);
        parent::set($key, $value);
    }

    public function remove($key)
    {
        $this->deleteFromCache($key);
        parent::remove($key);
    }
}
