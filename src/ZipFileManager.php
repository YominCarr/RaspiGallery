<?php

class ZipFileManager
{

    public function createZip($imageNames, $imagePaths, $destination)
    {
        if (!extension_loaded('zip')) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        for ($i = 0; $i < sizeof($imagePaths) && $i < sizeof($imageNames); ++$i) {
            $name = $imageNames[$i];
            $path = $imagePaths[$i];

            if (!file_exists($path)) {
                continue;
            }

            $path = str_replace('\\', '/', realpath($path));

            if (is_file($path) === true) {
                $zip->addFromString($name, file_get_contents($path));
            }
        }

        return $zip->close();
    }

}