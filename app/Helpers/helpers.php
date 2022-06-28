<?php

use Illuminate\Support\Facades\DB;

/**
 * Swicth psql DB schema
 *
 * @param Illuminate\Http\Request $request - HTTP Request object
 * @param string $schema - (Optional) Schema for application to switch to
 *
 * @return void
 */
function switchSchema($request, $schema = null) {
	$schema = $schema ? $schema : $request->getHttpHost();

	config(['database.connections.pgsql.search_path' => $schema]);
    DB::purge();
}

/**
 * Swicth email credentials
 *
 * @param array $emailConfig
 *
 * @return void
 */
function setEmailCredentials($emailConfig) {
    config(['mail.mailers.'.$emailConfig['mailer'] => [
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
function randomPassword() {
    $alphaNumChar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&()<,>?';
    return substr(str_shuffle($alphaNumChar), 0, 8);
}

/**
 * Global variable accessor helper
 * @param string $constant - Constant key in config/global.php
 * 
 * @return string
 */
function g($constant) {
    return config('global.'.$constant);
}

/**
 * Create schema if it doesn't exist
 * @param string $schemaName
 * 
 * @return void
 */
function createSchema($schemaName) {
    DB::unprepared('CREATE SCHEMA IF NOT EXISTS '.$schemaName);
}