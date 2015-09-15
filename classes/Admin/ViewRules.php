<?php
namespace RulesRegulations\Admin;
use RulesRegulations\Admin\View\RulesContainer;
use RulesRegulations\Model\Rule;
use WordWrap\Admin\TaskController;

/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:47 PM
 */
class ViewRules extends TaskController {

    /**
     * @var Rule[] all top level rules that exist
     */
    private $topLevelRules = [];

    /**
     * override this to setup anything that needs to be done before
     */
    public function processRequest() {
        $this->topLevelRules = Rule::fetchAllParents();
    }

    /**
     * override to render the main page
     */
    public function renderMainContent() {

        $rulesContainer = new RulesContainer($this->lifeCycle, $this->topLevelRules);

        return $rulesContainer->export();
    }

    /**
     * override to render the main page
     */
    public function renderSidebarContent() {
        // TODO: Implement renderSidebarContent() method.
    }


}