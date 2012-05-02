<?php

class Test extends CI_Controller {

    function index() {
        $em = $this->doctrine->em;
        var_dump($em);
        die('here');
    }

}
