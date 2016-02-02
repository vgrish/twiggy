<?php
if (!class_exists('modParser')) {
	require_once MODX_CORE_PATH . 'model/modx/modparser.class.php';
}

class twiggyParser extends modParser
{

	/** @var Twiggy $Twiggy */
	public $Twiggy;

	function __construct(xPDO &$modx)
	{
		parent::__construct($modx);
		$fqn = $modx->getOption('twiggy_class', null, 'twiggy.Twiggy', true);
		if ($twiggyClass = $modx->loadClass($fqn, '', false, true)) {
			$this->Twiggy = new $twiggyClass($modx);
		} elseif ($twiggyClass = $modx->loadClass($fqn, MODX_CORE_PATH . 'components/twiggy/model/', false, true)) {
			$this->Twiggy = new $twiggyClass($modx);
		} else {
			$modx->log(modX::LOG_LEVEL_ERROR, '[twiggy] Could not load twiggy from "MODX_CORE_PATH/components/twiggy/model/".');
		}
	}

	public function processElementTags($parentTag, & $content, $processUncacheable = false, $removeUnprocessed = false, $prefix = "[[", $suffix = "]]", $tokens = array(), $depth = 0)
	{
		if (is_string($content) AND $processUncacheable AND preg_match('#\{.*\}#', $content)) {
			$content = $this->Twiggy->process($content, $this->modx->placeholders);
			/*if (!empty($this->modx->resource) AND is_object($this->modx->resource)) {
				$this->modx->resource->_jscripts = $this->modx->jscripts;
				$this->modx->resource->_sjscripts = $this->modx->sjscripts;
				$this->modx->resource->_loadedjscripts = $this->modx->loadedjscripts;
			}*/
		}

		return parent::processElementTags($parentTag, $content, $processUncacheable, $removeUnprocessed, $prefix, $suffix, $tokens, $depth);
	}

}