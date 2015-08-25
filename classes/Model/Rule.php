<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 8/25/15
 * Time: 5:22 PM
 */

namespace MagicTimeLine\Model;


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
     * @var string the description of this entry
     */
    public $description;

    /**
     * @var int the parent id of this rule
     */
    public $parent_id;

    /**
     * @var Rule the parent instance
     */
    public $parent;

    /**
     * @var Rule[] the primary id of the time line
     */
    public $children = [];

    /**
     * @var DateTime when the object was deleted
     */
    public $deleted_at;

    private function addChild(Rule $rule) {
        $rule->parent = $this;
        $this->children[] = $rule;
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
            "title" => "VARCHAR(120)",
            "description" => "TEXT",
            "parent_id" => "INT(11) UNSIGNED",
            "deleted_at" => "DATETIME"
        ];
    }

    /**
     * @return Rule[] all entries for given timeline
     */
    public static function find_all(){
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