<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:57 PM
 */

namespace RulesRegulations\Admin;



class Create extends Edit {

    /**
     * override this to setup anything that needs to be done before
     * @param $action null|string the action the is attempting if any
     */
    public function processRequest($action =  null) {
        $this->action = "create";

        if($action)
            $this->handlePost();

    }
}