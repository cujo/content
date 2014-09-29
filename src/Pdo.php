<?php
namespace Cujo\Content;

use Cujo\Content\Criteria;

class Pdo extends Content implements Mutable
{
    private $pdo;
    private $table;
    private $group;

    private $stGet;
    private $stSetExists;
    private $stSetUpdate;
    private $stSetInsert;
    private $stRemove;

    public function __construct(\PDO $pdo, $table, $group)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        if (mb_strlen($group) > 64) {
            throw new \InvalidArgumentException('group length must be not more than 64 chars');
        }
        $this->group = $group;
    }

    public function get($key)
    {
        if (!$this->stGet) {
            $this->stGet = $this->pdo->prepare('SELECT `value` FROM `' . $this->table . '` WHERE `key`=? AND `group`=?'); 
        }
        $this->stGet->execute([$key, $this->group]);
        return $this->stGet->fetchColumn();
    }

    public function find(array $criteria)
    {
        $fields = '*';
        $limit = '';
        $where = '';
        foreach ($criteria as $item) {
            if ($item instanceof Criteria\Limit) {
                $limit = ' LIMIT ' . (int)$item->getLimit() . ' OFFSET ' . (int)$item->getOffset();
            }
            if ($item instanceof Criteria\Search) {
                switch ($item->getMode()) {
                    case null :
                    case Criteria\Search::MODE_PREFIX :
                        $where .= ' AND `key` LIKE ' . $this->pdo->quote(str_replace('%', '%%', $item->getSearch()) . '%');
                        break;
                    case Criteria\Search::MODE_KEY :
                        $where .= ' AND `key` LIKE ' . $this->pdo->quote(str_replace('%', '%%', '%' . $item->getSearch()) . '%');
                        break;
                    case Criteria\Search::MODE_VALUE :
                        $where .= ' AND `value` LIKE ' . $this->pdo->quote(str_replace('%', '%%', '%' . $item->getSearch()) . '%');
                        break;
                }
            }
        }
        $sql = 'SELECT ' . $fields
            . ' FROM `' . $this->table
            . '` WHERE `group`=' . $this->pdo->quote($this->group) . $where
            . ' ORDER BY `key`'
            . $limit;
        $st = $this->pdo->prepare($sql);
        $st->execute();
        return $st;
    }

    public function set($key, $value)
    {
        if (!$this->stSetExists) {
            $this->stSetExists = $this->pdo->prepare('SELECT 1 FROM `' . $this->table . '` WHERE `key`=? AND `group`=?'); 
        }
        $this->stSetExists->execute([$key, $this->group]);
        if ($this->stSetExists->rowCount()) {
            if (!$this->stSetUpdate) {
                $this->stSetUpdate = $this->pdo->prepare('UPDATE `' . $this->table . '` SET `value`=? WHERE `key`=? AND `group`=?'); 
            }
            $this->stSetUpdate->execute([$value, $key, $this->group]);
        } else {
            if (!$this->stSetInsert) {
                $this->stSetInsert = $this->pdo->prepare('INSERT INTO `' . $this->table . '`(`key`, `value`, `group`) VALUES (?,?,?)'); 
            }
            $this->stSetInsert->execute([$key, $value, $this->group]);
        }
    }

    public function remove($key)
    {
        if (!$this->stRemove) {
            $this->stRemove = $this->pdo->prepare('DELETE FROM `' . $this->table . '` WHERE `key`=? AND `group`=?'); 
        }
        $this->stRemove->execute([$key, $this->group]);
    }

    public function initialize()
    {
        $this->pdo->execute('
            CREATE TABLE `' . $this->table . '` (
                `key` VARCHAR(255) NOT NULL,
                `group` VARCHAR(64) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`key`,`group`),
                KEY `group` (`group`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8
        ');
    }
}
