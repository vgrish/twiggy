<?php

/**
 * The base class for Twiggy.
 */
class Twiggy
{
	/* @var modX $modx */
	public $modx;
	/** @var Twig_Environment $twig */
	public $twig;
	/** @var twiggyParser $parser */
	public $parser;
	/** @var array $config */
	public $config = array();
	/** @var string $namespace */
	protected $namespace = 'twiggy';
	/** @var  $debug */
	protected $debug;

	/** @var array $store Array for cache elements and user data */
	public $store = array(
		'data'     => array(),
		'chunk'    => array(),
		'snippet'  => array(),
		'resource' => array()
	);

	/**
	 * @param modX  $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;
		$this->setConfig(array_merge($this->getConfig(), $config));
	}

	/**
	 * @param       $key
	 * @param array $config
	 * @param null  $default
	 *
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
	{
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		if ($skipEmpty AND empty($option)) {
			$option = $default;
		}

		return $option;
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		/* array cache $options */
		$options = array(
			'cache_key' => 'config/twiggy_twig',
			'cacheTime' => 0,
		);
		if (!$config = $this->getCache($options)) {
			$q = $this->modx->newQuery('modSystemSetting', array('area' => 'twiggy_twig'));
			$q->select('key');
			if ($q->prepare() AND $q->stmt->execute()) {
				$settings = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
				foreach ($settings as $setting) {
					$config[str_replace('twiggy_', '', $setting)] = $this->modx->getOption($setting, null);
				}
			}
			$this->setCache($config, $options);
		}

