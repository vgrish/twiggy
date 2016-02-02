<?php

class TwiggyLoaderFile extends Twig_Loader_Filesystem
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

		$paths = $this->Twiggy->explodeAndClean($this->Twiggy->getOption('path_templates', null, '', true));
		$this->setPaths($paths);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getName($name)
	{
		$name = trim($name);
		if (strpos($name, 'file|') === false) {
			return null;
		}

		return str_replace('file|', '', $name);
	}

	/**
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function getSource($name)
	{
		$name = $this->getName($name);

		return parent::getSource($name);
	}

	/**
	 * @param string $name
	 *
	 * @return false|string
	 */
	public function getCacheKey($name)
	{
		$name = $this->getName($name);

		return parent::getCacheKey($name);
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function exists($name)
	{
		$name = $this->getName($name);

		return parent::exists($name);
	}

	/**
	 * @param string $name
	 * @param int    $time
	 *
	 * @return bool
	 */
	public function isFresh($name, $time)
	{
		$name = $this->getName($name);

		return parent::isFresh($name, $time);
	}
}
