<?php

namespace app\core;


class Controller
{
  public function render($view, $params = [])
  {
    return Application::$app->rounter->renderView($view, $params);
  }

  public function render_post($view, $params = [])
  {
    return Application::$app->rounter->renderView_post($view, $params);
  }
}
