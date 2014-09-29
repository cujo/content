<?php
namespace Cujo\Content;

class Prefix extends Proxy
{
    private $prefix;

    public function __construct($content, $prefix)
    {
        parent::__construct($content);
        $this->prefix = $prefix;
    }

    public function get($key)
    {
        return parent::get($this->prefix . $key);
    }

    public function find(array $criteria)
    {
        $result = [];
        $len = strlen($this->prefix);
        foreach (parent::find($criteria) as $key => $value) {
            if (0 === strpos($key, $this->prefix)) {
                $result[substr($key, $len)] = $value;
            }
        }
        return $result;
    }

    public function set($key, $value)
    {
        return parent::set($this->prefix . $key, $value);
    }

    public function remove($key)
    {
        return parent::remove($this->prefix . $key);
    }
}
