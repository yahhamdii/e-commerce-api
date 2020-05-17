<?php

namespace Sogedial\ApiBundle\Service;

class ConvertCsvToArray {

    /**
     * @param $filename
     * @param string $delimiter
     * @return array|bool
     */
    public function convert($filename, $delimiter = ',') {
        //dump(($filename));
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        $csv = explode("\n", file_get_contents($filename));
        $array = [];
        foreach ($csv as $line) {
            if ($line)
                $array[] = str_getcsv($line, $delimiter);
        }
        return $array;
    }

}
