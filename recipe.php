<?php

if (!class_exists('EkAndreas\DockerBedrock\Helpers')) {
    include_once 'src/DockerBedrock/Helpers.php';
}

use EkAndreas\DockerBedrock\Helpers;

$dir = Helpers::getProjectDir();
require_once $dir.'/vendor/autoload.php';

task('docker:start', function () {
    Helpers::start();
}, 999);

task('docker:up', function () {
    Helpers::start();
}, 999);

task('docker:stop', function () {
    Helpers::stop();
});

task('docker:halt', function () {
    Helpers::stop();
});

task('docker:kill', function () {
    Helpers::kill();
});

task('wpinit', function () {
    $server = env('server');
    runLocally("wp core install --url='{$server['name']}' --title='{$server['name']}' --admin_user='admin' --admin_password='admin' --admin_email='admin@{$server['name']}'");
});
