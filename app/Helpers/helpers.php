<?php

use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Switch psql DB schema
 *
 * @param $request - HTTP Request object
 * @param string $schema - (Optional) Schema for application to switch to
 *
 * @return void
 */
function switchSchema($request, $schema = null)
{
    $schema = $schema ? $schema : $request->getHttpHost();

    config(['database.connections.pgsql.search_path' => $schema]);
    DB::purge();
}

/**
 * Switch email credentials
 *
 * @param array $emailConfig
 *
 * @return void
 */
function setEmailCredentials($emailConfig)
{
    config(['mail.mailers.' . $emailConfig['mailer'] => [
        'transport' => $emailConfig['mailer'],
        'host' => $emailConfig['host'],
        'port' => $emailConfig['port'],
        'encryption' => $emailConfig['encryption'],
        'username' => $emailConfig['username'],
        'password' => $emailConfig['password'],

    ]]);

    config(['mail.from' => [
        'address' => $emailConfig['from_address'],
        'name' => $emailConfig['from_name'],
    ]]);
}

/**
 * Random password generator
 *
 * @return string
 */
function randomPassword()
{
    $alphaNumChar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&()<,>?';
    return substr(str_shuffle($alphaNumChar), 0, 8);
}

/**
 * Global variable accessor helper
 * @param string $constant - Constant key in config/global.php
 *
 * @return string
 */
function g($constant)
{
    return config('global.' . $constant);
}

/**
 * Create schema if it doesn't exist
 * @param string $schemaName
 *
 * @return void
 */
function createSchema($schemaName)
{
    DB::unprepared('CREATE SCHEMA IF NOT EXISTS ' . $schemaName);
}

/**
 * Migrate new schema from public migration files
 * @param string $schemaName
 * 
 * @return void
 */
function migrateNewSchema($schemaName)
{
    switchSchema([], $schemaName);
    Artisan::call('migrate');

    Artisan::call('db:seed', ['--class' => 'Database\Seeders\\CountriesSeeder']);
    Artisan::call('db:seed', ['--class' => 'Database\Seeders\\PlansSeeder']);
    Artisan::call('db:seed', ['--class' => 'Database\Seeders\\AdminSeeder']);

    DB::unprepared('INSERT INTO oauth_personal_access_clients SELECT * FROM public.oauth_personal_access_clients');
    DB::unprepared('INSERT INTO oauth_clients SELECT * FROM public.oauth_clients');
}

/**
 * Convert base64 encoded file to actual file
 * @param string $base64File
 *
 * @return Illuminate\Http\UploadedFile
 */
function base64ToFile($base64File)
{
    // decode the base64 file
    $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));

    // save it to temporary dir first.
    $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
    file_put_contents($tmpFilePath, $fileData);

    // this just to help us get file info.
    $tmpFile = new File($tmpFilePath);

    $file = new UploadedFile(
        $tmpFile->getPathname(),
        $tmpFile->getFilename(),
        $tmpFile->getMimeType(),
        0,
        true // Mark it as test, since the file isn't from real HTTP POST.
    );

    return $file;
}
