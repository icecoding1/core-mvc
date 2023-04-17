<?php

namespace app\core;

class Response
{

  public  function setStatusCode(int $status)
  {
    return http_response_code($status);
  }
}
