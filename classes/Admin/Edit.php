<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:56 PM
 */

namespace RulesRegulations\Admin;


use RulesRegulations\Admin\View\RulesContainer;
use RulesRegulations\Model\Rule;
use WordWrap\Admin\TaskController;
use WordWrap\View\Editor;
use WordWrap\View\View;
use WordWrap\View\ViewCollection;

class Edit extends TaskController{

    /**
     * @var Rule the rule that is currently being edited
     */
    private $rule;

    /**
     * override this to setup anything that needs to be done before
     */
    public function processRequest() {
        if(!isset($_GET["id"]) || $_GET["id"] == "")
            wp_redirect("admin.php?page=rules_regulations&task=view");

        $this->rule = Rule::find_one($_GET["id"]);

        if(isset($_POST)) {
            $this->handlePost();
        }

    }

    /**
     * By default this will attempt to edit this post
     */
    protected function handlePost() {

    }

    /**
     * override to render the main page
     */
    public function renderMainContent() {

        $view = new View($this->lifeCycle, "admin/rule_edit");

        $view->setTemplateVar("task", $this->task->getSlug());

        $title = "";
        $id = "";
        $aboveRules = "";
        $belowRules = "";

        $childrenRules = [];

        if(isset($this->rule)) {
            $title = $this->rule->title;
            $id = "&id=" . $this->rule->id;
            $aboveRules = $this->rule->top_content;
            $belowRules = $this->rule->bottom_content;

            $childrenRules = $this->rule->getChildren();
        }

        if(isset($_POST["title"]))
            $title = $_POST["title"];
        if(isset($_POST["above_rules"]))
            $aboveRules = $_POST["above_rules"];
        if(isset($_POST["below_rules"]))
            $belowRules = $_POST["below_rules"];

        $view->setTemplateVar("title", $title);
        $view->setTemplateVar("id", $id);

        $aboveEditor = new Editor($this->lifeCycle, "above_rules", $aboveRules, "Above Children Rules");
        $view->setTemplateVar("above_rules", $aboveEditor->export());

        $belowEditor = new Editor($this->lifeCycle, "below_rules", $belowRules, "Below Children Rules");
        $view->setTemplateVar("below_rules", $belowEditor->export());

        $rulesContainer = new RulesContainer($this->lifeCycle, $childrenRules, $this->rule);
        $view->setTemplateVar("rules", $rulesContainer->export());

        return $view->export();

    }

    /**
     * override to render the main page
     */
    public function renderSidebarContent() {
        // TODO: Implement renderSidebarContent() method.
    }

    /**
     * @return string sets the custom task name for editing this task
     */
    public function getTaskName() {
        $taskName = parent::getTaskName();

        if(isset($this->rule) && $this->rule->id)
            $taskName .= " #" . $this->rule->id;

        return $taskName;
    }
}