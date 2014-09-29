<?php
namespace Cujo\Content\Criteria;

class Search extends Criteria
{
    const MODE_KEY = 'key';
    const MODE_PREFIX = 'prefix';
    const MODE_VALUE = 'value';

    private $search;

    public function __construct($search, $mode = null)
    {
        $this->search = $search;
        $this->mode = $mode;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function getMode()
    {
        return $this->mode;
    }
}
