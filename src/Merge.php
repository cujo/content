<?php
namespace Cujo\Content;

class Merge extends Content
{
    private $contents;

    public function __construct(array $contents)
    {
        $this->contents = $contents;
    }

    public function get($key)
    {
        foreach ($this->contents as $content) {
            $value = $content->get($key);
            if (false !== $value && null !== $value) {
                return $value;
            }
        }
        return false;
    }

    public function find(array $criteria)
    {
        $result = [];
        foreach (array_reverse($this->contents) as $content) {
            $result = array_merge($result, $content->find($criteria));
        }
        return $result;
    }
}