		return $config;
	}

	/**
	 * @param array $config
	 */
	public function setConfig(array $config = array())
	{
		$this->config = array_merge(array(
			'debug'      => false,
			'cache'      => null,
			'template'   => null,
			'autoescape' => false,
		), $config);
	}

	/**
	 * @return twiggyParser
	 */
	public function getParser()
	{
		$this->parser = $this->modx->getParser();
		if (!($this->parser instanceof twiggyParser)) {
			if (!class_exists('twiggyParser')) {
				require_once dirname(__FILE__) . '/twiggyparser.class.php';
			}
			$this->parser = new twiggyParser($this->modx);
		}

		return $this->parser;
	}

	/**
	 * @return bool|Twig|Twig_Environment
	 */
	public function getTwig()
	{
		if (!$this->twig) {
			try {

				require_once dirname(dirname(dirname(__FILE__))) . '/vendor/Twig/vendor/autoload.php';

				/** @var Twig_Loader_Chain $twigLoader */
				$twigLoader = new Twig_Loader_Chain(array());

				$loaders = $this->explodeAndClean($this->getOption('loaders', $this->config, '', true));
				if (!empty($loaders)) {
					$pathLoaders = trim($this->getOption('path_loaders', $this->config, '', true));
					foreach ($loaders as $loader) {
						if ($loaderClass = $this->modx->loadClass('TwiggyLoader' . $loader, $pathLoaders, false, true)) {
							$twigLoader->addLoader(new $loaderClass($this));
						}
					}
				}

				/** @var Twig_Environment twig */
				$this->twig = new Twig_Environment($twigLoader, $this->config);
				$this->debug = (boolean)$this->getOption('debug', $this->config, false, true);
				if ($this->debug) {
					$this->twig->addExtension(new Twig_Extension_Debug());
				}

				/** load sandbox */
				$sandbox = (boolean)$this->getOption('sandbox', $this->config, false, true);
				if ($sandbox) {
					/* array cache $options */
					$options = array(
						'cache_key' => 'config/twiggy_sandbox',
						'cacheTime' => 0,
					);
					if (!$sandboxConfig = $this->getCache($options)) {
						$sandboxTags = $this->explodeAndClean($this->getOption('sandbox_tags', $this->config, '', true));
						$sandboxFilters = $this->explodeAndClean($this->getOption('sandbox_filters', $this->config, '', true));
						$sandboxMethods = $this->modx->fromJSON($this->getOption('sandbox_methods', $this->config, "{}", true));
						$sandboxProperties = $this->modx->fromJSON($this->getOption('sandbox_properties', $this->config, "{}", true));
						$sandboxFunctions = $this->explodeAndClean($this->getOption('sandbox_functions', $this->config, '', true));
						$sandboxConfig = array(
							'tags'       => $sandboxTags,
							'filters'    => $sandboxFilters,
							'methods'    => $sandboxMethods,
							'properties' => $sandboxProperties,
							'functions'  => $sandboxFunctions
						);
						$this->setCache($sandboxConfig, $options);
					}

					$sandboxPolicy = new Twig_Sandbox_SecurityPolicy(
						$sandboxConfig['tags'],
						$sandboxConfig['filters'],
						$sandboxConfig['methods'],
						$sandboxConfig['properties'],
						$sandboxConfig['functions']
					);
					$sandboxExtension = new Twig_Extension_Sandbox($sandboxPolicy);
					$this->twig->addExtension($sandboxExtension);
				}

				/** load external $extensions */
				$extensions = $this->explodeAndClean($this->getOption('extensions', $this->config, '', true));
				if (!empty($extensions)) {
					$pathExtensions = trim($this->getOption('path_extensions', $this->config, '', true));
					foreach ($extensions as $extension) {
						if ($extensionClass = $this->modx->loadClass('TwiggyExtension' . $extension, $pathExtensions, false, true)) {
							$this->twig->addExtension(new $extensionClass($this));
						}
					}
				}

				/** set Globals */
				$this->setGlobals();

			} catch (Exception $e) {
				$this->modx->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage());

				return false;
			}
		}

		return $this->twig;
	}

	/**
	 * setGlobals
	 */
	public function setGlobals()
	{
		if ($this->twig) {
			$this->twig->addGlobal('get', $_GET);
			$this->twig->addGlobal('post', $_POST);
			$this->twig->addGlobal('session', $_SESSION);
			$this->twig->addGlobal('cookie', $_COOKIE);
			$this->twig->addGlobal('request', $_REQUEST);
			$this->twig->addGlobal('files', $_FILES);
			$this->twig->addGlobal('server', $_SERVER);
			$this->twig->addGlobal('modx', $this->modx);
		}
	}

	/**
	 * @param       $content
	 * @param array $properties
	 *
	 * @return string
	 */
	public function process($content, array $pls = array(), $fastMode = false)
	{
		$content = is_array($content) ? trim($content['content']) : trim($content);
		if (!preg_match('#\{.*\}#', $content)) {
			return $content;
		}
		if (!$twig = $this->getTwig()) {
			return $content;
		}
		try {
			if ($fastMode) {
				$twig->setLoader(new \Twig_Loader_Array(['tpl' => $content]));
				$content = $twig->render('tpl', array('pls' => $pls));
			} else {
				$template = $twig->createTemplate($content);
				$content = $template->render(array('pls' => $pls));
			}
		} catch (Exception $e) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
		}

		return $content;
	}


	/**
	 * This algorithm taken from
	 * https://github.com/bezumkin/pdoTools/blob/badd69699eeb39579ac020e02c7207b6ff880c46/core/components/pdotools/model/pdotools/pdotools.class.php#L678
	 *
	 * @param       $name
	 * @param array $row
	 *
	 * @return array|null
	 */
	protected function loadChunk($name, $row = array())
	{
		$name = trim($name);
		$content = '';

		switch (true) {
			case strpos($name, '@INLINE ') !== false:
				$cacheName = crc32($name);
				$binding = 'INLINE';
				break;
			default:
				$cacheName = $name;
				$binding = 'CHUNK';
				break;
		}

		if ($chunk = $this->getStore($cacheName, 'chunk')) {
			return $chunk;
		}

		$id = 0;
		$properties = array();
		/** @var modChunk $element */
		switch ($binding) {
			case 'INLINE':
				$element = $this->modx->newObject('modChunk', array('name' => $cacheName));
				$content = str_replace('@INLINE', '', $name);
				$element->setContent($content);
				break;
			case 'CHUNK':
				if ($element = $this->modx->getObject('modChunk', array('name' => $name))) {
					$content = $element->getContent();
					$id = $element->get('id');
				}
				break;
		}

		if (!$element) {
			return false;
		}

		// Preparing special tags
		if (!empty($this->config['nestedChunkPrefix']) AND strpos($content, '<!--' . $this->config['nestedChunkPrefix']) !== false) {
			preg_match_all('/\<!--' . $this->config['nestedChunkPrefix'] . '(.*?)[\s|\n|\r\n](.*?)-->/s', $content, $matches);
			$src = $dst = $placeholders = array();
			foreach ($matches[1] as $k => $v) {
				$src[] = $matches[0][$k];
				$dst[] = '';
				$placeholders[$v] = $matches[2][$k];
			}

			if (!empty($src) AND !empty($dst)) {
				$content = str_replace($src, $dst, $content);
			}
		} else {
			$placeholders = array();
		}

		$chunk = array(
			'object'       => $element,
			'content'      => $content,
			'placeholders' => $placeholders,
			'properties'   => $properties,
			'name'         => $cacheName,
			'id'           => $id,
			'binding'      => strtolower($binding),
		);

		$this->setStore($cacheName, $chunk, 'chunk');

		return $chunk;
	}


	/**
	 * This algorithm taken
	 * https://github.com/bezumkin/pdoTools/blob/badd69699eeb39579ac020e02c7207b6ff880c46/core/components/pdotools/model/pdotools/pdotools.class.php#L337
	 *
	 * @param string $name
	 * @param array  $properties
	 * @param bool   $fastMode
	 *
	 * @return mixed|string
	 */
	public function getChunk($name = '', array $properties = array(), $fastMode = false)
	{
		$name = trim($name);
		if (!empty($name)) {
			$chunk = $this->loadChunk($name, $properties);
		}
		if (empty($name) OR empty($chunk) OR !(isset($chunk['object']) AND $chunk['object'] instanceof modChunk)) {
			return !empty($properties)
				? str_replace(array('[', ']', '`'), array('&#91;', '&#93;', '&#96;'), htmlentities(print_r($properties, true), ENT_QUOTES, 'UTF-8'))
				: '';
		}
		$properties = array_merge($chunk['properties'], $properties);
		$content = $this->process($chunk, $properties);

		if (strpos($content, '[[') !== false) {
			// Processing quick placeholders
			if (!empty($chunk['placeholders'])) {
				$properties = $this->flattenArray($properties);
				$pl = $chunk['placeholders'];
				foreach ($pl as $k => $v) {
					if ($k[0] == '!') {
						if (empty($properties[substr($k, 1)])) {
							$pl[substr($k, 1)] = $v;
						}
						unset($pl[$k]);
					} elseif (empty($properties[$k])) {
						$pl[$k] = '';
					}
				}
				if (!empty($pl)) {
					$pl = $this->makePlaceholders($pl);
					$content = str_replace($pl['pl'], $pl['vl'], $content);
				}
			}
			// Processing given placeholders
			if (!empty($properties)) {
				$pl = $this->makePlaceholders($properties);
				$content = str_replace($pl['pl'], $pl['vl'], $content);
			}
		}
		// Processing other placeholders
		if (strpos($content, '[[') !== false) {
			if ($fastMode) {
				$content = $this->fastProcess($content, true);
			} else {
				$chunk['object']->_cacheable = false;
				$chunk['object']->_processed = false;
				$chunk['object']->_content = '';
				/** @var $chunk modChunk[] */
				$content = $chunk['object']->process($properties, $content);
			}
		}

		return $content;

	}

	/**
	 * This algorithm taken from
	 * https://github.com/bezumkin/pdoTools/blob/badd69699eeb39579ac020e02c7207b6ff880c46/core/components/pdotools/model/pdotools/pdotools.class.php#L413
	 *
	 * @param string $name
	 * @param array  $properties
	 * @param string $prefix
	 * @param string $suffix
	 *
	 * @return mixed|string
	 */
	public function parseChunk($name = '', array $properties = array(), $prefix = '[[+', $suffix = ']]')
	{
		$name = trim($name);
		/** @var array $chunk */
		if (!empty($name)) {
			$chunk = $this->loadChunk($name, $properties);
		}
		if (empty($name) OR empty($chunk['content'])) {
			return !empty($properties)
				? str_replace(array('[', ']', '`'), array('&#91;', '&#93;', '&#96;'), htmlentities(print_r($properties, true), ENT_QUOTES, 'UTF-8'))
				: '';
		}
		$properties = array_merge($chunk['properties'], $properties);
		$content = $this->process($chunk, $properties);
		if (strpos($content, '[[') !== false) {
			$pl = $this->makePlaceholders($properties, '', $prefix, $suffix);
			$content = str_replace($pl['pl'], $pl['vl'], $content);
		}

		return $content;
	}

	/**
	 * @param      $content
	 * @param bool $processUncacheable
	 *
	 * @return mixed
	 */
	public function fastProcess($content, $processUncacheable = true)
	{
		$tags = array();
		$this->getParser()->collectElementTags($content, $tags);
		$unprocessed = $pl = $vl = array();
		foreach ($tags as $tag) {
			$tmp = $this->parser->processTag($tag, $processUncacheable);
			if ($tmp === $tag[0]) {
				$unprocessed[] = $tmp;
			} else {
				$pl[] = $tag[0];
				$vl[] = $tmp;
			}
		}
		$content = str_replace($pl, $vl, $content);
		$content = str_replace($unprocessed, '', $content);

		return $content;
	}


	/**
	 * Sets data to cache
	 *
	 * @param mixed $data
	 * @param mixed $options
	 *
	 * @return string $cacheKey
	 */
	public function setCache($data = array(), $options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
			$this->modx->cacheManager->set(
				$cacheKey,
				$data,
				$cacheOptions[xPDO::OPT_CACHE_EXPIRES],
				$cacheOptions
			);
		}

		return $cacheKey;
	}

	/**
	 * Returns data from cache
	 *
	 * @param mixed $options
	 *
	 * @return mixed
	 */
	public function getCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cached = '';
		if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
			$cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
		}

		return $cached;
	}

	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public function clearCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey)) {
			$cacheOptions['cache_key'] .= $cacheKey;
		}
		if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
			return $this->modx->cacheManager->clean($cacheOptions);
		}

		return false;
	}

	/**
	 * Returns array with options for cache
	 *
	 * @param $options
	 *
	 * @return array
	 */
	protected function getCacheOptions($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		$cacheOptions = array(
			xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
				? 'default'
				: 'default/' . $this->namespace . '/',
			xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
				? $options['cache_handler']
				: $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
			xPDO::OPT_CACHE_EXPIRES => (isset($options['cacheTime']) AND $options['cacheTime'] !== '')
				? (integer)$options['cacheTime']
				: (integer)$this->modx->getOption('cache_resource_expires', null, 0),
		);

		return $cacheOptions;
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 *
	 * @return bool|string
	 */
	public function getCacheKey($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		if (!empty($options['cache_key'])) {
			return $options['cache_key'];
		}
		$key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

		return $key . '/' . sha1(serialize($options));
	}

	/**
	 * @param        $name
	 * @param        $object
	 * @param string $type
	 */
	public function setStore($name, $object, $type = 'data')
	{
		$this->store[$type][$name] = $object;
	}

	/**
	 * @param        $name
	 * @param string $type
	 *
	 * @return null
	 */
	public function getStore($name, $type = 'data')
	{
		return isset($this->store[$type][$name]) ? $this->store[$type][$name] : null;
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array
	 */
	public function explodeAndClean($array, $delimiter = ',')
	{
		$array = explode($delimiter, $array);     // Explode fields to array
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		return $array;
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array|string
	 */
	public function cleanAndImplode($array, $delimiter = ',')
	{
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		$array = implode($delimiter, $array);

		return $array;
	}

	/**
	 * @param array  $array
	 * @param string $prefix
	 *
	 * @return array
	 */
	public function flattenArray(array $array = array(), $prefix = '')
	{
		$outArray = array();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
			} else {
				$outArray[$prefix . $key] = $value;
			}
		}

		return $outArray;
	}

	/**
	 * Transform array to placeholders
	 *
	 * from
	 * https://github.com/bezumkin/pdoTools/blob/56f66c3a18dfb894e3a4aafdc1a4e36973e14ac3/core/components/pdotools/model/pdotools/pdotools.class.php#L282
	 *
	 * @param array  $array
	 * @param string $plPrefix
	 * @param string $prefix
	 * @param string $suffix
	 * @param bool   $uncacheable
	 *
	 * @return array
	 */
	public function makePlaceholders(array $array = array(), $plPrefix = '', $prefix = '[[+', $suffix = ']]', $uncacheable = true)
	{
		$result = array('pl' => array(), 'vl' => array());
		$uncached_prefix = str_replace('[[', '[[!', $prefix);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
			} else {
				$pl = $plPrefix . $k;
				$result['pl'][$pl] = $prefix . $pl . $suffix;
				$result['vl'][$pl] = $v;
				if ($uncacheable) {
					$result['pl']['!' . $pl] = $uncached_prefix . $pl . $suffix;
					$result['vl']['!' . $pl] = $v;
				}
			}
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function clearTwiggyCache()
	{
		$folder = rtrim(trim($this->getOption('cache', $this->config, MODX_CORE_PATH . 'cache/default/twiggy/', true)), 'cache/') . '/';

		return $this->modx->cacheManager->deleteTree($folder, array('deleteTop' => true, 'extensions' => array('.string.php', '.cache.php', '.php')));
	}

}