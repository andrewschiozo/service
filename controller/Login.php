<?php
namespace controller;

use util\Request;
use util\Response;
use util\JWT;
use util\Config;


class Login extends Controller
{
	
	public function post()
	{
        echo '<pre>';
        print_r(Request::getData());
    }
		
}