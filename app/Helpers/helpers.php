<?php

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
