<?php
namespace controllers;
class AppController {
    private mixed $request;

    public function __construct(){
        $this->request = $_SERVER['REQUEST_METHOD'];
    }

    protected function isGet():bool
    {
        return $this->request === 'GET';
    }
    protected function isPost():bool
    {
        return $this->request === 'POST';
    }

    protected function getContentType(){
        return isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }

   protected function render(string $template = "", array $variables = []) {
      $templatePath = "public/views/".$template.".php";
      $output = "File not found: ".$templatePath;

      if(file_exists($templatePath)) {
        extract($variables);
        ob_start();
        include $templatePath;
        $output = ob_get_clean();
      }

      print $output;
      return null;
   }
}