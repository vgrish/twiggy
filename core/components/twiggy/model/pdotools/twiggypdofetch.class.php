<?php

/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/pdotools/model/pdotools/pdofetch.class.php';

class twiggyPdoFetch extends pdoFetch
{
    /** @var modX $modx */
    public $modx;
    /* @var Twiggy $Twiggy */
    public $Twiggy;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        parent::__construct($modx, $config);
        $corePath = $this->modx->getOption('twiggy_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/twiggy/');
        $this->Twiggy = $this->modx->getService('Twiggy', 'Twiggy', $corePath . 'model/twiggy/', $config);
    }


    public function getChunk($name = '', array $properties = array(), $fastMode = false)
    {
        return $this->Twiggy->getChunk($name, $properties, $fastMode);
    }

    public function parseChunk($name = '', array $properties = array(), $prefix = '[[+', $suffix = ']]')
    {
        return $this->Twiggy->parseChunk($name, $properties, $prefix, $suffix);
    }

}