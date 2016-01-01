<?php

class Twig_Extensions_Extension_UniqID extends Twig_Extension
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
			new Twig_SimpleFunction('uniqid', 'Twig_Extensions_Extension_UniqID::uniqid'),
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