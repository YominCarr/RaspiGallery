<?php

require_once __DIR__ . '/IncludeHeader.php';
require_once __DIR__ . '/FileSystemEntity.php';

class FileSystemEntitySorter
{
    protected $sorter = "sortByFileName";

    public function __construct() {
        $this->appendSortingDirectionToSorter();
    }

    protected function appendSortingDirectionToSorter() {
        switch (Config::folderSortingOrder) {
            case SortingOrder::ascending:
                $this->sorter .= "Asc";
                break;
            case SortingOrder::descending:
                $this->sorter .= "Desc";
                break;
        }
    }

    public function sort(array &$array)
    {
        usort($array, array($this, $this->sorter));
    }

    public function sortAssociativeByValue(array &$array)
    {
        uasort($array, array($this, $this->sorter));
    }

    public function sortAssociativeByKey(array &$array)
    {
        uksort($array, array($this, $this->sorter));
    }

    protected function sortByFileNameAsc(FileSystemEntity $a, FileSystemEntity $b)
    {
        return $a->getName() < $b->getName();
    }

    protected function sortByFileNameDesc(FileSystemEntity $a, FileSystemEntity $b)
    {
        return $a->getName() > $b->getName();
    }

}