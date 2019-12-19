<?php

require 'vendor/autoload.php';

use Nesk\Puphpeteer\Puppeteer;

$puppeteer = new Puppeteer;
$browser = $puppeteer->launch();

$page = $browser->newPage();
$page->goto('https://example.com');
$page->screenshot(['path' => 'example.png']);

$browser->close();