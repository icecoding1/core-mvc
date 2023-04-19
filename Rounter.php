<?php

namespace Patiphon\PhpCoreMvc;

use Patiphon\PhpCoreMvc\SiteController;
use Patiphon\PhpCoreMvc\exception\NotFoundException;



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

  public function check_middle($key, $name, $middleware)
  {
    $this->rountes_chck[$key][$name] = $middleware;

    return $this;
  }

  public function call_nameMiddle($key, $middleware)
  {
    $name_middle = isset($this->rountes_chck[$key][$middleware]) ? $this->rountes_chck[$key][$middleware] : false;
    return $name_middle;
  }

  public function call_nameMiddleGetcheck($method, $middleware)
  {
    $detail_middle = isset($this->rountes[$method][$middleware]) ? $this->rountes[$method][$middleware] : false;
    return $detail_middle;
  }

  public function get(string $path, $callback, $middleware = null)
  {
    if ($middleware != null) {
      $path_rount = substr($path, 1);
      $path_rount = explode('/', $path_rount);
      $path_rount = $path_rount[0];
      $this->check_middle($path, $path_rount, $middleware);
    }
    $this->rountes['get'][$path] =  $callback;
  }

  public function post(string $path, $callback)
  {
    $this->rountes['post'][$path] =  $callback;
  }

  public function getRouteMap($method): array
  {
    return $this->rountes[$method] ?? [];
  }

  public function getDetailRouteMap()
  {
  }

  public function getCallback($recieve = 1)
  {
    $method = $this->request->getMethod();
    $url = $this->request->getPart();
    // Trim slashes
    $url = trim($url, '/');

    // Get all routes for current request method
    $routes = $this->getRouteMap($method);

    $routeParams = false;

    // Start iterating registed routes
    foreach ($routes as $route => $callback) {

      $path_all = $route;
      $path_rount = substr($route, 1);
      $path_rount = explode('/', $path_rount);
      $path_rount = $path_rount[0];

      // Trim slashes
      $route = trim($route, '/');
      $routeNames = [];

      if (!$route) {
        continue;
      }

      // Find all route names from route and save in $routeNames example home/{id} to id
      if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
        $routeNames = $matches[1];
      }

      // Convert route name into regex pattern by home/{id} = @^home(\w+)$@
      $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn ($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

      // Test and match current route against $routeRegex
      if (preg_match_all($routeRegex, $url, $valueMatches)) {
        $values = [];
        for ($i = 1; $i < count($valueMatches); $i++) {
          $values[] = $valueMatches[$i][0];
        }
        // create array with to key => value
        $routeParams = array_combine($routeNames, $values);

        // save param
        $this->request->setRouteParams($routeParams);
        if ($recieve == 1) {
          return $callback;
        } else if ($recieve == 2) {
          $call_nameMiddle = $this->call_nameMiddle($path_all, $path_rount);
          return $call_nameMiddle;
        }
      }
    }

    return false;
  }


  public function reslove()
  {
    $part =  $this->request->getPart();
    $method = $this->request->getMethod();

    $path_rount = substr($part, 1);
    $path_rount = explode('/', $path_rount);
    $path_rount = $path_rount[0];
    $path_call = '/' . $path_rount;

    // for call middleware
    $call_nameMiddle = $this->call_nameMiddle($part, $path_rount);


    $callback = !empty($this->rountes[$method][$part]) ? $this->rountes[$method][$part] : false;
    if (!$callback) {
      $callback =  $this->getCallback();
      $call_nameMiddle =  $this->getCallback(2);

      if ($callback == false) {
        Application::$app->response->setStatusCode($this->err_log->status);
        return $this->renderContent_notfound('not_found', ["status" => $this->err_log->status, "message" => $this->err_log->message]);
      }
    }

    if ($call_nameMiddle != false) {
      $callback_middle = [Middlewares::class, $call_nameMiddle];
      $callback_middle[0] = new $callback_middle[0]();
      call_user_func($callback_middle);
    }

    if (is_string($callback)) {
      return $this->renderView($callback);
    }


    if (is_array($callback)) {
      $callback[0] = new $callback[0]();
    }

    return call_user_func($callback, $this->request);
  }


  public function renderView($view, $param = [])
  {
    $layout = Application::$app->view->layoutFunc();
    $view_lead = Application::$app->view->readOnlyView($view, $param);
    return str_replace('{{content}}', $view_lead, $layout);
  }

  public function renderView_post($view, $param = [])
  {
    $view_lead = Application::$app->view->readOnlyView($view, $param);
    return   $view_lead;
  }

  public function renderView_mana($view, $param = [])
  {
    $layout = Application::$app->view->layoutFunc('management');
    $view_lead = Application::$app->view->readOnlyView($view, $param);
    return str_replace('{{content}}', $view_lead, $layout);
  }


  public function renderContent_notfound($view, array $param)
  {
    $layout = Application::$app->view->layoutFunc();
    $view_lead = Application::$app->view->readOnlynot($param);

    return  str_replace('{{content}}', $view_lead, $layout);
  }
}
