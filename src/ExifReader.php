<?php

require_once __DIR__ . '/ExifData.php';

class ExifReader
{

    public function readExifDataFromImage(string $image) : ExifData {
        $data = new ExifData();

        $exif = read_exif_data($image);

        $this->assignExifValueIfPresent($exif, "Model", $data->camera);

        $this->assignExifValueIfPresent($exif, "ISOSpeedRatings", $data->iso);
        $data->iso = "ISO " . ($data->iso ? round($data->iso, 1) : "?");

        $this->assignExifValueIfPresent($exif, "ExposureTime", $data->shutter);
        $data->shutter = ($data->shutter ? round($data->shutter, 1) : "?") . " sec";

        $this->assignExifValueIfPresent($exif, "FNumber", $data->aperture);
        $data->aperture = "F " . ($data->aperture ? round($data->aperture, 1) : "?");

        $this->assignExifValueIfPresent($exif, "FocalLength", $data->focal);
        $data->focal = ($data->focal ? round($data->focal, 1) : "?") . " mm";

        // In case only DateTime exists
        $this->assignExifValueIfPresent($exif, "DateTime", $data->dateTaken);
        $this->assignExifValueIfPresent($exif, "DateTimeOriginal", $data->dateTaken);

        /*echo "XX" . $data->shutter . "ZZ" . floatval($data->shutter) . "YY<br>";
        if (!empty($data->shutter) && floatval($data->shutter) < 0.25) {
            $data->shutter = "1/" . (1.0 / floatval($data->shutter));
        }*/

        return $data;
    }

    private function assignExifValueIfPresent(array &$exif, string $key, string &$target) {
        if (isset($exif[$key])) {
            $target = $exif[$key];
        } else {
            $target = "";
        }
    }

}
