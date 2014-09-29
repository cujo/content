<?php
namespace Cujo\Content;

class File extends Content implements Mutable
{
    private $baseDir;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function get($key)
    {
        $file = $this->findFile($key);
        if ($file) {
            return file_get_contents($file);
        }
    }

    public function find(array $criteria)
    {
        $result = [];
        foreach (glob($this->baseDir . DIRECTORY_SEPARATOR . '*.*') as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $result[] = ['key' => $key, 'value' => $this->get($key)];
        }
        return $result;
    }

    public function set($key, $value)
    {
        $file = $this->findFile($key);
        if ($file) {
            unlink($file);
        } else {
            $file = $this->baseDir . DIRECTORY_SEPARATOR . $this->escapeKey($key) . '.txt'; 
        }
        file_put_contents($file, $value);
    }

    public function remove($key)
    {
        $file = $this->findFile($key);
        if ($file) {
            unlink($file);
        }
    }

    protected function findFile($key)
    {
        $result = glob($this->baseDir . DIRECTORY_SEPARATOR . $this->escapeKey($key) . '.*');
        if (count($result) > 1) {
            throw new \InvalidArgumentException('Key ' . $key . ' is ambiguous');
        }
        return array_pop($result);
    }

    protected function escapeKey($key)
    {
        return preg_replace('/(\/|\\\|\*|\?|\[|\])/', '\\\$1', $key);
    }
}
