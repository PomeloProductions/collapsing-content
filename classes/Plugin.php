<?php
namespace CollapsingContent;

use CollapsingContent\Model\Entry;
use WordWrap\LifeCycle;

class Plugin extends LifeCycle {


    public function getPluginDisplayName() {
        return 'Collapsing Content';
    }


    public function onInitActionsAndFilters() {


        $this->assetManager->registerAssetType("html", __DIR__ . "/../assets/html/");

        $sc = new ShortCode($this);
        $sc->register('collapsing_content');
    }

}
