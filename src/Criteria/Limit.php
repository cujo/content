<?php
namespace Cujo\Content\Criteria;

class Limit extends Criteria
{
    private $limit;
    private $offset;

    public function __construct($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return (int)$this->offset;
    }
}
