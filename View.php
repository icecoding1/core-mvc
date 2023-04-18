<?php

namespace Patiphon\PhpCoreMvc;

class View
{
  public function layoutFunc()
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/layout/content.php";
    return ob_get_clean();
  }


  public function readOnlyView($view, $param)
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/$view.php";
    return ob_get_clean();
  }


  public function readOnlynot(array $code_detail)
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/not_found.php";
    return ob_get_clean();
  }
}
