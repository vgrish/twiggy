<?php

class TwiggyLoaderTemplate extends Twig_Loader_Array
{
    /** @var MODx $modx */
    private $modx;
    /** @var Twiggy $twiggy */
    private $twiggy;

    /**
     * @param Twiggy $Twiggy
     */
    public function __construct(Twiggy &$Twiggy)
    {
        $this->twiggy = &$Twiggy;
        $this->modx = &$Twiggy->modx;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getName($name)
    {
        $name = trim($name);
        if (strpos($name, 'template|') === false) {
            return null;
        }

        return str_replace('template|', '', $name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        $name = $this->getName($name);
        $c = (is_numeric($name) AND $name > 0) ? $name : array('templatename' => $name);

        return (bool)$this->modx->getCount('modTemplate', $c);
    }

    /**
     * @param string $name
     *
     * @return mixed|string
     */
    public function getSource($name)
    {
        $name = $this->getName($name);
        $content = '';
        if ($pos = strpos($name, '@')) {
            $propertySet = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
        }
        $c = (is_numeric($name) AND $name > 0) ? $name : array('templatename' => $name);
        /** @var modChunk $chunk */
        if ($element = $this->modx->getObject('modTemplate', $c)) {
            $content = $element->getContent();
            if (!empty($propertySet) AND $tmp = $element->getPropertySet($propertySet)) {
                $properties = $tmp;
            } else {
                $properties = $element->getProperties();
            }
            if (!empty($content) AND !empty($properties)) {
                $content = $this->twiggy->parseChunk('@INLINE ' . $content, $properties);
            }
        }

        return $content;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * @param string $name
     * @param int    $time
     *
     * @return bool
     */
    public function isFresh($name, $time)
    {
        return !(boolean)$this->twiggy->getOption('debug', $this->twiggy->config, false, true);
    }

}
