<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    const SUCCESS_STATUS = 'success';
    const ERROR_STATUS = 'fail';
    protected $apiStructure = [
        'status',
        'message',
        'data' => []
    ];
}
