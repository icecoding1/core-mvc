<?php

namespace Patiphon\PhpCoreMvc;

class Request
{


  public array $routeParams = [];

  public function getPart()
  {
    $part = $_SERVER['REQUEST_URI'];
    $position = strpos($part, '?');

    if ($position === false) {
      return $part;
    }

    $part = substr($part, 0, $position);
    return $part;

    exit;
  }

  public function getPartAll()
  {
    $part = $_SERVER['REQUEST_URI'];
    return $part;
  }

  public function getMethod()
  {
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  public function isGet()
  {
    return $this->getMethod() === 'get';
  }

  public function isPost()
  {
    return $this->getMethod() === 'post';
  }

  public function getBody()
  {
    $body = [];
    if ($this->getMethod() === 'get') {
      foreach ($_GET as $key => $value) {
        $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }
    } else if ($this->getMethod() === 'post') {
      foreach ($_POST as $key => $value) {
        $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }
    }
    return $body;
  }

  public function setRouteParams($params)
  {
    $this->routeParams = $params;
    return $this;
  }

  public function getRouteParams()
  {
    return $this->routeParams;
  }

  public function getRouteParam($param, $default = null)
  {
    return $this->routeParams[$param] ?? $default;
  }
}
