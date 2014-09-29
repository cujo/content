<?php
namespace Cujo\Content;

class Proxy extends Content implements Mutable
{
    private $content;

    public function __construct($content)
    {
        if (($content instanceof \Closure) || ($content instanceof Content)) {
            $this->content = $content;
        } else {
            throw new \InvalidArgumentException('content must be Content or Closure object, ' . gettype($content) . ' given');
        }
        $this->content = $content;
    }

    public function get($key)
    {
        return $this->getContent()->get($key);
    }

    public function find(array $criteria)
    {
        return $this->getContent()->find($criteria);
    }

    public function set($key, $value)
    {
        $this->getContent()->set($key, $value);
    }

    public function remove($key)
    {
        $this->getContent()->remove($key);
    }

    public function isMutable()
    {
        return ($this->getContent() instanceof Mutable);
    }

    public function getContent()
    {
        if ($this->content instanceof \Closure) {
            $func = $this->content;
            $content = $func();
            if ($content instanceof Content) {
                $this->content = $content;
            } else {
                throw new \InvalidArgumentException('closure must return Content object, ' . gettype($content) . ' found');
            }
        }
        return $this->content;
    }
}
