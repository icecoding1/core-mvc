<?php

/**
 * User: TheCodeholic
 * Date: 7/25/2020
 * Time: 11:35 AM
 */

namespace Patiphon\PhpCoreMvc\exception;


use Patiphon\PhpCoreMvc\Application;

/**
 * Class ForbiddenException
 *
 * @author  Zura Sekhniashvili <zurasekhniashvili@gmail.com>
 * @package Patiphon\PhpCoreMvc\exception
 */
class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
}
