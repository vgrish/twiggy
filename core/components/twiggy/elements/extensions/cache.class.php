<?php

require_once dirname(__FILE__) . '/node/cachenode.class.php';
require_once dirname(__FILE__) . '/tokenparser/cache.class.php';

class Twig_Extensions_Extension_Cache extends Twig_Extension
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
	 * @return string
	 */
	public function getName()
	{
		return 'twiggy/cache';
	}

	/**
	 * @return Twiggy
	 */
	public function getTwiggy()
	{
		return $this->Twiggy;
	}

	/**
	 * @return modX
	 */
	public function getModx()
	{
		return $this->modx;
	}


	/**
	 * @return array
	 */
	public function getTokenParsers()
	{
		return array(
			new Cache(),
		);
	}

	/**
	 * @param $name
	 * @param $time
	 *
	 * @return string
	 */
	public function getKey($name, $time)
	{
		return sha1($name . $time);
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getCache($key)
	{
		$options = array(
			'cache_key' => 'cache/html/' . $key,
			'cacheTime' => 0,
		);

		return $this->Twiggy->getCache($options);
	}

	/**
	 * @param $data
	 * @param $key
	 * @param $time
	 *
	 * @return string
	 */
	public function setCache($data, $key, $time)
	{
		$options = array(
			'cache_key' => 'cache/html/' . $key,
			'cacheTime' => $time,
		);

		return $this->Twiggy->setCache($data, $options);
	}
}
