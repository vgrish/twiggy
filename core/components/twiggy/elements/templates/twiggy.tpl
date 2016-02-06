<!DOCTYPE html>
<html>
<head>
    {% block head %}
    <link rel="stylesheet" href="style.css"/>
    <title>{% block title %}{% endblock %} - Мой сайт</title>
    {% endblock %}
</head>
<body>
<div id="content">{% block content %}{% endblock %}</div>
<div id="footer">
    {% block footer %}
    &copy; Copyright 2015 <a href="http://example.com/">Вы</a>.
    {% endblock %}
</div>
</body>
</html>