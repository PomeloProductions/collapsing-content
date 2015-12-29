<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 5/24/15
 * Time: 8:15 PM
 */

namespace CollapsingContent;


use CollapsingContent\Model\Entry;
use WordWrap\Assets\View\ViewCollection;
use WordWrap\ShortCodeScriptLoader;

class ShortCode extends ShortCodeScriptLoader{

    /**
     * @param  $atts array inputs
     * @return string shortcode content
     */
    public function handleShortcode($atts) {

        $entries = Entry::fetchAll();

        $collections = $this->buildCollections($entries);

        $exportedHTML = '';

        foreach($collections as $collection)
            $exportedHTML.= $collection->export();

        return $exportedHTML;
    }

    /**
     * @param $entries Entry[]
     * @return ViewCollection[]
     */
    private function buildCollections($entries) {
        $collections = [];

        foreach($entries as $entry) {

            $templateName = "front_end/" . $entry->template . "/entry";

            $collection = new ViewCollection($this->lifeCycle, $templateName);

            $collection->setTemplateVar("title", $entry->title);
            $collection->setTemplateVar("top_content", $entry->top_content);
            $collection->setTemplateVar("bottom_content", $entry->bottom_content);

            if(count($entry->getChildren()))
                $collection->addChildViews("children", $this->buildCollections($entry->getChildren()));

            $collections[] = $collection;
        }

        return $collections;
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