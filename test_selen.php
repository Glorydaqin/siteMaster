<?php
namespace Facebook\WebDriver;

require 'vendor/autoload.php';


use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

try{

    $host = 'http://192.168.136.121:4444/wd/hub/'; // this is the default
//    $host = 'http://192.168.10.138:4444/wd/hub/'; // this is the default
//    $host = 'http://selenium-hub:4444/wd/hub/'; // this is the default
    $capabilities = DesiredCapabilities::chrome();
    $options = new ChromeOptions();
//    $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36';
//    $options->addArguments(["user-agent={$useragent}"]);
    $options->addArguments(["--incognito"]);
    $options->addArguments(["--lang=en"]);
    $options->addArguments(["--disable-features=NetworkService"]);
    $options->addArguments(["--window-size=1920,1080"]);
    $options->addArguments(["headless"]);

    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

    $driver = RemoteWebDriver::create($host, $capabilities, 5000);

    $driver->get('https://www.semrush.com/users/login.html');
//    $driver->get('https://www.semrush.com');
//    $driver->get('https://www.baidu.com');
    $cookie = $driver->manage()->getCookies();
    d($cookie);
    d($driver->getPageSource());
    $driver->quit();

}catch (\Exception $exception){

    dd($exception);
}

