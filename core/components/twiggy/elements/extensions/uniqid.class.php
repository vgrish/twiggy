<?php

class Twig_Extensions_Extension_UniqID extends Twig_Extension
{
	public function getName()
	{
		return 'Twig_Extensions_Extension_UniqID';
	}


	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction('uniqid', 'Twig_Extensions_Extension_UniqID::uniqid'),
		);
	}

	public static function uniqid()
	{
		return uniqid();
	}
}