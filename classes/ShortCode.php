<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 5/24/15
 * Time: 8:15 PM
 */

namespace CollapsingContent;


use CollapsingContent\Model\Entry;
use stdClass;
use WordWrap\Assets\BaseAsset;
use WordWrap\Assets\Template\Mustache\MustacheTemplate;
use WordWrap\Assets\View\ViewCollection;
use WordWrap\ShortCodeScriptLoader;

class ShortCode extends ShortCodeScriptLoader{

    /**
     * @param  $atts array inputs
     * @return string shortcode content
     */
    public function handleShortcode($atts) {

        if (!isset($atts["id"]))
            $entries = Entry::fetchAll();
        else
            $entries = [Entry::find_one($atts["id"])];

        $collections = $this->buildCollections($entries);

        $exportedHTML = '';

        foreach($collections as $collection)
            $exportedHTML.= $collection->export();

        return $exportedHTML;
    }

    /**
     * @param $entries Entry[]
     * @return BaseAsset[]
     */
    private function buildCollections($entries) {
        $collections = [];

        foreach($entries as $entry) {

            switch($entry->template) {
                case "nested":
                    $collections[] = $this->buildNestedTemplate($entry);
                    break;
                case "simple":
                    $collections[] = $this->buildSimpleTemplate($entry);
                    break;
            }
        }

        return $collections;
    }

    /**
     * Builds an instance of a simple template
     * @param $entry Entry for the template
     * @return ViewCollection the build template
     */
    private function buildSimpleTemplate($entry) {

        $collection = new ViewCollection($this->lifeCycle, "front_end-entry");

        $collection->setTemplateVar("title", $entry->title);
        $collection->setTemplateVar("top_content", $entry->top_content);
        $collection->setTemplateVar("bottom_content", $entry->bottom_content);

        if(count($entry->getChildren()))
            $collection->addChildViews("children", $this->buildCollections($entry->getChildren()));

        return $collection;
    }

    /**
     * @param $entry Entry for which we are building our nested template
     * @return MustacheTemplate the created template
     */
    private function buildNestedTemplate($entry) {

        $data = new stdClass();
        $data->children = [];

        foreach ($entry->getChildren() as $child) {

            $childData = new stdClass();

            $childData->title = $child->title;

            if ($child->using_post) {
                $childData->content = apply_filters("collapsing_content-post", $child->post_id);
            } else {
                $childData->above_content = $child->top_content;
                $childData->bottom_content = $child->bottom_content;
            }

            $data->children[] = $childData;
        }

        return new MustacheTemplate($this->lifeCycle, "nested", $data);

    }

    /**
     * Example:
     *   wp_register_script('my-script', plugins_url('js/my-script.js', __FILE__), array('jquery'), '1.0', true);
     *   wp_print_scripts('my-script');
     * @return void
     */
    public function addScript() {
        // TODO: Implement addScript() method.
    }
}