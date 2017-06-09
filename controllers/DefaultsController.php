<?php
class Defaults extends Controller {
    function indexAction(){
        $modelsDefault = $this->loadModel('Defaultss');
        $this->render('index');
    }
}