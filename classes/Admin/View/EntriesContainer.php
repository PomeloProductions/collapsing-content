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
     * @param Entry[] $entries
     * @param Entry|null $parent the parent Entry
     */
    public function __construct(LifeCycle $lifeCycle, $entries, Entry $parent = null) {
        parent::__construct($lifeCycle, "admin/entries_container");

        foreach($entries as $entry) {
            $view = new EntryTR($this->lifeCycle, $entry->id, $entry->title);

            $this->addChildView("entries", $view);
        }

        if($parent != null)
            $this->setTemplateVar("parent", "&parent_id=" . $parent->id);

    }
}