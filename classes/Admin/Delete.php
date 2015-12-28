<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/17/15
 * Time: 2:18 AM
 */

namespace CoillapsingContent\Admin;


use CoillapsingContent\Model\Rule;
use WordWrap\Admin\TaskController;

class Delete extends TaskController {

    /**
     * override this to setup anything that needs to be done before
     * @param $action string the action the user is trying to complete
     */
    public function processRequest($action = null) {
        $id = isset($_GET["id"]) ? $_GET["id"] : false;

        if(!$id)
            header("Location: admin.php?page=rules_regulations&task=view_rules");

        $rule = Rule::find_one($id);

        $rule->delete();

        if($rule->getParent())
            header("Location: admin.php?page=rules_regulations&task=edit_rule&id=" . $rule->getParent()->id);
        else
            header("Location: admin.php?page=rules_regulations&task=view_rules");
    }

    /**
     * override to render the main page
     */
    protected function renderMainContent() { }

    /**
     * override to render the main page
     */
    protected function renderSidebarContent() { }
}