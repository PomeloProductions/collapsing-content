<?php
namespace CollapsingContent\Admin;
use CollapsingContent\Admin\View\EntriesContainer;
use CollapsingContent\Model\Entry;
use WordWrap\Admin\TaskController;

/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:47 PM
 */
class ViewEntries extends TaskController {

    /**
     * @var Entry[] all top level entries that exist
     */
    private $topLevelEntries = [];

    /**
     * override this to setup anything that needs to be done before
     * @param $action null|string the action that is being processed
     */
    public function processRequest($action = null) {
        $this->topLevelEntries = Entry::fetchAllParents();
    }

    /**
     * override to render the main page
     */
    public function renderMainContent() {

        $entriesContainer = new EntriesContainer($this->lifeCycle, $this->topLevelEntries);

        return $entriesContainer->export();
    }

    /**
     * override to render the main page
     */
    public function renderSidebarContent() {
        // TODO: Implement renderSidebarContent() method.
    }


}