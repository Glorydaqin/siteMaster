<?php
namespace Facebook\WebDriver;

require 'vendor/autoload.php';


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

$host = '192.168.10.138:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

try{

    $driver->get('https://www.semrush.com');
    $cookie = $driver->manage()->getCookies();
    d($cookie);
    d($driver->getTitle());
    $driver->quit();


}catch (\Exception $exception){

    $driver->quit();
    dd($exception);
}

