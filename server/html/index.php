<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';

if ( getenv( 'APP_DEBUG' ) ) {
		// Disable OpCache
		ini_set( 'opcache.enable', 0 );

		// WARNING: You should setup permissions the proper way!
		// REMOVE the following PHP line and read
		// https://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
		umask( 0000 );

		Debug::enable();
}

Request::setTrustedProxies(
	[ '127.0.0.1', gethostbyname( 'ingress.' ) ],
	Request::HEADER_X_FORWARDED_ALL
);

$kernel = new Kernel( getenv( 'APP_ENV' ), getenv( 'APP_DEBUG' ) );
$request = Request::createFromGlobals();

$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
