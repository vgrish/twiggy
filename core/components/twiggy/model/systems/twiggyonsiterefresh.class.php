<?php

class twiggyOnSiteRefresh extends twiggyPlugin
{
	public function run()
	{
		$folder = rtrim(trim($this->Twiggy->getOption('cache', $this->Twiggy->config, MODX_CORE_PATH . 'cache/default/twiggy/', true)), 'cache/') . '/';
		$this->modx->cacheManager->deleteTree($folder, array('deleteTop' => true, 'extensions' => array('.string.php', '.cache.php', '.php')));
	}

}
