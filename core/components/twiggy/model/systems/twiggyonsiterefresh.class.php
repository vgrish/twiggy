<?php

class twiggyOnSiteRefresh extends twiggyPlugin
{
    public function run()
    {
        $this->twiggy->clearTwiggyCache();
    }

}
