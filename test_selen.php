<?php

namespace Facebook\WebDriver;

require 'vendor/autoload.php';

set_time_limit(0);

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

$user = "692860800@qq.com";
$password = "daqing";

try {


    $host = 'http://192.168.136.121:4444/wd/hub/'; // this is the default
//    $host = 'http://192.168.10.138:4444/wd/hub/'; // this is the default
//    $host = 'http://selenium-hub:4444/wd/hub/'; // this is the default
    $capabilities = DesiredCapabilities::chrome();
    $options = new ChromeOptions();
//    $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36';
//    $options->addArguments(["user-agent={$useragent}"]);
//    $options->addArguments(["--incognito"]);
    $options->addArguments(["--lang=en"]);
//    $options->addArguments(["--disable-features=NetworkService"]);
    $options->addArguments(["--window-size=1920,1080"]);
//    $options->addArguments(["headless"]);

    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

    $driver = RemoteWebDriver::create($host, $capabilities, 5000);

    $driver->get('https://www.semrush.com/users/login.html');
//    $driver->get('https://www.semrush.com');
//    $driver->get('https://www.baidu.com');
//    $cookie = $driver->manage()->getCookies();

    $driver->wait(20)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::className('sc-btn__inner')
        )
    );
    echo "get login finish" . PHP_EOL;
//    $driver->manage()->timeouts()->implicitlyWait(15);    //隐性设置15秒
    $driver->findElement(WebDriverBy::name('email'))->sendKeys($user);
    $driver->findElement(WebDriverBy::name('password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::className('sc-btn__inner'))->click();
    echo "click login button finish" . PHP_EOL;

    //由于下拉框是通过点击“搜索设置”按钮触发JS动态生成的DOM，所以这里使用Wait for new element to appear方式，不然直接调用查找元素会报错，说找不到元素
    $driver->wait(20)->until(
        WebDriverExpectedCondition::urlContains("/dashboard")
//        WebDriverExpectedCondition::visibilityOfElementLocated(
//            WebDriverBy::className('srf-line')
//        )
    );

    echo "login success" . PHP_EOL;
    $html = $driver->getPageSource();
    echo 'html strlen: ' . strlen($html) . PHP_EOL;

    if (stripos($html, 'Please confirm your email')) {
        echo "find Please confirm your email" . PHP_EOL;
    }

//    var_dump($driver->manage()->getCookies());
    $driver->quit();

} catch (\Exception $exception) {

    var_dump($exception);
}

