<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:56 PM
 */

namespace CoillapsingContent\Admin;


use CoillapsingContent\Admin\View\RulesContainer;
use CoillapsingContent\Model\Rule;
use WordWrap\Admin\TaskController;
use WordWrap\Assets\View\Editor;
use WordWrap\Assets\View\View;

class Edit extends TaskController{

    /**
     * @var Rule the rule that is currently being edited
     */
    private $rule;

    /**
     * @var string the action the user is attempting to carry out
     */
    protected $action = "edit";

    /**
     * override this to setup anything that needs to be done before
     * @param $action null|string the action the is attempting if any
     */
    public function processRequest($action =  null) {
        if(!isset($_GET["id"]) || $_GET["id"] == "")
            wp_redirect("admin.php?page=rules_regulations&task=view");

        $this->rule = Rule::find_one($_GET["id"]);

        if($action)
            $this->handlePost();

    }

    /**
     * By default this will attempt to edit this post
     */
    protected function handlePost() {

        if(!$this->rule)
            $this->rule = Rule::create([]);

        if (isset($_POST["title"]))
            $this->rule->title = $_POST["title"];
        if (isset($_POST["above_rules"]))
            $this->rule->top_content = $_POST["above_rules"];
        if (isset($_POST["below_rules"]))
            $this->rule->bottom_content = $_POST["below_rules"];
        if (isset($_POST["parent"]) && $_POST["parent"] != "")
            $this->rule->parent_id = $_POST["parent"];

        $this->rule->save();

        if ($this->rule->getParent())
            header("Location: admin.php?page=rules_regulations&task=edit_rule&id=" . $this->rule->getParent()->id);
        else
            header("Location: admin.php?page=rules_regulations&task=view_rules");
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

        $parent = null;

        if(isset($this->rule)) {
            $title = $this->rule->title;
            $id = "&id=" . $this->rule->id;
            $aboveRules = $this->rule->top_content;
            $belowRules = $this->rule->bottom_content;

            $childrenRules = $this->rule->getChildren();

            if($this->rule->getParent())
                $parent = $this->rule->getParent()->id;
        }

        if(isset($_POST["title"]))
            $title = $_POST["title"];
        if(isset($_POST["above_rules"]))
            $aboveRules = $_POST["above_rules"];
        if(isset($_POST["below_rules"]))
            $belowRules = $_POST["below_rules"];

        if(isset($_GET["parent_id"]) && $_GET["parent_id"])
            $parent = $_GET["parent_id"];

        $view->setTemplateVar("title", $title);
        $view->setTemplateVar("id", $id);

        $aboveEditor = new Editor($this->lifeCycle, "above_rules", $aboveRules, "Above Children Rules");
        $aboveEditor->setHeight(300);
        $view->setTemplateVar("above_rules", $aboveEditor->export());

        $belowEditor = new Editor($this->lifeCycle, "below_rules", $belowRules, "Below Children Rules");
        $belowEditor->setHeight(300);
        $view->setTemplateVar("below_rules", $belowEditor->export());

        $rulesContainer = new RulesContainer($this->lifeCycle, $childrenRules, $this->rule);
        $view->setTemplateVar("rules", $rulesContainer->export());

        $view->setTemplateVar("action", $this->action);
        if($parent)
            $view->setTemplateVar("parent", $parent);

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