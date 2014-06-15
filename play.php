<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$loader = require_once __DIR__.'/app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);

/** The code you are testing should be here */
use Yoda\EventBundle\Entity\Event;

$entityManager = $container->get('doctrine')->getManager();
$event = $entityManager->getRepository('EventBundle:Event')->find(1);
print_r($event->getTime());