<?php 

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

require_once 'vendor/autoload.php';

// create EventLoop
$loop = React\EventLoop\Factory::create();

// ReactPHP HTTP Server
$server = new \React\Http\Server([
    // Representation of an incoming, server-side HTTP request.
    function (ServerRequestInterface $request) {

    	$path = $request->getUri()->getPath();
	   	$method = $request->getMethod();
	   	$requestTarget = $request->getRequestTarget();
    	
        // Get data from Parsed in Body Fields
        $data = $request->getParsedBody();

        if ($path == '/') {

        	switch ($method) {
        		case 'GET':
        			$response = "This Using ReactPHP EventLoop and Http!";
        			// Create a Notifier
        			$notifier = NotifierFactory::create();

        			// Create your notification
        			$notification =
        			    (new Notification())
        			    ->setTitle('Notification From WebApp')
        			    ->setBody('This is cool right?')
        			    ->setIcon(__DIR__.'/../icon-success.png')
        			    // ->addOption('subtitle', 'This is a subtitle') // Only works on macOS (AppleScriptNotifier)
        			    // ->addOption('sound', 'Frog') // Only works on macOS (AppleScriptNotifier)
        			;

        			// Send it
        			$notifier->send($notification);
                    return new Response(200, ['Content-Type' => 'text/plain'],  $response);
        			break;
        		case 'POST':
        			// 
        			break;
        	}
        }
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8181', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();