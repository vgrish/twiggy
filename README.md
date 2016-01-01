## twiggy

## TEMPLATES ##

Loading templates from database and file system.
Add extension and template inheritance.

* base template

```
<!DOCTYPE html> 
<html>
    <head>
        {% block head %}
            <link rel="stylesheet" href="style.css" />
            <title>{% block title %}{% endblock %} - Мой сайт</title>
        {% endblock %}
    </head>
    <body>
        <div id="content">{% block content %}{% endblock %}</div>
        <div id="footer">
            {% block footer %}
                &copy; Copyright 2013 <a href="http://example.com/">Вы</a>.
            {% endblock %}
        </div>
    </body>
</html>
```

* inherited template

```
{% extends "base" %}

{% block title %}Главная{% endblock %}
{% block head %}
    {{ parent() }}
    <style type="text/css">
        .important { color: #336699; }
    </style>
{% endblock %}
{% block content %}
    <h1>Главная</h1>
    <p class="important">
        Приветсвую на своем потрясном сайте!
    </p>
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

* get - 
* post - 
* session - 
* cookie - 
* request - 
* files - 
* server - 
* option - 
* lexicon -
* pls - 
* url -
* toJson - 
* fromJson - 
* toArray - 
* field - 

```
{{ 'modx.mgr.user.token'|session }}
{{ 'site_name'|option }}
{{ '+upload_images'|pls }}
{{ '[[*id]]'|url('','',1) }}
{{ dump(modx.getObject('modUserProfile', 1)|toArray) }}
 
```

* Functions

* chunk - 
* snippet - 
* processor - 

```
{{ chunk('@INLINE [[+name]]',{'name':'Володя Володин'}) }}
{{ snippet('pdoNeighbors') }}

{% set response = processor('mgr/valute/getlist',{'ns':'currencyrate', 'sortdir':'asc'}) %}
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