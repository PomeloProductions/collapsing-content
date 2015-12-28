<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 9/13/15
 * Time: 1:25 AM
 */

namespace CollapsingContent\Admin\View;


use CollapsingContent\Model\Entry;
use WordWrap\Assets\View\ViewCollection;
use WordWrap\LifeCycle;

class EntriesContainer extends ViewCollection {

    /**
     * @param LifeCycle $lifeCycle
     * @param Entry[] $rules
     * @param Entry|null $parent the parent Rule
     */
    public function __construct(LifeCycle $lifeCycle, $rules, Entry $parent = null) {
        parent::__construct($lifeCycle, "admin/rules_container");

        foreach($rules as $rule) {
            $view = new EntryTR($this->lifeCycle, $rule->id, $rule->title);

            $this->addChildView("rules", $view);
        }

        if($parent != null)
            $this->setTemplateVar("parent", "&parent_id=" . $parent->id);

    }
}