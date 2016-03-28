<?php

if (!class_exists('pdoFetch')) {
    /** @noinspection PhpIncludeInspection */
    require MODX_CORE_PATH . 'components/pdotools/model/pdotools/pdofetch.class.php';
}

class twiggyPdoFetch extends pdoFetch
{
    /** @var modX $modx */
    public $modx;
    /* @var Twiggy $twiggy */
    public $twiggy;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        parent::__construct($modx, $config);

        $fqn = $modx->getOption('twiggy_class', null, 'twiggy.twiggy', true);
        $path = $modx->getOption('twiggy_class_path', null, MODX_CORE_PATH . 'components/twiggy/model/', true);
        if ($twiggyClass = $modx->loadClass($fqn, $path, false, true)) {
            $this->twiggy = new $twiggyClass($modx, $this->config);
        }

    }

    public function getChunk($name = '', array $properties = array(), $fastMode = false)
    {
        return $this->twiggy->getChunk($name, $properties, $fastMode);
    }

    public function parseChunk($name = '', array $properties = array(), $prefix = '[[+', $suffix = ']]')
    {
        return $this->twiggy->parseChunk($name, $properties, $prefix, $suffix);
    }

}