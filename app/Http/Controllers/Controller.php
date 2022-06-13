<?php

namespace App\Http\Controllers;

use App\Traits\CsvTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, CsvTrait;

    public function index(Request $request)
    {
        return $this->setCSVStructure(['Name', 'Email', 'Number'], 'wisdom-cooperative');
    }
}
