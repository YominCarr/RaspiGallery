<?php

require_once __DIR__ . '/IncludeHeader.php';
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

    protected function sortByCreationDateAsc(Image $a, Image $b)
    {
        return $a->getCreationDate() < $b->getCreationDate();
    }

    protected function sortByCreationDateDesc(Image $a, Image $b)
    {
        return $a->getCreationDate() > $b->getCreationDate();
    }

    protected function sortByModificationDateAsc(Image $a, Image $b)
    {
        return $a->getModificationDate() < $b->getModificationDate();
    }

    protected function sortByModificationDateDesc(Image $a, Image $b)
    {
        return $a->getModificationDate() > $b->getModificationDate();
    }

    protected function sortByShotDateAsc(Image $a, Image $b)
    {
        return $a->getExifData()->dateTaken < $b->getExifData()->dateTaken;
    }

    protected function sortByShotDateDesc(Image $a, Image $b)
    {
        return $a->getExifData()->dateTaken > $b->getExifData()->dateTaken;
    }
}
