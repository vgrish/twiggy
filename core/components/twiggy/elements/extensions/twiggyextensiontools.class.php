<?php

class TwiggyExtensionTools extends Twig_Extension
{
    /** @var MODx $modx */
    static private $modx;
    /** @var Twiggy $twiggy */
    static private $twiggy;

    /**
     * @param Twiggy $twiggy
     */
    public function __construct(Twiggy &$twiggy)
    {
        self::$twiggy = &$twiggy;
        self::$modx = &$twiggy->modx;
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
    public static function filterToArray(
        Twig_Environment $env,
        $instance,
        $keyPrefix = '',
        $rawValues = false,
        $excludeLazy = false,
        $includeRelated = false
    ) {
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
    public static function filterGetField(
        Twig_Environment $env,
        $instance,
        $key,
        $format = null,
        $formatTemplate = null
    ) {
        if ($instance instanceof xPDOObject) {
            if (isset($instance->_fieldMeta[$key])) {
                return $instance->get($key, $format, $formatTemplate);
            }
        }

        return null;
    }

    /**
     * @param Twig_Environment $env
     * @param                  $var
     * @param null             $type
     * @param null             $class
     *
     * @return bool
     */
    public static function filterGetType(
        Twig_Environment $env,
        $var,
        $type = null,
        $class = null
    ) {

        switch ($type) {
            case 'bool':
                return filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
            case 'float':
                return filter_var($var, FILTER_VALIDATE_FLOAT) !== false;
            case 'int':
                return filter_var($var, FILTER_VALIDATE_INT) !== false;
            case 'email':
                return filter_var($var, FILTER_VALIDATE_EMAIL) !== false;
            case 'ip':
                return filter_var($var, FILTER_VALIDATE_IP) !== false;
            case 'regexp':
                return filter_var($var, FILTER_VALIDATE_REGEXP) !== false;
            case 'url':
                return filter_var($var, FILTER_VALIDATE_URL) !== false;

            case 'numeric':
                return is_numeric($var);
            case 'array':
                return is_array($var);
            case 'class':
                return is_object($var) === true AND get_class($var) === $class;
            case 'object':
                return is_object($var);
            case 'scalar':
                return is_scalar($var);
            case 'string':
                return is_string($var);

            default:
                return false;
        }
    }

    /**
     * @param     $log
     */
    public static function log($log)
    {
        self::$modx->log(modX::LOG_LEVEL_ERROR, print_r($log, 1));
    }

    /**
     * @param $log
     */
    public static function varDump($log)
    {
        echo "<pre class=\"twiggy var_dump\">\n";
        var_dump($log);
        echo "</pre>";
    }

    /**
     * @param string $key
     * @param bool   $string
     *
     * @return array|string
     */
    public static function getInfo($key = '', $string = true)
    {
        $totalTime = (microtime(true) - self::$modx->startTime);
        $queryTime = self::$modx->queryTime;
        $queries = isset (self::$modx->executedQueries) ? self::$modx->executedQueries : 0;
        $phpTime = $totalTime - $queryTime;
        $queryTime = sprintf("%2.4f s", $queryTime);
        $totalTime = sprintf("%2.4f s", $totalTime);
        $phpTime = sprintf("%2.4f s", $phpTime);
        $source = self::$modx->resourceGenerated ? "database" : "cache";

        $info = array(
            'totalTime' => $totalTime,
            'queryTime' => $queryTime,
            'phpTime'   => $phpTime,
            'queries'   => $queries,
            'source'    => $source
        );

        if (empty($key) AND $string) {
            foreach ($info as $k => $v) {
                $info[$k] = $k . ' : ' . $v;
            }
        }

        return isset($info[$key]) ? $info[$key] : ($string ? implode("\r\n", $info) : $info);
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
    public static function toArray(
        $instance,
        $keyPrefix = '',
        $rawValues = false,
        $excludeLazy = false,
        $includeRelated = false
    ) {
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
     * @param       $name
     * @param array $properties
     *
     * @return mixed|string
     */
    public static function getChunk($name, array $properties = array(), $fastMode = false)
    {
        return self::$twiggy->getChunk($name, $properties, $fastMode);
    }

    /**
     * @param string $name
     * @param array  $properties
     * @param string $prefix
     * @param string $suffix
     *
     * @return mixed|string
     */
    public static function parseChunk($name = '', array $properties = array(), $prefix = '[[+', $suffix = ']]')
    {
        return self::$twiggy->parseChunk($name, $properties, $prefix, $suffix);
    }

    /**
     * @param       $name
     * @param array $properties
     *
     * @return mixed|string
     */
    public static function runSnippet($name, array $properties = array())
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
    public static function runProcessor($action = '', array $properties = array())
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


    /**
     * @param null  $id
     * @param int   $depth
     * @param array $options
     *
     * @return array
     */
    public static function getChildIds($id = null, $depth = 10, array $options = array())
    {
        return self::$modx->getChildIds($id, $depth, $options);
    }

    /**
     * @param null  $id
     * @param int   $height
     * @param array $options
     *
     * @return array
     */
    public static function getParentIds($id = null, $height = 10, array $options = array())
    {
        return self::$modx->getParentIds($id, $height, $options);
    }

    /**
     * @param      $groups
     * @param bool $matchAll
     *
     * @return bool
     */
    public static function isMember($groups, $matchAll = false)
    {
        return self::$modx->user->isMember($groups, $matchAll);
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public static function isAuthenticated($context = 'web')
    {
        return self::$modx->user->isAuthenticated($context);
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public static function hasSessionContext($context = 'web')
    {
        return self::$modx->user->hasSessionContext($context);
    }

    /**
     * @param $pm
     *
     * @return bool
     */
    public static function hasPermission($pm)
    {
        return self::$modx->hasPermission($pm);
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
            new Twig_SimpleFilter('toJson', 'TwiggyExtensionTools::filterToJson', array(
                'needs_environment' => true
            )),
            new Twig_SimpleFilter('fromJson', 'TwiggyExtensionTools::filterFromJson', array(
                'needs_environment' => true
            )),
            new Twig_SimpleFilter('toArray', 'TwiggyExtensionTools::filterToArray', array(
                'needs_environment' => true
            )),
            new Twig_SimpleFilter('field', 'TwiggyExtensionTools::filterGetField', array(
                'needs_environment' => true
            )),

            new Twig_SimpleFilter('typeOf', 'TwiggyExtensionTools::filterGetType', array(
                'needs_environment' => true
            )),

            new Twig_SimpleFilter('md5', 'md5'),

        );
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('log', 'TwiggyExtensionTools::log'),
            new Twig_SimpleFunction('varDump', 'TwiggyExtensionTools::varDump'),
            new Twig_SimpleFunction('getInfo', 'TwiggyExtensionTools::getInfo'),

            new Twig_SimpleFunction('getOption', 'TwiggyExtensionTools::getOption'),

            new Twig_SimpleFunction('loadLexicon', 'TwiggyExtensionTools::loadLexicon'),
            new Twig_SimpleFunction('lexicon', 'TwiggyExtensionTools::lexicon'),
            new Twig_SimpleFunction('_', 'TwiggyExtensionTools::lexicon'),

            new Twig_SimpleFunction('makeUrl', 'TwiggyExtensionTools::makeUrl'),
            new Twig_SimpleFunction('toJson', 'TwiggyExtensionTools::toJson'),
            new Twig_SimpleFunction('fromJson', 'TwiggyExtensionTools::fromJson'),
            new Twig_SimpleFunction('toArray', 'TwiggyExtensionTools::toArray'),

            new Twig_SimpleFunction('getField', 'TwiggyExtensionTools::getField'),
            new Twig_SimpleFunction('getCount', 'TwiggyExtensionTools::getCount'),
            new Twig_SimpleFunction('getObject', 'TwiggyExtensionTools::getObject'),

            new Twig_SimpleFunction('sendError', 'TwiggyExtensionTools::sendError'),
            new Twig_SimpleFunction('sendRedirect', 'TwiggyExtensionTools::sendRedirect'),
            new Twig_SimpleFunction('sendForward', 'TwiggyExtensionTools::sendForward'),

            new Twig_SimpleFunction('setPlaceholder', 'TwiggyExtensionTools::setPlaceholder'),
            new Twig_SimpleFunction('setPls', 'TwiggyExtensionTools::setPlaceholder'),

            new Twig_SimpleFunction('toPlaceholder', 'TwiggyExtensionTools::toPlaceholder'),
            new Twig_SimpleFunction('toPls', 'TwiggyExtensionTools::toPlaceholder'),

            new Twig_SimpleFunction('getPlaceholder', 'TwiggyExtensionTools::getPlaceholder'),
            new Twig_SimpleFunction('getPls', 'TwiggyExtensionTools::getPlaceholder'),

            new Twig_SimpleFunction('unsetPlaceholder', 'TwiggyExtensionTools::unsetPlaceholder'),
            new Twig_SimpleFunction('unsetPls', 'TwiggyExtensionTools::unsetPlaceholder'),

            new Twig_SimpleFunction('setPlaceholders', 'TwiggyExtensionTools::setPlaceholders'),
            new Twig_SimpleFunction('toPlaceholders', 'TwiggyExtensionTools::toPlaceholders'),
            new Twig_SimpleFunction('getPlaceholders', 'TwiggyExtensionTools::getPlaceholders'),
            new Twig_SimpleFunction('unsetPlaceholders', 'TwiggyExtensionTools::unsetPlaceholders'),

            new Twig_SimpleFunction('getChunk', 'TwiggyExtensionTools::getChunk'),
            new Twig_SimpleFunction('parseChunk', 'TwiggyExtensionTools::parseChunk'),

            new Twig_SimpleFunction('runSnippet', 'TwiggyExtensionTools::runSnippet'),
            new Twig_SimpleFunction('runProcessor', 'TwiggyExtensionTools::runProcessor'),

            new Twig_SimpleFunction('user', 'TwiggyExtensionTools::user'),
            new Twig_SimpleFunction('profile', 'TwiggyExtensionTools::profile'),
            new Twig_SimpleFunction('resource', 'TwiggyExtensionTools::resource'),

            new Twig_SimpleFunction('isMember', 'TwiggyExtensionTools::isMember'),
            new Twig_SimpleFunction('isAuthenticated', 'TwiggyExtensionTools::isAuthenticated'),
            new Twig_SimpleFunction('hasSessionContext', 'TwiggyExtensionTools::hasSessionContext'),
            new Twig_SimpleFunction('hasPermission', 'TwiggyExtensionTools::hasPermission'),

            new Twig_SimpleFunction('getChildIds', 'TwiggyExtensionTools::getChildIds'),
            new Twig_SimpleFunction('getParentIds', 'TwiggyExtensionTools::getParentIds'),

        );
    }

}
