<?php

namespace Patiphon\PhpCoreMvc;

class Session
{

  public function __construct()
  {
    session_start();
  }


  public function set_session($name, $value)
  {
    return $_SESSION[$name] = $value;
  }

  public function get_session($name)
  {
    return $_SESSION[$name];
  }

  public function del_session($name)
  {
    unset($_SESSION[$name]);
    return;
  }


  public function del_sessionAll()
  {
    session_destroy();
    return;
  }
}
