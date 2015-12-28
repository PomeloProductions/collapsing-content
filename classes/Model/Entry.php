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
     * @var int the primary id of this entry
     */
    public $id;

    /**
     * @var string the title of this entry
     */
    public $title;

    /**
     * @var string the content that goes above child entries
     */
    public $top_content;

    /**
     * @var string the content that goes below child entries
     */
    public $bottom_content;

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
        return "wp_collapsing_content_entry";
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
            "top_content" => "TEXT",
            "bottom_content" => "TEXT",
            "parent_id" => "INT(11) UNSIGNED",
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