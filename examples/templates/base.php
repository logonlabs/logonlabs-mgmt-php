<?php

$id_count = 0;

function pageHeader($title, $url = 'index') {
    $ret = "<!doctype html>
  <html>
  <head>
    <title>" . $title . "</title>
    <link href='styles/style.css' rel='stylesheet' type='text/css' />
  </head>
  <script src=\"dist/json-formatter.umd.js\"></script>
  <script>
    var rendering = function(target, json) {
        var el, formatter;
        el = document.getElementById(target);
        formatter = new JSONFormatter(json, 1, {
            theme: 'dark'
        });
        el.appendChild(formatter.render());
    };
  </script>
  <body>\n";
    $self = explode('/', $_SERVER['PHP_SELF']);
    $current = $self[count($self) - 1];
    if ($current != "index.php") {
        $ret .= "<p><a href='" . $url . ".php'>Back</a></p>";
    }
    $ret .= "<header><h1>" . $title . "</h1></header>";

    // Start the session (for storing access tokens and things)
    if (!headers_sent()) {
        session_start();
    }

    return $ret;
}

function pageRequire() {
    $ret = '';

    $ret .= '<h2>Missing parameters in the config.ini </h2>';
    $ret .= '<div>Please put in username and password in the config.ini.</div>';
    $ret .= '<p>example:</p>';
    $ret .= '<pre><code>username="username@example.com"
password="random_password"
</code></pre>';
    return $ret;
}

function apiResult($json, $title = null) {
    global $id_count;
    $render_target = 'result' . $id_count++;
    $ret = "";
    if ($title) {
        $ret .= "<label>" . $title . "</label>";
    }
    $ret .= "<pre class='json'><code id='" . $render_target . "'></code></pre>";

    $ret .= "<script>
    results = " . json_encode($json) . ";
    rendering('" . $render_target . "', results);
    </script>
    ";

    return $ret;
}

function pageFooter($file = null) {
    $ret = "";
    if ($file) {
        $ret .= "<h3>Code:</h3>";
        $ret .= "<pre class='code'>";
        $ret .= htmlspecialchars(file_get_contents($file));
        $ret .= "</pre>";
    }
    $ret .= "</html>";

    return $ret;
}

