<?php

class twiggyOnLoadWebDocument extends twiggyPlugin
{
    public function run()
    {
        if ($this->modx->getService('registry', 'registry.modRegistry')) {
            $this->modx->registry->getRegister('twiggy', 'registry.modDbRegister', array('directory' => 'twiggy'));
            $this->modx->registry->twiggy->connect();
            $this->modx->registry->twiggy->subscribe('/cache/time');
            if (!$this->modx->registry->twiggy->read(array('remove_read' => false, 'poll_limit' => 1))) {
                $this->twiggy->clearTwiggyCache();
            }
        }
    }
}
