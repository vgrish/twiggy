<?php

if (!class_exists('modParser')) {
    /** @noinspection PhpIncludeInspection */
    require MODX_CORE_PATH . 'model/modx/modparser.class.php';
}

class twiggyParser extends modParser
{

    /** @var Twiggy $twiggy */
    public $twiggy;

    /**
     * twiggyParser constructor.
     *
     * @param xPDO $modx
     */
    function __construct(xPDO &$modx)
    {
        parent::__construct($modx);
        
        $fqn = $modx->getOption('twiggy_class', null, 'twiggy.twiggy', true);
        $path = $modx->getOption('twiggy_class_path', null, MODX_CORE_PATH . 'components/twiggy/model/', true);
        if ($twiggyClass = $modx->loadClass($fqn, $path, false, true)) {
            $this->twiggy = new $twiggyClass($modx);
        }

    }

    /**
     * @param string $parentTag
     * @param string $content
     * @param bool   $processUncacheable
     * @param bool   $removeUnprocessed
     * @param string $prefix
     * @param string $suffix
     * @param array  $tokens
     * @param int    $depth
     *
     * @return int
     */
    public function processElementTags(
        $parentTag,
        & $content,
        $processUncacheable = false,
        $removeUnprocessed = false,
        $prefix = "[[",
        $suffix = "]]",
        $tokens = array(),
        $depth = 0
    ) {
        if (is_string($content) AND $processUncacheable AND preg_match('#\{.*\}#', $content)) {
            $content = $this->twiggy->process($content, $this->modx->placeholders);
        }

        if ($processUncacheable AND $removeUnprocessed AND
            !empty($this->modx->resource) AND is_object($this->modx->resource)
        ) {
            if ($this->modx->resource->get('_jscripts')) {
                $this->modx->jscripts = $this->modx->resource->get('_jscripts');
            }
        }

        return parent::processElementTags($parentTag, $content, $processUncacheable, $removeUnprocessed, $prefix,
            $suffix, $tokens, $depth);
    }

}