<?php

namespace Patiphon\PhpCoreMvc;

class Application
{

  public static string $ROOT_DIR;
  public static Application $app;
  public Response $response;
  public Rounter  $rounter;
  public Request  $request;
  public Database  $database;
  public View  $view;
  public Session  $session;
  static  public $arr = [];

  public function __construct($rootPart, array $config)
  {
    self::$ROOT_DIR  = $rootPart;
    self::$app  = $this;
    $this->rounter = new Rounter();
    $this->response = new Response();
    $this->request = new Request();
    $this->view = new View();
    $this->session = new Session();
    $this->database = new Database($config);
  }

  public function run()
  {
    // return ค่าจะไม่ออกมา
    echo "<pre>";
    // print_r($this->rounter->rountes);
    echo "</pre>";
    echo $this->rounter->reslove();
  }
}
