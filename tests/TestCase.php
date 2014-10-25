<?php

namespace Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../src/app.php';
    }
}
