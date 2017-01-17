<?php
namespace CollapsingContent;

use CollapsingContent\Model\Entry;
use WordWrap\LifeCycle;

class Plugin extends LifeCycle {



    public function onInitActionsAndFilters() {


        $this->assetManager->registerAssetType("html", __DIR__ . "/../assets/html/");

    }

}
