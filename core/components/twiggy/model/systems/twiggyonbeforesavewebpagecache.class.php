<?php

/*
 * Thanks to Fi1osof (https://github.com/Fi1osof) for idea
 */

class twiggyOnBeforeSaveWebPageCache extends twiggyPlugin
{
    public function run()
    {
        if (is_object($this->modx->resource) AND $this->modx->resource instanceof modResource) {
            $this->modx->resource->_jscripts = $this->modx->jscripts;

            /* остальное нельзя кэшировать
            $this->modx->resource->_sjscripts = $this->modx->sjscripts;
            $this->modx->resource->_loadedjscripts = $this->modx->loadedjscripts;*/
        }
    }
}