<?php
namespace App\Exports;

use vitorccs\LaravelCsv\Concerns\Exportable;
use vitorccs\LaravelCsv\Concerns\FromArray;
use vitorccs\LaravelCsv\Concerns\WithHeadings;

class ExcelExport implements FromArray, WithHeadings
{
    use Exportable;

    public function headings()
    {
        return ['ID', 'Name', 'Email'];
    }
}
