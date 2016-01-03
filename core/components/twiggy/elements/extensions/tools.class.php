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
			new Twig_SimpleFilter('pls', 'Twig_Extensions_Extension_Tools::filterGetPls', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('option', 'Twig_Extensions_Extension_Tools::filterGetOption', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('lexicon', 'Twig_Extensions_Extension_Tools::filterLexicon', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('makeUrl', 'Twig_Extensions_Extension_Tools::filterMakeUrl', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('toJson', 'Twig_Extensions_Extension_Tools::filterToJson', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('fromJson', 'Twig_Extensions_Extension_Tools::filterFromJson', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('toArray', 'Twig_Extensions_Extension_Tools::filterToArray', array(
				'needs_environment' => true
			)),
			new Twig_SimpleFilter('field', 'Twig_Extensions_Extension_Tools::filterGetField', array(
				'needs_environment' => true
			)),
		);
	}


	/**
	 * @param Twig_Environment $env
	 * @param                  $key
	 *
	 * @return mixed
	 */
	public static function filterGetPls(Twig_Environment $env, $key)
	{
		return self::$modx->getPlaceholder($key);
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
	public static function filterGetOption(Twig_Environment $env, $key, $options = null, $default = null, $skipEmpty = false)
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
	public static function filterLexicon(Twig_Environment $env, $key, $params = array(), $language = '')
	{
		return self::$modx->lexicon($key, $params, $language);
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
	public static function filterMakeUrl(Twig_Environment $env, $id, $context = '', $args = '', $scheme = -1, array $options = array())
	{
		return self::$modx->makeUrl($id, $context, $args, $scheme, $options);
	}

	/**
	 * @param Twig_Environment $env
	 * @param                  $array
	 *
	 * @return string
	 */
	public static function filterToJson(Twig_Environment $env, $array)
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
	public static function filterFromJson(Twig_Environment $env, $src, $asArray = true)
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
	public static function filterToArray(Twig_Environment $env, $instance, $keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
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
	public static function filterGetField(Twig_Environment $env, $instance, $key, $format = null, $formatTemplate = null)
	{
		if ($instance instanceof xPDOObject) {
			if (isset($instance->_fieldMeta[$key])) {
				return $instance->get($key, $format, $formatTemplate);
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction('loadLexicon', 'Twig_Extensions_Extension_Tools::loadLexicon'),
			new Twig_SimpleFunction('lexicon', 'Twig_Extensions_Extension_Tools::lexicon'),
			new Twig_SimpleFunction('_', 'Twig_Extensions_Extension_Tools::lexicon'),

			new Twig_SimpleFunction('makeUrl', 'Twig_Extensions_Extension_Tools::makeUrl'),
			new Twig_SimpleFunction('toJson', 'Twig_Extensions_Extension_Tools::toJson'),
			new Twig_SimpleFunction('fromJson', 'Twig_Extensions_Extension_Tools::fromJson'),
			new Twig_SimpleFunction('toArray', 'Twig_Extensions_Extension_Tools::toArray'),
			new Twig_SimpleFunction('getField', 'Twig_Extensions_Extension_Tools::getField'),
			new Twig_SimpleFunction('getCount', 'Twig_Extensions_Extension_Tools::getCount'),
			new Twig_SimpleFunction('getObject', 'Twig_Extensions_Extension_Tools::getObject'),
			new Twig_SimpleFunction('sendError', 'Twig_Extensions_Extension_Tools::sendError'),
			new Twig_SimpleFunction('sendRedirect', 'Twig_Extensions_Extension_Tools::sendRedirect'),
			new Twig_SimpleFunction('sendForward', 'Twig_Extensions_Extension_Tools::sendForward'),
			new Twig_SimpleFunction('setPlaceholder', 'Twig_Extensions_Extension_Tools::setPlaceholder'),
			new Twig_SimpleFunction('setPlaceholders', 'Twig_Extensions_Extension_Tools::setPlaceholders'),
			new Twig_SimpleFunction('toPlaceholder', 'Twig_Extensions_Extension_Tools::toPlaceholder'),
			new Twig_SimpleFunction('toPlaceholders', 'Twig_Extensions_Extension_Tools::toPlaceholders'),
			new Twig_SimpleFunction('getPlaceholder', 'Twig_Extensions_Extension_Tools::getPlaceholder'),
			new Twig_SimpleFunction('getPlaceholders', 'Twig_Extensions_Extension_Tools::getPlaceholders'),
			new Twig_SimpleFunction('unsetPlaceholder', 'Twig_Extensions_Extension_Tools::unsetPlaceholder'),
			new Twig_SimpleFunction('unsetPlaceholders', 'Twig_Extensions_Extension_Tools::unsetPlaceholders'),
			new Twig_SimpleFunction('getOption', 'Twig_Extensions_Extension_Tools::getOption'),
			new Twig_SimpleFunction('getPls', 'Twig_Extensions_Extension_Tools::getPlaceholder'),

			new Twig_SimpleFunction('var_dump', 'Twig_Extensions_Extension_Tools::var_dump'),
			new Twig_SimpleFunction('log', 'Twig_Extensions_Extension_Tools::log'),
			new Twig_SimpleFunction('chunk', 'Twig_Extensions_Extension_Tools::chunk'),
			new Twig_SimpleFunction('snippet', 'Twig_Extensions_Extension_Tools::snippet'),
			new Twig_SimpleFunction('processor', 'Twig_Extensions_Extension_Tools::processor'),

			new Twig_SimpleFunction('resource', 'Twig_Extensions_Extension_Tools::resource'),
			new Twig_SimpleFunction('user', 'Twig_Extensions_Extension_Tools::user'),
			new Twig_SimpleFunction('profile', 'Twig_Extensions_Extension_Tools::profile'),
		);
	}

	/**
	 * @param      $key
	 * @param null $options
	 * @param null $default
	 * @param bool $skipEmpty
	 *
	 * @return mixed
	 */
	public static function getOption($key, $options = null, $default = null, $skipEmpty = false)
	{
		return self::$modx->getOption($key, $options, $default, $skipEmpty);
	}


	/**
	 * 'ru:core:default'
	 *
	 * @param $key
	 */
	public static function loadLexicon($key)
	{
		if (!self::$modx->lexicon) {
			self::$modx->getService('lexicon', 'modLexicon');
		}
		self::$modx->lexicon->load($key);
	}

	/**
	 * @param        $key
	 * @param array  $params
	 * @param string $language
	 *
	 * @return null|string
	 */
	public static function lexicon($key, $params = array(), $language = '')
	{
		return self::$modx->lexicon($key, $params, $language);
	}

	/**
	 * @param        $id
	 * @param string $context
	 * @param string $args
	 * @param int    $scheme
	 * @param array  $options
	 *
	 * @return string
	 */
	public static function makeUrl($id, $context = '', $args = '', $scheme = -1, array $options = array())
	{
		return self::$modx->makeUrl($id, $context, $args, $scheme, $options);
	}

	/**
	 * @param $array
	 *
	 * @return string
	 */
	public static function toJson($array)
	{
		return self::$modx->toJSON($array);
	}

	/**
	 * @param      $src
	 * @param bool $asArray
	 *
	 * @return mixed
	 */
	public static function fromJson($src, $asArray = true)
	{
		return self::$modx->fromJSON($src, $asArray);
	}

	/**
	 * @param        $instance
	 * @param string $keyPrefix
	 * @param bool   $rawValues
	 * @param bool   $excludeLazy
	 * @param bool   $includeRelated
	 *
	 * @return array
	 */
	public static function toArray($instance, $keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
	{
		if ($instance instanceof xPDOObject) {
			return $instance->toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
		}

		return array();
	}

	/**
	 * @param      $instance
	 * @param      $key
	 * @param null $format
	 * @param null $formatTemplate
	 *
	 * @return mixed|null
	 */
	public static function getField($instance, $key, $format = null, $formatTemplate = null)
	{
		if ($instance instanceof xPDOObject) {
			return $instance->get($key, $format, $formatTemplate);
		}

		return null;
	}

	/**
	 * @param      $class
	 * @param null $criteria
	 *
	 * @return int
	 */
	public static function getCount($class, $criteria = null)
	{
		if (is_array($criteria)) {
			$criteria = self::$modx->newQuery($class, $criteria);
		}

		return self::$modx->getCount($class, $criteria);
	}

	/**
	 * @param      $class
	 * @param      $criteria
	 * @param bool $cacheFlag
	 *
	 * @return array
	 */
	public static function getObject($class, $criteria, $cacheFlag = true)
	{
		if (is_array($criteria)) {
			$criteria = self::$modx->newQuery($class, $criteria);
		}
		/** @var xPDOObject $object */
		$object = self::$modx->getObject($class, $criteria, $cacheFlag);

		return $object ? $object->toArray() : array();
	}

	/**
	 * @param string $type
	 * @param array  $options
	 */
	public static function sendError($type = '', $options = array())
	{
		self::$modx->sendError($type, $options);
	}

	/**
	 * @param        $url
	 * @param bool   $options
	 * @param string $type
	 * @param string $responseCode
	 */
	public static function sendRedirect($url, $options = false, $type = '', $responseCode = '')
	{
		self::$modx->sendRedirect($url, $options, $type, $responseCode);
	}

	/**
	 * @param      $id
	 * @param null $options
	 */
	public static function sendForward($id, $options = null)
	{
		self::$modx->sendForward($id, $options);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public static function setPlaceholder($key, $value)
	{
		self::$modx->setPlaceholder($key, $value);
	}

	/**
	 * @param        $placeholders
	 * @param string $namespace
	 */
	public static function setPlaceholders($placeholders, $namespace = '')
	{
		self::$modx->setPlaceholders($placeholders, $namespace);
	}

	/**
	 * @param        $key
	 * @param        $value
	 * @param string $prefix
	 * @param string $separator
	 * @param bool   $restore
	 *
	 * @return array
	 */
	public static function toPlaceholder($key, $value, $prefix = '', $separator = '.', $restore = false)
	{
		return self::$modx->toPlaceholder($key, $value, $prefix, $separator, $restore);
	}

	/**
	 * @param        $subject
	 * @param string $prefix
	 * @param string $separator
	 * @param bool   $restore
	 *
	 * @return array
	 */
	public static function toPlaceholders($subject, $prefix = '', $separator = '.', $restore = false)
	{
		return self::$modx->toPlaceholders($subject, $prefix, $separator, $restore);
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function getPlaceholder($key)
	{
		return self::$modx->getPlaceholder($key);
	}

	/**
	 * @return array
	 */
	public static function getPlaceholders()
	{
		return self::$modx->placeholders;
	}

	/**
	 * @param $key
	 */
	public static function unsetPlaceholder($key)
	{
		self::$modx->unsetPlaceholder($key);
	}

	/**
	 * @param $keys
	 */
	public static function unsetPlaceholders($keys)
	{
		self::$modx->unsetPlaceholders($keys);
	}

	/**
	 * @param $log
	 */
	public static function var_dump($log)
	{
		echo "<pre class=\"twiggy var_dump\">\n";
		var_dump($log);
		echo "</pre>";
	}

	/**
	 * @param     $log
	 */
	public static function log($log)
	{
		self::$modx->log(modX::LOG_LEVEL_ERROR, print_r($log, 1));
	}

	/**
	 * @param       $name
	 * @param array $properties
	 *
	 * @return mixed|string
	 */
	public static function chunk($name, array $properties = array(), $fastMode = false)
	{
		return self::$Twiggy->getChunk($name, $properties, $fastMode);
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
			self::$modx->addPackage($namespace, $corePath . "model/");
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

	/**
	 * @param string $field
	 *
	 * @return mixed|null
	 */
	public static function resource($field = '')
	{
		if (is_object(self::$modx->resource) AND self::$modx->resource instanceof modResource) {
			if (isset(self::$modx->resource->_fieldMeta[$field])) {
				return self::$modx->resource->get($field);
			}
		}

		return null;
	}

	/**
	 * @param string $field
	 *
	 * @return mixed|null
	 */
	public static function user($field = '')
	{
		if (is_object(self::$modx->user) AND self::$modx->user instanceof modUser) {
			if (isset(self::$modx->user->_fieldMeta[$field])) {
				return self::$modx->user->get($field);
			}
		}

		return null;
	}

	/**
	 * @param string $field
	 *
	 * @return mixed|null
	 */
	public static function profile($field = '')
	{
		if (is_object(self::$modx->user) AND self::$modx->user instanceof modUser) {
			if ($profile = self::$modx->user->getOne('Profile')) {
				if (isset($profile->_fieldMeta[$field])) {
					return $profile->get($field);
				}
			}
		}

		return null;
	}
}
