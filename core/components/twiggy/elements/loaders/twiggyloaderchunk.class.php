<?php

class TwiggyLoaderChunk extends Twig_Loader_Array
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

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getName($name)
	{
		$name = trim($name);
		if (strpos($name, 'chunk|') === false) {
			return null;
		}

		return str_replace('chunk|', '', $name);
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function exists($name)
	{
		$name = $this->getName($name);
		$c = (is_numeric($name) AND $name > 0) ? $name : array('name' => $name);

		return (bool)$this->modx->getCount('modChunk', $c);
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
		$c = (is_numeric($name) AND $name > 0) ? $name : array('name' => $name);
		/** @var modChunk $chunk */
		if ($element = $this->modx->getObject('modChunk', $c)) {
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
		return !(boolean)$this->Twiggy->getOption('debug', $this->Twiggy->config, false, true);
	}

}
