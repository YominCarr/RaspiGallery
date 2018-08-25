<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.08.2018
 * Time: 19:18
 */

function printContent(array $content): string
{
    $ret = "<ul>";
    $ret .= printFolders($content["folders"]);
    $ret .= printImages($content["images"]);
    $ret .= "</ul>";
    return $ret;
}

function printFolders(array $folders): string
{
    $ret = "";
    foreach ($folders as $f) {
        $ret .= "<li>" . $f->getName() . "<ul>";
        $ret .= printContent($f->getContent());
        $ret .= "</ul></li>";
    }
    return $ret;
}

function printImages(array $images): string
{
    $ret = "";
    foreach ($images as $i) {
        $ret .= "<li>" . $i->getName() . "</li>";
    }
    return $ret;
}
