<?php
namespace Wandu\Http\Controllers;

use Wandu\Http\Exception\HttpBadRequestException;

class HelloWorld
{
    public function index()
    {
        return "hello world";
    }
}
