<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Switch psql db schema
Artisan::command('schema:switch {schemaName?}', function(Request $request, $schemaName = 'main') {
    createSchema($schemaName);
    switchSchema($request, $schemaName);
    
    echo "- - - - - - - - - - - - - -\n";
    echo " DB Schema switched to * $schemaName * \n";
    echo "- - - - - - - - - - - - - -";
})->purpose('Switch psql db schema');