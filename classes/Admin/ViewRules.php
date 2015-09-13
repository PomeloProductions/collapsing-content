<?php
namespace RulesRegulations\Admin;
use RulesRegulations\Admin\View\RuleTR;
use RulesRegulations\Model\Rule;
use WordWrap\Admin\TaskController;
use WordWrap\View\View;
use WordWrap\View\ViewCollection;

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
    public function setup() {
        $this->topLevelRules = Rule::fetchAllParents();
    }

    /**
     * override to render the main page
     */
    public function renderMainContent() {
        $viewCollection = new ViewCollection($this->lifeCycle, "admin/view_container");

        foreach($this->topLevelRules as $rule) {
            $view = new RuleTR($this->lifeCycle, $rule->id, $rule->title);

            $viewCollection->addChildView("rules", $view);
        }

        return $viewCollection->export();
    }

    /**
     * override to render the main page
     */
    public function renderSidebarContent() {
        // TODO: Implement renderSidebarContent() method.
    }


}