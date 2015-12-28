<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/13/15
 * Time: 1:25 AM
 */

namespace CoillapsingContent\Admin\View;


use CoillapsingContent\Model\Rule;
use WordWrap\Assets\View\ViewCollection;
use WordWrap\LifeCycle;

class RulesContainer extends ViewCollection {

    /**
     * @param LifeCycle $lifeCycle
     * @param Rule[] $rules
     * @param Rule|null $parent the parent Rule
     */
    public function __construct(LifeCycle $lifeCycle, $rules, Rule $parent = null) {
        parent::__construct($lifeCycle, "admin/rules_container");

        foreach($rules as $rule) {
            $view = new RuleTR($this->lifeCycle, $rule->id, $rule->title);

            $this->addChildView("rules", $view);
        }

        if($parent != null)
            $this->setTemplateVar("parent", "&parent_id=" . $parent->id);

    }
}