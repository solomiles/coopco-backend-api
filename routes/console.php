<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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

// Create psql db schema if it doesn't exist, then Switch to schema
Artisan::command('schema:switch {schemaName?}', function(Request $request, $schemaName = 'main') {
    createSchema($schemaName);
    switchSchema($request, $schemaName);
    
    echo "- - - - - - - - - - - - - -\n";
    echo " DB Schema switched to * $schemaName * \n";
    echo "- - - - - - - - - - - - - -";
})->purpose('Switch psql db schema');

// Migrate files in a specified folder for a specified schema
Artisan::command('schema:migrate {schemaName}', function(Request $request, $schemaName) {
    createSchema($schemaName);
    switchSchema($request, $schemaName);
    
    try {
        Artisan::call('migrate', ['--path' => '/database/migrations/'.$schemaName]);

        $files = array_diff(scandir(base_path('database/migrations/'.$schemaName)), array('.', '..'));

        foreach($files as $file) {
            echo "\n    $file";
        }

        echo "\n\n    Migration complete for * $schemaName *\n";
    } catch (\Throwable $th) {
        Log::error($th);
        echo "    Could not migrate to * $schemaName *\n";
    }
});