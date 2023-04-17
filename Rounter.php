<?php

namespace app\core;

use app\controllers\SiteController;


class Rounter
{

  public Request $request;
  public LogException $err_log;
  public array $rountes = [];
  public array $rountes_chck = [];


  public function __construct()
  {
    $new_request = new Request();
    $this->request = $new_request;
    $this->err_log = new LogException();
  }

  public function only($key, $middleware)
  {
    $this->rountes_chck[$key][$middleware] = $middleware;

    return $this;
  }

  public function get($path, $callback, $middleware = null)
  {
    if ($middleware != null) {
      $this->only($path, $middleware);
    }
    $this->rountes['get'][$path] =  $callback;
  }

  public function post($path, $callback)
  {
    $this->rountes['post'][$path] =  $callback;
  }


  public function reslove()
  {
    $part =  $this->request->getPart();
    $method = $this->request->getMethod();

    $request = substr($part, 1);
    $requestSegments = explode('/', $request);

    $route1 = isset($requestSegments[0]) ? $requestSegments[0] : '';
    $route2 = isset($requestSegments[1]) ? true : false;

    if ($route2) {
      header("location: ../home");
      return;
    }

    $callback = $this->rountes[$method][$part] ?? false;

    if ($callback == false) {
      Application::$app->response->setStatusCode($this->err_log->status);
      return $this->renderContent_notfound($callback, ["status" => $this->err_log->status, "message" => $this->err_log->message]);
    }

    // old call view

    if (is_string($callback)) {
      return $this->renderView($callback);
    }


    if (is_array($callback)) {
      $callback[0] = new $callback[0]();
    }



    return call_user_func($callback, $this->request);

    // old logic
    // if (is_callable($callback)) {
    //   return call_user_func($callback, $this->request);
    // } else {
    //   Application::$app->response->setStatusCode(404);
    //   return $this->renderContent_notfound($callback);
    // }
  }


  public function renderView($view, $param = [])
  {
    $layout = $this->layoutFunc();
    $view_lead = $this->readOnlyView($view, $param);
    return str_replace('{{content}}', $view_lead, $layout);
  }

  public function renderView_post($view, $param = [])
  {
    $view_lead = $this->readOnlyView($view, $param);
    return   $view_lead;
  }


  public function renderContent_notfound($view, array $param)
  {
    $layout = $this->layoutFunc();
    $view_lead = $this->readOnlynot($param);

    return  str_replace('{{content}}', $view_lead, $layout);
  }



  protected function layoutFunc()
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/layout/content.php";
    return ob_get_clean();
  }


  protected function readOnlyView($view, $param)
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/include_class.php";
    include_once Application::$ROOT_DIR . "/views/$view.php";
    return ob_get_clean();
  }


  protected function readOnlynot(array $code_detail)
  {
    ob_start();
    include_once Application::$ROOT_DIR . "/views/not_found.php";
    return ob_get_clean();
  }
}
