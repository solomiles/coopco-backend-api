<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

    echo "    Copying OAuth2 tables to $schemaName schema . . .\n";

    $oauth2Tables = ['oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients', 'oauth_refresh_tokens'];

    // Copy OAuth2 tables to switched schema
    foreach($oauth2Tables as $table) {
        DB::unprepared('SELECT * INTO '.$table.' FROM public.'.$table);
        echo "\n    Copied $table";
    }

    echo "\n    Copied all OAuth2 tables to $schemaName schema . . .\n";
    
    // Migrate tables in schema folder
    echo "\n    Migrating $schemaName schema . . .\n";

    try {
        Artisan::call('migrate');

        $files = array_diff(scandir(base_path('database/migrations/'.$schemaName)), array('.', '..'));

        foreach($files as $file) {
            echo "\n    $file";
        }

        echo "\n\n    Migration complete for $schemaName schema\n";
    } catch (\Throwable $th) {
        Log::error($th);
        echo "    Could not migrate to $schemaName schema\n";
    }
});

// Seed db classes in a specified folder for a specified schema
Artisan::command('schema:seed {schemaName}', function(Request $request, $schemaName) {
    createSchema($schemaName);
    switchSchema($request, $schemaName);
    
    echo "    Seeding $schemaName schema . . .\n";

    try {
        $files = array_diff(scandir(base_path('database/seeders/'.$schemaName)), array('.', '..'));
        
        foreach($files as $file) {
            $className = str_replace('.php', '', $file);

            Artisan::call('db:seed', ['--class' => 'Database\Seeders\\'.$schemaName.'\\'.$className]);

            echo "\n    $file seeded successfully";
        }

        echo "\n\n    Seeding complete for $schemaName schema\n";
    } catch (\Throwable $th) {
        Log::error($th);
        echo "    Could not seed $schemaName schema\n";
    }
});