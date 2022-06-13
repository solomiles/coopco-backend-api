<?php

namespace App\Traits;

trait CsvTrait
{

    /**
     * Set the structure of the CSV file
     *
     * @param array $columns
     * @param string $name
     *
     * @return boolean (true|false)
     */
    public function setCSVStructure($columns, $name)
    {
        $filename = public_path("storage/files/" . $name . ".csv");
        $handle = fopen($filename, 'w');

        if (fputcsv($handle, $columns) == false) {
            return false;
        } else {
            return true;
        }
    }
}
