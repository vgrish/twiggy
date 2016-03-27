<?php

class twiggyOnTemplateRemove extends twiggyPlugin
{
    public function run()
    {
        $this->twiggy->clearTwiggyCache();
    }

}
