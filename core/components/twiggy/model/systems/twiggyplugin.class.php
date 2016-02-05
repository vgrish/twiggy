<?php

abstract class twiggyPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var Twiggy $Twiggy */
    protected $Twiggy;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->twiggy = $this->modx->Twiggy;

        if (!is_object($this->Twiggy) OR !($this->Twiggy instanceof Twiggy)) {
            $corePath = $this->modx->getOption('twiggy_core_path', null,
                $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/twiggy/');
            $this->Twiggy = $this->modx->getService('Twiggy', 'Twiggy', $corePath . 'model/twiggy/',
                $this->scriptProperties);
        }
    }

    abstract public function run();
}