<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 8/25/15
 * Time: 5:22 PM
 */

namespace RulesRegulations\Model;


use DateTime;
use WordWrap\ORM\BaseModel;

class Rule extends BaseModel{

    /**
     * @var int the primary id of this rule
     */
    public $id;

    /**
     * @var string the title of this entry
     */
    public $title;

    /**
     * @var string the content that goes above child rules
     */
    public $top_content;

    /**
     * @var string the content that goes below child rules
     */
    public $bottom_content;

    /**
     * @var int the parent id of this rule
     */
    public $parent_id = null;

    /**
     * @var Rule the parent instance
     */
    public $parent = null;

    /**
     * @var Rule[] the primary id of the time line
     */
    public $children = null;

    /**
     * @var DateTime when the object was deleted
     */
    public $deleted_at = null;

    /**
     * @param Rule $rule to add to this instance
     */
    private function addChild(Rule $rule) {
        if($this->children == null)
            $this->children = [];

        $rule->parent = $this;
        $this->children[] = $rule;
    }

    /**
     * @return Rule|null the parent rule of null if none
     */
    public function getParent() {
        if($this->parent == null && $this->parent_id)
            $this->parent = Rule::find_one($this->parent_id);

        return $this->parent;
    }

    /**
     * @return Rule[] all children of given rule
     */
    public function getChildren() {

        if($this->children == null) {
            $this->children = [];

            $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL AND `parent_id` = " . $this->id;

            global $wpdb;

            $results = $wpdb->get_results($SQL, ARRAY_A);

            foreach ($results as $row)
                $this->addChild(new Rule($row));
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
        return "wp_rules_regulations_rule";
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
     * @return Rule[] all entries in the rules table
     */
    public static function fetchAll(){
        $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL";

        global $wpdb;

        $rows = $wpdb->get_results($SQL, ARRAY_A);

        $rules = [];
        foreach($rows as $row) {
            $rules[] = new Rule($row);
        }

        $organizedRules = self::organizeRules($rules);

        return $organizedRules;
    }

    /**
     * @return Rule[] all entries in the rules table that do not have a parent
     */
    public static function fetchAllParents() {
        $SQL = "SELECT * FROM `" . static::get_table() . "` WHERE `deleted_at` IS NULL AND `parent_id` IS NULL";

        global $wpdb;

        $rows = $wpdb->get_results($SQL, ARRAY_A);

        $rules = [];
        foreach($rows as $row)
            $rules[] = new Rule($row);

        return $rules;
    }

    /**
     * @param Rule[] $rules
     * @return Rule[] organizes rules
     */
    private static function organizeRules(array $rules) {
        $organizedRules = [];

        foreach($rules as $rule) {

            if($rule->parent_id) {
                foreach($rules as $parent) {
                    if($parent->id == $rule->parent_id) {
                        $parent->addChild($rule);
                        break;
                    }
                }
            }

            else
                $organizedRules[] = $rule;
        }

        return $organizedRules;
    }
}