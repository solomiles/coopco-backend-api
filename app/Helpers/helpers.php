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