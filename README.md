## twiggy

## TEMPLATES ##

Loading templates from database and file system.
Add extension and template inheritance.

* base template

```
<!DOCTYPE html>
<html lang="en">
<head>
    {% block head %}
            {% include 'chunk|head' %}
    {% endblock %}
</head>
<body>
    {% block navbar %}
            {% include 'chunk|navbar' %}
    {% endblock %}
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                {% block content %}
                    {{ modx.resource.content }}
                {% endblock %}
            </div>
            <div class="col-md-2">
                {% block sidebar %}
                    Sidebar
                {% endblock %}
            </div>
        </div>
        {% block footer %}
            {% include 'chunk|footer' %}
        {% endblock %}
    </div>
</body>
</html>
```

* inherited template

```
{% extends "template|base" %}
{% block content %}
    <h3>{{ modx.resource.pagetitle }}</h3>
    <div class="jumbotron">
        {{ modx.resource.parent }}
    </div>
{% endblock %}
```

## CACHE ##

```
{% cache 'neighbors' 3000 %}
    {{ modx.runSnippet('pdoNeighbors') }}
{% endcache %}
```

## TOOLS ##

* Filters

* toJson - 
* fromJson - 
* toArray - 
* field - 

```
{{ dump(modx.getObject('modUserProfile', 1)|toArray) }}
 
```

* Functions

* log - 
* varDump - 
* getInfo -
* getOption - 
* loadLexicon -
* lexicon (_) - 

* makeUrl - 
* toJson - 
* fromJson - 
* toArray - 

* getField - 
* getCount - 
* getObject - 

* sendError - 
* sendRedirect - 
* sendForward - 

* setPlaceholder (setPls) - 
* toPlaceholder (toPls) - 
* getPlaceholder (getPls) - 
* unsetPlaceholder (unsetPls) - 

* getChunk - 
* parseChunk - 
* runSnippet - 
* runProcessor - 

* getChildIds - 
* getParentIds - 

```
{{ getCount('modUser') }}
{{ getObject('modUserProfile', {'email':'admin@mail.ru'})|toJson }}
{{ getChunk('@INLINE _['name']',{'name':'Володя Володин'}) }}
{{ runSnippet('pdoNeighbors') }}

{% set response = runProcessor('mgr/valute/getlist',{'ns':'currencyrate', 'sortdir':'asc'}) %}
{# { dump(response) }#}
{% for result in response.response.results %}
    <h5>{{ result.charcode }}</h5>
{% endfor %}

```

## PCRE ##

* preg_quote   - Quote regular expression characters
* preg_match   - Perform a regular expression match
* preg_get     - Perform a regular expression match and returns the matched group
* preg_get_all - Perform a regular expression match and return the group for all matches
* preg_grep    - Perform a regular expression match and return an array of entries that match the pattern
* preg_replace - Perform a regular expression search and replace
* preg_filter  - Perform a regular expression search and replace, returning only matched subjects.
* preg_split   - Split text into an array using a regular expression

```
{% if email|preg_match('/^.+@.+\\.\\w+$/') %}Email: {{ email }}{% endif %}
Website: {{ website|preg_replace('~^https?://~')
First name: {{ fullname|preg_get('/^\S+/') }}
<ul>
  {% for item in items|preg_split('/\s+/')|grep_filter('/-test$/', 'invert') %}
    <li>{{ item }}</li>
  {% endfor %}
</ul>
```

## Add Service ##

```
switch ($modx->event->name) {

    case 'twiggyOnTwigInit':

		if (!$twiggy = $modx->getOption('twiggy', $scriptProperties)) {
			return;
		}

		$fqn = $modx->getOption('modclassvar_class', null, 'modclassvar.modclassvar', true);
        $path = $modx->getOption('modclassvar_class_path', null,
            $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modclassvar/');
        if (!$modclassvar = $modx->getService($fqn, '', $path . 'model/',
            array('core_path' => $path))
        ) {
            return false;
        }

		$twiggy->twig->addGlobal('mcv', $modclassvar);

        break;
}
```