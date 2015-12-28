<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/17/15
 * Time: 2:18 AM
 */

namespace CollapsingContent\Admin;


use CollapsingContent\Model\Entry;
use WordWrap\Admin\TaskController;

class Delete extends TaskController {

    /**
     * override this to setup anything that needs to be done before
     * @param $action string the action the user is trying to complete
     */
    public function processRequest($action = null) {
        $id = isset($_GET["id"]) ? $_GET["id"] : false;

        if(!$id)
            header("Location: admin.php?page=collapsing_content&task=view_entries");

        $entry = Entry::find_one($id);

        $entry->delete();

        if($entry->getParent())
            header("Location: admin.php?page=collapsing_content&task=edit_entry&id=" . $entry->getParent()->id);
        else
            header("Location: admin.php?page=collapsing_content&task=view_entries");
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