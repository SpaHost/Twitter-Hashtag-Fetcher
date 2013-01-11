<?php
//
// Voxtours España
// Autor       : Lorenzo J Gonzalez
// Fecha       : 2011 - 2012
// Web         : http://www.spahost.es
// Email       : soporte@spahost.es
//

// Definicion de seguridad
if(!defined('twitfetch_')) die('No esta permitido acceder a esta pagina.');

function getTweets($hash_tag) {
  $url = 'http://search.twitter.com/search.atom?q='.urlencode($hash_tag).'&result_type=recent' ;
  $ch = curl_init($url);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $xml = curl_exec ($ch);
  $affected = 0;
  $twelement = new SimpleXMLElement($xml);

    
  $i = 0;
  foreach ($twelement->entry as $entry) {
    $text = trim($entry->title);
    $author = trim($entry->author->name);
    $uri = trim($entry->author->uri);
    $time = strtotime($entry->published);
    $getid = explode(',', $entry->id);
    $id = $getid['1'];

  // 1.- Preg_replace for link
  // 2.- Preg_replace for mention
  // 3.- Preg_replace for hashtag
  $text = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $text);
  $text = preg_replace("/@(\w+)/i", "<a href=\"http://twitter.com/$1\">$0</a>", $text);
  $text = preg_replace("/#(\w+)/i", "<a href=\"https://twitter.com/search?q=%23$1&src=hash\">$0</a>", $text);

  // Modificar a tu gusto
  echo '
    <div class="row">
      <blockquote class="pull-left">
        <p><t6>',$text,'</t6></p>
        <small><a href="',$uri,'">',$author,'</a> - ',date('n/j/y g:i a',$time),'</small>
      </blockquote>
    </div>';

  if (++$i == $limite_twiites) break;
  }

return true ;
}

// Doctype y Charset
echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="generator" content="SpaHost - Copyright (C) 2013. All rights reserved.">
  <meta name="author" content="SpaHost">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <title>Twitter Hashtag Fetcher</title>
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <link rel="stylesheet" media="screen" href="/css/bootstrap.min.css" type="text/css">
  <link rel="stylesheet" media="screen" href="/css/bootstrap-responsive.min.css" type="text/css">
  <style type="text/css">
  t6 { margin: 10px 0; font-family: inherit; font-weight: bold; line-height: 20px; color:#999999; text-rendering: optimizelegibility; font-size: 11.9px; }
  </style>
</head>
<body>
<div class="container">
  <div class="span12">
    <div class="page-header">
      <h1>Twitter Hashtag Fetcher</h1>
    </div>';

// Llamamos la funcion con getTweets();
if ($_REQUEST[hash]) {
  $laid = '#'.$_REQUEST[hash];
  getTweets($laid);
} else {
  $laid = '#SpaHost';
  getTweets($laid);
}

// Fin codigo
echo '
  </div>
</div>
</body>
</html>';

?>