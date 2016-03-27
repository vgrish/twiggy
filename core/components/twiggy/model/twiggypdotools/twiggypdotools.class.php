<?php

if (!class_exists('pdoTools')) {
    /** @noinspection PhpIncludeInspection */
    require MODX_CORE_PATH . 'components/pdotools/model/pdotools/pdotools.class.php';
}

class twiggyPdoTools extends pdoTools
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
        $this->twiggy = $modx->getService(
            $fqn,
            '',
            $path,
            $this->config
        );

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
