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
  static  public $arr = [];

  public function __construct($rootPart, array $config)
  {
    self::$ROOT_DIR  = $rootPart;
    self::$app  = $this;
    $this->rounter = new Rounter();
    $this->response = new Response();
    $this->request = new Request();
    $this->database = new Database($config);
  }

  public function run()
  {
    // return ค่าจะไม่ออกมา
    // for test 
    // print_r($this->rounter->rountes_chck);
    echo $this->rounter->reslove();
  }

  public function echo()
  {
    return 'Welcome To My Website';
  }

  public function echo_home($message)
  {
    return $message;
  }
}
