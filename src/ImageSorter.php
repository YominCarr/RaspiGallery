<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Image.php';
require_once __DIR__ . '/FileSystemEntitySorter.php';

class ImageSorter extends FileSystemEntitySorter
{

    public function __construct()
    {
        parent::__construct();

        switch (Config::imageSortingKey) {
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

        $this->appendSortingDirectionToSorter();
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
