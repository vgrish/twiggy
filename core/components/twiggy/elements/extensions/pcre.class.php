<?php


class Twiggy_Pcre_Filters
{
	static function quote(Twig_Environment $env, $value, $delimiter = '/')
	{
		if (!isset($value)) return null;

		return preg_quote($value, $delimiter);
	}


	static function get(Twig_Environment $env, $value, $pattern, $group = 0)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;
		if (!preg_match($pattern, $value, $matches)) return null;

		return isset($matches[$group]) ? $matches[$group] : null;
	}

	static function getAll($value, $pattern, $group = 0)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;
		if (!preg_match_all($pattern, $value, $matches, PREG_PATTERN_ORDER)) return array();

		return isset($matches[$group]) ? $matches[$group] : array();
	}

	static function grep($values, $pattern, $flags = '')
	{
		self::assertNoEval($pattern);

		if (!isset($values)) return null;

		if (is_string($flags)) $flags = $flags == 'invert' ? PREG_GREP_INVERT : 0;

		return preg_grep($pattern, $values, $flags);
	}

	static function replace($value, $pattern, $replacement = '', $limit = -1)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_replace($pattern, $replacement, $value, $limit);
	}

	static function filter($value, $pattern, $replacement = '', $limit = -1)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_filter($pattern, $replacement, $value, $limit);
	}

	static function split($value, $pattern)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_split($pattern, $value);
	}


	static function match(Twig_Environment $env, $value, $pattern)
	{
		self::assertNoEval($pattern);
		if (!isset($value)) return null;

		return preg_match($pattern, $value);
	}

	public static function assertNoEval($pattern)
	{
		if (preg_match('/(.).*\1(.+)$/', trim($pattern), $match) && strpos($match[1], 'e') !== false) throw new \Exception("Using the eval modifier for regular expressions is not allowed");
	}
}

class Twig_Extensions_Extension_Pcre extends Twig_Extension
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		if (!extension_loaded('pcre')) throw new \Exception("The Twig PCRE extension requires PHP extension 'pcre' (see http://www.php.net/pcre).");
	}

	/**
	 * Callback for Twig
	 * @ignore
	 */
	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('preg_quote', 'Twiggy_Pcre_Filters::quote', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_match', 'Twiggy_Pcre_Filters::match', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_get', 'Twiggy_Pcre_Filters::get', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_get_all', 'Twiggy_Pcre_Filters::getAll', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_grep', 'Twiggy_Pcre_Filters::grep', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_replace', 'Twiggy_Pcre_Filters::replace', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_filter', 'Twiggy_Pcre_Filters::filter', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_split', 'Twiggy_Pcre_Filters::split', array(
				'needs_environment' => true,
			)),
		);
	}

	public function getName()
	{
		return 'twiggy/pcre';
	}
}