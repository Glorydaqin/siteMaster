<?php
namespace Facebook\WebDriver;

require 'vendor/autoload.php';


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

try{

    $host = 'http://127.0.0.1:4444/wd/hub/'; // this is the default
//    $host = 'http://hub:4444/wd/hub/'; // this is the default
    $capabilities = DesiredCapabilities::chrome();
    $driver = RemoteWebDriver::create($host, $capabilities, 5000);

    $driver->get('https://www.semrush.com/users/login.html');
//    $driver->get('https://www.google.com');
    $cookie = $driver->manage()->getCookies();
    d($cookie);
    d($driver->getPageSource());
    $driver->quit();

}catch (\Exception $exception){

    dd($exception);
}

