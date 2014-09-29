<?php
namespace Cujo\Content;

interface Mutable
{
    public function set($key, $value);
    public function remove($key);
}
