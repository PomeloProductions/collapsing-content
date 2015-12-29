<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/12/15
 * Time: 12:56 PM
 */

namespace CollapsingContent\Admin;


use CollapsingContent\Admin\View\EntriesContainer;
use CollapsingContent\Model\Entry;
use WordWrap\Admin\TaskController;
use WordWrap\Assets\View\Editor;
use WordWrap\Assets\View\View;
use WP_Query;

class Edit extends TaskController{

    /**
     * @var Entry the entry that is currently being edited
     */
    private $entry;

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
            wp_redirect("admin.php?page=collapsing_content&task=view");

        $this->entry = Entry::find_one($_GET["id"]);

        if($action)
            $this->handlePost();

    }

    /**
     * By default this will attempt to edit this post
     */
    protected function handlePost() {

        if(!$this->entry)
            $this->entry = Entry::create([]);

        if (isset($_POST["title"]))
            $this->entry->title = $_POST["title"];
        if (isset($_POST["above_entries"]))
            $this->entry->top_content = $_POST["above_entries"];
        if (isset($_POST["below_entries"]))
            $this->entry->bottom_content = $_POST["below_entries"];
        if (isset($_POST["template"]))
            $this->entry->setTemplate($_POST["template"]);
        if (isset($_POST["parent"]) && $_POST["parent"] != "")
            $this->entry->parent_id = $_POST["parent"];

        if (isset($_POST["using_post"]))
            $this->entry->using_post = $_POST["using_post"] == "on";
        else
            $this->entry->using_post = false;

        if (isset($_POST["post_id"]))
            $this->entry->post_id = $_POST["post_id"];

        $this->entry->save();

        if ($this->entry->getParent())
            header("Location: admin.php?page=collapsing_content&task=edit_entry&id=" . $this->entry->getParent()->id);
        else
            header("Location: admin.php?page=collapsing_content&task=view_entries");
    }

    /**
     * override to render the main page
     */
    public function renderMainContent() {

        $view = new View($this->lifeCycle, "admin/entry_edit");

        $view->setTemplateVar("task", $this->task->getSlug());

        $view->setTemplateVar("available_templates", $this->renderAvailableTemplates());
        $view->setTemplateVar("available_posts", $this->renderAvailablePosts());

        $title = "";
        $id = "";
        $aboveEntries = "";
        $belowEntries = "";
        $usingPost = false;
        $postSelectDisplay = "none";
        $contentEditingDisplay = "block";
        $usingPostDisplay = "block";

        $childrenEntries = [];

        $parent = null;

        if(isset($this->entry)) {
            $title = $this->entry->title;
            $id = "&id=" . $this->entry->id;
            $aboveEntries = $this->entry->top_content;
            $belowEntries = $this->entry->bottom_content;
            $usingPost = $this->entry->using_post;

            $childrenEntries = $this->entry->getChildren();

            if($this->entry->getParent())
                $parent = $this->entry->getParent()->id;
        }

        if(isset($_POST["title"]))
            $title = $_POST["title"];
        if(isset($_POST["above_entries"]))
            $aboveEntries = $_POST["above_entries"];
        if(isset($_POST["below_entries"]))
            $belowEntries = $_POST["below_entries"];
        if (isset($_POST["using_post"]))
            $usingPost = $_POST["using_post"] == "on";

        if ($usingPost) {
            $postSelectDisplay = "block";
            $contentEditingDisplay = "none";
        }

        if (isset($this->entry) && $this->entry->template == "nested") {
            $usingPostDisplay = "none";
            $postSelectDisplay = "none";
            $contentEditingDisplay = "none";
        }

        if(isset($_GET["parent_id"]) && $_GET["parent_id"])
            $parent = $_GET["parent_id"];

        $view->setTemplateVar("using_post", $usingPost ? "checked" : "");

        $view->setTemplateVar("post_select_display", $postSelectDisplay);
        $view->setTemplateVar("content_editing_visibility", $contentEditingDisplay);
        $view->setTemplateVar("using_post_visibility", $usingPostDisplay);

        $view->setTemplateVar("title", $title);
        $view->setTemplateVar("id", $id);

        $aboveEditor = new Editor($this->lifeCycle, "above_entries", $aboveEntries, "Above Children Entries");
        $aboveEditor->setHeight(200);
        $view->setTemplateVar("above_entries", $aboveEditor->export());

        $belowEditor = new Editor($this->lifeCycle, "below_entries", $belowEntries, "Below Children Entries");
        $belowEditor->setHeight(200);
        $view->setTemplateVar("below_entries", $belowEditor->export());

        $entriesContainer = new EntriesContainer($this->lifeCycle, $childrenEntries, $this->entry);
        $view->setTemplateVar("entries", $entriesContainer->export());

        $view->setTemplateVar("action", $this->action);
        if($parent)
            $view->setTemplateVar("parent", $parent);

        return $view->export();

    }

    /**
     * creates the html for the select options of available templates
     * @return string options available
     */
    private function renderAvailableTemplates() {

        $availableTemplates = Entry::$availableTemplates;
        $selectedTemplate = "simple";
        $optionsContent = "";

        if (isset($this->entry))
            $selectedTemplate = $this->entry->getTemplate();
        if (isset($_POST["template"]))
            $selectedTemplate = $_POST["template"];

        if (!in_array($selectedTemplate, $availableTemplates))
            $selectedTemplate = "simple";

        foreach ($availableTemplates as $template) {

            $optionsContent.= "<option ". ($template == $selectedTemplate ? "selected" : "")
                ." value='" . $template ."'>" . $template . "</option>";
        }

        return $optionsContent;
    }

    /**
     * creates the html for the select options of available templates
     * @return string options available
     */
    private function renderAvailablePosts() {

        $args = [
            'post_type' => ['post', 'page'],
            'nopaging' => false,
            'orderby' => 'title',
            'order' => 'ASC',
        ];

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $availablePosts = [];

        foreach ($posts as $post) {
            $availablePosts[$post->ID] = $post->post_title;
        }

        $selectedPost = 0;
        $optionsContent = "";

        if (isset($this->entry))
            $selectedPost = $this->entry->post_id;
        if (isset($_POST["post_id"]))
            $selectedPost = $_POST["post_id"];


        foreach ($availablePosts as $id => $title) {

            $optionsContent.= "<option ". ($id == $selectedPost ? "selected" : "")
                ." value='" . $id ."'>" . $title . "</option>";
        }

        return $optionsContent;
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

        if(isset($this->entry) && $this->entry->id)
            $taskName .= " #" . $this->entry->id;

        return $taskName;
    }
}