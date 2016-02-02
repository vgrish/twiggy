<?php

class TwiggyLoaderTemplate extends Twig_Loader_Array
{
	/** @var MODx $modx */
	private $modx;
	/** @var Twiggy $Twiggy */
	private $Twiggy;

	/**
	 * @param Twiggy $Twiggy
	 */
	public function __construct(Twiggy &$Twiggy)
	{
		$this->Twiggy = &$Twiggy;
		$this->modx = &$Twiggy->modx;
	}

	public function getName($name)
	{
		$name = trim($name);
		if (strpos($name, 'template|') === false) {
			return null;
		}

		return str_replace('template|', '', $name);
	}

	public function exists($name)
	{
		$name = $this->getName($name);
		$c = (is_numeric($name) AND $name > 0) ? $name : array('templatename' => $name);

		return (bool)$this->modx->getCount('modTemplate', $c);
	}

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
				$content = $this->Twiggy->parseChunk('@INLINE ' . $content, $properties);
			}
		}

		return $content;
	}

	public function getCacheKey($name)
	{
		return $name;
	}

	public function isFresh($name, $time)
	{
		return !(boolean)$this->Twiggy->getOption('debug', '', false, true);
	}

}
