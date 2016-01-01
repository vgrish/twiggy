<?php

class Twig_Extensions_Extension_Tools extends Twig_Extension
{
	/** @var MODx $modx */
	static private $modx;
	/** @var Twiggy $Twiggy */
	static private $Twiggy;

	/**
	 * @param Twiggy $Twiggy
	 */
	public function __construct(Twiggy &$Twiggy)
	{
		self::$Twiggy = &$Twiggy;
		self::$modx = &$Twiggy->modx;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'twiggy/tools';
	}

	/**
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('get', 'Twig_Extensions_Extension_Tools::get', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('post', 'Twig_Extensions_Extension_Tools::post', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('session', 'Twig_Extensions_Extension_Tools::session', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('cookie', 'Twig_Extensions_Extension_Tools::cookie', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('request', 'Twig_Extensions_Extension_Tools::request', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('files', 'Twig_Extensions_Extension_Tools::files', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('server', 'Twig_Extensions_Extension_Tools::server', array(
				'needs_environment' => true
			)),


			new Twig_SimpleFilter('option', 'Twig_Extensions_Extension_Tools::option', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('lexicon', 'Twig_Extensions_Extension_Tools::lexicon', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('pls', 'Twig_Extensions_Extension_Tools::pls', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('url', 'Twig_Extensions_Extension_Tools::url', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('toJson', 'Twig_Extensions_Extension_Tools::toJson', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('fromJson', 'Twig_Extensions_Extension_Tools::fromJson', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('toArray', 'Twig_Extensions_Extension_Tools::toArray', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('field', 'Twig_Extensions_Extension_Tools::field', array(
				'needs_environment' => true
			)),

		);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function get(Twig_Environment $env, $key)
	{
		return isset($_GET[$key]) ? $_GET[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function post(Twig_Environment $env, $key)
	{
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function session(Twig_Environment $env, $key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function cookie(Twig_Environment $env, $key)
	{
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function request(Twig_Environment $env, $key)
	{
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function files(Twig_Environment $env, $key)
	{
		return isset($_FILES[$key]) ? $_FILES[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return null
	 */
	public static function server(Twig_Environment $env, $key)
	{
		return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 * @param null             $options
	 * @param null             $default
	 * @param bool             $skipEmpty
	 *
	 * @return mixed
	 */
	public static function option(Twig_Environment $env, $key, $options = null, $default = null, $skipEmpty = false)
	{
		return self::$modx->getOption($key, $options, $default, $skipEmpty);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 * @param array            $params
	 * @param string           $language
	 *
	 * @return null|string
	 */
	public static function lexicon(Twig_Environment $env, $key, $params = array(), $language = '')
	{
		return self::$modx->lexicon($key, $params, $language);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return mixed
	 */
	public static function pls(Twig_Environment $env, $key)
	{
		return self::$modx->getPlaceholder($key);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $id
	 * @param string           $context
	 * @param string           $args
	 * @param int              $scheme
	 * @param array            $options
	 *
	 * @return string
	 */
	public static function url(Twig_Environment $env, $id, $context = '', $args = '', $scheme = -1, array $options = array())
	{
		return self::$modx->makeUrl($id, $context, $args, $scheme, $options);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $array
	 *
	 * @return string
	 */
	public static function toJson(Twig_Environment $env, $array)
	{
		return self::$modx->toJSON($array);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $src
	 * @param bool             $asArray
	 *
	 * @return mixed
	 */
	public static function fromJson(Twig_Environment $env, $src, $asArray = true)
	{
		return self::$modx->fromJSON($src, $asArray);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $instance
	 * @param string           $keyPrefix
	 * @param bool             $rawValues
	 * @param bool             $excludeLazy
	 * @param bool             $includeRelated
	 *
	 * @return array
	 */
	public static function toArray(Twig_Environment $env, $instance, $keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
	{
		if ($instance instanceof xPDOObject) {
			return $instance->toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
		}

		return array();
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $instance
	 * @param                  $key
	 * @param null             $format
	 * @param null             $formatTemplate
	 *
	 * @return mixed|null
	 */
	public static function field(Twig_Environment $env, $instance, $key, $format = null, $formatTemplate = null)
	{
		if ($instance instanceof xPDOObject) {
			return $instance->get($key, $format, $formatTemplate);
		}

		return null;
	}


	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction('chunk', 'Twig_Extensions_Extension_Tools::chunk'),
			new Twig_SimpleFunction('snippet', 'Twig_Extensions_Extension_Tools::snippet'),
			new Twig_SimpleFunction('processor', 'Twig_Extensions_Extension_Tools::processor'),
			//
		);
	}

	/**
	 * @param       $name
	 * @param array $properties
	 *
	 * @return mixed|string
	 */
	public static function chunk($name, array $properties = array())
	{
		$output = '';
		$cacheable = true;
		if (strpos($name, '!') !== false) {
			$name = substr($name, 1);
			$cacheable = false;
		}
		if (strpos($name, '@INLINE ') !== false) {
			$content = str_replace('@INLINE', '', $name);
			/** @var modChunk $chunk */
			$chunk = self::$modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
			$chunk->setCacheable($cacheable);
			$output = $chunk->process($properties, $content);
		} elseif (self::$modx->getParser()) {
			/** @var modChunk $chunk */
			$chunk = self::$modx->parser->getElement('modChunk', $name);
			if ($chunk instanceof modChunk) {
				$chunk->setCacheable($cacheable);
				$output = $chunk->process($properties);
			}
		}

		return $output;
	}

	/**
	 * @param       $name
	 * @param array $properties
	 *
	 * @return mixed|string
	 */
	public static function snippet($name, array $properties = array())
	{
		$output = '';
		$cacheable = true;
		if (strpos($name, '!') !== false) {
			$name = substr($name, 1);
			$cacheable = false;
		}
		if (self::$modx->getParser()) {
			/** @var modSnippet $snippet */
			$snippet = self::$modx->parser->getElement('modSnippet', $name);
			if ($snippet instanceof modSnippet) {
				$snippet->setCacheable($cacheable);
				$output = $snippet->process($properties);
			}
		}

		return $output;
	}

	/**
	 * @param string $action
	 * @param array  $properties
	 *
	 * @return array
	 */
	public static function processor($action = '', array $properties = array())
	{
		$options = array();
		$namespace = self::$modx->getOption('ns', $properties, null, true);
		if ($namespace) {
			$corePath = self::$modx->getOption("{$namespace}.core_path", null);
			if (!$corePath AND $ns = self::$modx->getObject('modNamespace', $namespace)) {
				$corePath = $ns->getCorePath();
			}
			self::$modx->getService($namespace, $namespace, $corePath . "model/{$namespace}/", $properties);
			$options['processors_path'] = $corePath . 'processors/';
		}
		if (!empty($properties['location'])) {
			$options['location'] = $properties['location'];
		}

		self::$modx->error->reset();
		$response = self::$modx->runProcessor($action, $properties, $options);

		if ($response instanceof modProcessorResponse) {
			$output = array(
				'success'  => !$response->isError(),
				'message'  => $response->getMessage(),
				'response' => self::$modx->fromJSON($response->getResponse()),
				'errors'   => $response->getFieldErrors()
			);
		} else {
			$output = array(
				'success'  => false,
				'message'  => $response,
				'response' => array(),
				'errors'   => array()
			);
		}

		return $output;
	}

}
