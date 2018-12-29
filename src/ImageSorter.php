<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Image.php';

class ImageSorter
{
    private $sorter;

    public function __construct()
    {
        switch (Config::sortingKey) {
            case SortingMethod::fileName:
                $this->sorter = "sortByFileName";
                break;
            case SortingMethod::fileCreationDate:
                $this->sorter = "sortByCreationDate";
                break;
            case SortingMethod::fileModificationDate:
                $this->sorter = "sortByModificationDate";
                break;
            case SortingMethod::photoShotDate:
                $this->sorter = "sortByShotDate";
                break;
        }

        switch (Config::sortingOrder) {
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

    private function sortByFileNameAsc(Image $a, Image $b)
    {
        return $a->getName() < $b->getName();
    }

    private function sortByFileNameDesc(Image $a, Image $b)
    {
        return $a->getName() > $b->getName();
    }

    private function sortByCreationDateAsc(Image $a, Image $b)
    {
        return $a->getCreationDate() < $b->getCreationDate();
    }

    private function sortByCreationDateDesc(Image $a, Image $b)
    {
        return $a->getCreationDate() > $b->getCreationDate();
    }

    private function sortByModificationDateAsc(Image $a, Image $b)
    {
        return $a->getModificationDate() < $b->getModificationDate();
    }

    private function sortByModificationDateDesc(Image $a, Image $b)
    {
        return $a->getModificationDate() > $b->getModificationDate();
    }

    // @todo get exif info and implement these two
    private function sortByShotDateAsc(Image $a, Image $b)
    {
        return 0;
    }

    private function sortByShotDateDesc(Image $a, Image $b)
    {
        return 0;
    }
}
