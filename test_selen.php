<?php
namespace Facebook\WebDriver;

require 'vendor/autoload.php';


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

$host = '192.168.2.103:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

try{

    $driver->get('https://www.google.com');
    $cookie = $driver->manage()->getCookies();
    d($cookie);
    d($driver->getPageSource());
    $driver->quit();


}catch (\Exception $exception){

    $driver->quit();
    dd($exception);
}
