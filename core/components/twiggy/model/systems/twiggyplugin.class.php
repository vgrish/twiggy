<?php

abstract class twiggyPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var Twiggy $twiggy */
    protected $twiggy;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct(modX $modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;

        $fqn = $modx->getOption('twiggy_class', null, 'twiggy.twiggy', true);
        $path = $modx->getOption('twiggy_class_path', null, MODX_CORE_PATH . 'components/twiggy/model/', true);
        $this->twiggy = $modx->getService(
            $fqn,
            '',
            $path,
            $this->scriptProperties
        );

    }

    abstract public function run();
}