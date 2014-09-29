<?php
namespace Cujo\Content;

abstract class Content implements \ArrayAccess
{
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public function __isSet($offset)
    {
        return $this->offsetExists($offset);
    }

    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($this instanceof Mutable) {
            $this->set($offset, $value);
        }
        throw new \Exception('Content ' . get_class($this) . ' is not mutable');
    }

    public function offsetExists($offset)
    {
        return true;
    }

    public function offsetUnset($offset)
    {
        if ($this instanceof Mutable) {
            $this->remove($offset);
        }
        throw new \Exception('Content ' . get_class($this) . ' is not mutable');
    }

    abstract protected function get($key);

    abstract public function find(array $criteria);

    public function isMutable()
    {
        return $this instanceof Mutable;
    }
}
