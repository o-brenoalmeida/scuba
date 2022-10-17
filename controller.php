<?php
class Controller
{
    public $page;

    public function __construct($page)
    {
        $this->page = $page;
        $this->view = new View();
    }

    public function doRegister()
    {
        http_response_code(200);
        return $this->view->render($this->page);
    }
    
    public function doLogin()
    {
        http_response_code(200);
        return $this->view->render($this->page);
    }
    
    public function doNotFound()
    {
        http_response_code(404);
        return $this->view->render($this->page);
        
    }
}
