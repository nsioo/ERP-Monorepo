<?php

class Default_IndexController extends Zend_Controller_Action {

    public function indexAction() {
        $this->view->noHeader = true;
    }

}

