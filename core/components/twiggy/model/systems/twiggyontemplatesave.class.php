<?php

class twiggyOnTemplateSave extends twiggyPlugin
{
    public function run()
    {
        $this->twiggy->clearTwiggyCache();
    }

}
