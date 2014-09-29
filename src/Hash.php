<?php
namespace Cujo\Content;

class Hash extends Content implements Mutable
{
    private $data;

    public function __construct($data)
    {
        if (is_array($data) || ($data instanceof ArrayAccess)) {
            $this->data = $data;
        } else {
            throw new \InvalidArgumentException('data must be an array or ArrayAccess object, ' . gettype($data) . ' given');
        }
    }

    public function get($key)
    {
        $value = isset($this->data[$key]) ? $this->data[$key] : null;
        if ($value instanceof \Closure) {
            $value = $value();
        }
        return $value;
    }

    public function find(array $criteria)
    {
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }
}
