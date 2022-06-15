<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

        return fputcsv($handle, $columns) != false;
    }

    /**
     * Provides download url for generated CSV template
     *
     * @param string $fileName
     * @return (string | boolean)
     */
    public function downloadCSVTemplate($fileName)
    {
        $file = 'public/csv-templates/' . $fileName . '.csv';

        if (Storage::exists($file)) {
            return URL::to('/') . Storage::url($file);
        }
        return false;
    }
}
