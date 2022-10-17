<?php
class View{
    public function render($page)
    {
        $content = file_get_contents(VIEW_FOLDER.$page.'.view');
        echo $content;
    }
}