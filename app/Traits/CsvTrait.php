<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
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
        $filename = public_path('storage/csv-templates/' . $name . '.csv');

        if (Storage::exists('public/csv-templates')) {
            $handle = fopen($filename, 'w');
        } else {
            Storage::makeDirectory('public/csv-templates');
            $handle = fopen($filename, 'w');
        }

        if (fputcsv($handle, $columns) == false) {
            return false;
        } else {
            return true;
        }
    }
}
