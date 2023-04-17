<?php

namespace Patiphon\PhpCoreMvc;

use \Exception;

class LogException extends Exception
{
  public  $message;
  public  $status;

  public function __construct()
  {
    $this->message = "Not Found Page";
    $this->status = 404;
  }
}
