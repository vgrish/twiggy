<?php

class TwiggyExtensionUniqID extends Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'twiggy/uniqid';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('uniqid', 'TwiggyExtensionUniqID::uniqid'),
        );
    }

    /**
     * @return string
     */
    public static function uniqid()
    {
        return uniqid();
    }
}