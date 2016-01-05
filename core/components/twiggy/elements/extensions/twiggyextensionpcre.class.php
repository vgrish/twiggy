<?php


class TwiggyPcreFilters
{
	/**
	 * @param Twig_Environment $env
	 * @param                  $value
	 * @param string           $delimiter
	 *
	 * @return null|string
	 */
	static function quote(Twig_Environment $env, $value, $delimiter = '/')
	{
		if (!isset($value)) return null;

		return preg_quote($value, $delimiter);
	}


	/**
	 * @param Twig_Environment $env
	 * @param                  $value
	 * @param                  $pattern
	 * @param int              $group
	 *
	 * @return null
	 * @throws Exception
	 */
	static function get(Twig_Environment $env, $value, $pattern, $group = 0)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;
		if (!preg_match($pattern, $value, $matches)) return null;

		return isset($matches[$group]) ? $matches[$group] : null;
	}

	/**
	 * @param     $value
	 * @param     $pattern
	 * @param int $group
	 *
	 * @return array|null
	 * @throws Exception
	 */
	static function getAll($value, $pattern, $group = 0)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;
		if (!preg_match_all($pattern, $value, $matches, PREG_PATTERN_ORDER)) return array();

		return isset($matches[$group]) ? $matches[$group] : array();
	}

	/**
	 * @param        $values
	 * @param        $pattern
	 * @param string $flags
	 *
	 * @return array|null
	 * @throws Exception
	 */
	static function grep($values, $pattern, $flags = '')
	{
		self::assertNoEval($pattern);

		if (!isset($values)) return null;

		if (is_string($flags)) $flags = $flags == 'invert' ? PREG_GREP_INVERT : 0;

		return preg_grep($pattern, $values, $flags);
	}

	/**
	 * @param        $value
	 * @param        $pattern
	 * @param string $replacement
	 * @param int    $limit
	 *
	 * @return mixed|null
	 * @throws Exception
	 */
	static function replace($value, $pattern, $replacement = '', $limit = -1)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_replace($pattern, $replacement, $value, $limit);
	}

	/**
	 * @param        $value
	 * @param        $pattern
	 * @param string $replacement
	 * @param int    $limit
	 *
	 * @return mixed|null
	 * @throws Exception
	 */
	static function filter($value, $pattern, $replacement = '', $limit = -1)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_filter($pattern, $replacement, $value, $limit);
	}

	/**
	 * @param $value
	 * @param $pattern
	 *
	 * @return array|null
	 * @throws Exception
	 */
	static function split($value, $pattern)
	{
		self::assertNoEval($pattern);

		if (!isset($value)) return null;

		return preg_split($pattern, $value);
	}


	/**
	 * @param Twig_Environment $env
	 * @param                  $value
	 * @param                  $pattern
	 *
	 * @return int|null
	 * @throws Exception
	 */
	static function match(Twig_Environment $env, $value, $pattern)
	{
		self::assertNoEval($pattern);
		if (!isset($value)) return null;

		return preg_match($pattern, $value);
	}

	/**
	 * @param $pattern
	 *
	 * @throws Exception
	 */
	public static function assertNoEval($pattern)
	{
		if (preg_match('/(.).*\1(.+)$/', trim($pattern), $match) && strpos($match[1], 'e') !== false) throw new \Exception("Using the eval modifier for regular expressions is not allowed");
	}
}

/**
 * Class Twig_Extensions_Extension_Pcre
 */
class TwiggyExtensionPcre extends Twig_Extension
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		if (!extension_loaded('pcre')) throw new \Exception("The Twig PCRE extension requires PHP extension 'pcre' (see http://www.php.net/pcre).");
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'twiggy/pcre';
	}

	/**
	 * Callback for Twig
	 *
	 * @ignore
	 */
	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('preg_quote', 'TwiggyPcreFilters::quote', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_match', 'TwiggyPcreFilters::match', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_get', 'TwiggyPcreFilters::get', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_get_all', 'TwiggyPcreFilters::getAll', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_grep', 'TwiggyPcreFilters::grep', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_replace', 'TwiggyPcreFilters::replace', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_filter', 'TwiggyPcreFilters::filter', array(
				'needs_environment' => true,
			)),
			new Twig_SimpleFilter('preg_split', 'TwiggyPcreFilters::split', array(
				'needs_environment' => true,
			)),
		);
	}

}