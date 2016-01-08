<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 8/25/15
 * Time: 5:22 PM
 */

namespace CollapsingContent\Model;


use DateTime;
use WordWrap\ORM\BaseModel;

class Entry extends BaseModel{

    /**
     * @var string[] all available templates
     */
    public static $availableTemplates = [
        "simple", "nested"
    ];

    /**
     * @var int the primary id of this entry
     */
    public $id;

    /**
     * @var string the title of this entry
     */
    public $title;

    /**
     * @var string the subtitle of this entry
     */
    public $subtitle;

    /**
     * @var string the content that goes above child entries
     */
    public $top_content;

    /**
     * @var string the content that goes below child entries
     */
    public $bottom_content;

    /**
     * @var bool whether or not this entry is exclusivly using a post
     */
    public $using_post = true;

    /**
     * @var int the id of the post this uses if we are using a post
     */
    public $post_id;

    /**
     * @var string the template the user has chosen
     */
    public $template = "simple";

    /**
     * @var int the parent id of this entry
     */
    public $parent_id = null;

    /**
     * @var Entry the parent instance
     */
    public $parent = null;

    /**
     * @var Entry[] the primary id of the time line
     */
    public $children = null;

    /**
     * @var DateTime when the object was deleted
     */
    public $deleted_at = null;

    /**
     * @param Entry $entry to add to this instance
     */
    private function addChild(Entry $entry) {
        if($this->children == null)
            $this->children = [];

        $entry->parent = $this;
        $this->children[] = $entry;
    }

    /**
     * @return Entry|null the parent entry of null if none
     */
    public function getParent() {
        if($this->parent == null && $this->parent_id)
            $this->parent = Entry::find_one($this->parent_id);

        return $this->parent;
    }

    /**
     * @return Entry[] all children of given entry
     */
    public function getChildren() {

        if($this->children == null) {
            $this->children = [];

            $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL AND `parent_id` = " . $this->id;

            global $wpdb;

            $results = $wpdb->get_results($SQL, ARRAY_A);

            foreach ($results as $row)
                $this->addChild(new Entry($row));
        }

        return $this->children;
    }

    /**
     * @return string the template attached to this entry
     */
    public function getTemplate() {

        if ($this->template)
            return $this->template;

        return "simple";
    }

    /**
     * @param $template string the template we are setting
     */
    public function setTemplate($template) {
        if (in_array($template, static::$availableTemplates))
            $this->template = $template;
    }

    /**
     * Overrides parent function sets this objects deleted at field to be now, and then saves
     */
    public function delete() {
        $this->deleted_at = new DateTime();

        $this->save();
    }

    /**
     * Overwrite this in your concrete class. Returns the table name used to
     * store models of this class.
     *
     * @return string
     */
    public static function get_table(){
        return "wp_collapsing_content_entries";
    }

    /**
     * Get an array of fields to search during a search query.
     *
     * @return array
     */
    public static function get_searchable_fields() {
        // TODO: Implement get_searchable_fields() method.
    }

    /**
     * Get an array of all fields for this Model with a key and a value
     * The key should be the name of the column in the database and the value should be the structure of it
     *
     * @return array
     */
    public static function get_fields() {
        return [
            "title" => "TEXT",
            "subtitle" => "TEXT",
            "top_content" => "TEXT",
            "bottom_content" => "TEXT",
            "using_post" => "TINYINT(1)",
            "post_id" => "INT(11) UNSIGNED",
            "parent_id" => "INT(11) UNSIGNED",
            "template" => "VARCHAR(55)",
            "deleted_at" => "DATETIME"
        ];
    }

    /**
     * @return Entry[] all entries in the entries table
     */
    public static function fetchAll(){
        $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL";

        global $wpdb;

        $rows = $wpdb->get_results($SQL, ARRAY_A);

        $entries = [];
        foreach($rows as $row) {
            $entries[] = new Entry($row);
        }

        $organizedEntries = self::organizeEntries($entries);

        return $organizedEntries;
    }

    /**
     * @return Entry[] all entries in the entries table that do not have a parent
     */
    public static function fetchAllParents() {
        $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL AND `parent_id` IS NULL";

        global $wpdb;

        $rows = $wpdb->get_results($SQL, ARRAY_A);

        $entries = [];
        foreach($rows as $row)
            $entries[] = new Entry($row);

        return $entries;
    }

    /**
     * @param Entry[] $entries
     * @return Entry[] organizes entries
     */
    private static function organizeEntries(array $entries) {
        $organizedEntries = [];

        foreach($entries as $entry) {

            if($entry->parent_id) {
                foreach($entries as $parent) {
                    if($parent->id == $entry->parent_id) {
                        $parent->addChild($entry);
                        break;
                    }
                }
            }

            else
                $organizedEntries[] = $entry;
        }

        return $organizedEntries;
    }
}