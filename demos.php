<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html class="dj_webkit dj_chrome dj_contentbox" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title>The Dojo Toolkit - Demos Index</title>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Demo for The Dojo Toolkit, dojo, JavaScript Framework" />
	<meta name="description" content="The Dojo Toolkit Demo Index" />
	<meta name="author" content="Dojo Foundation" />
	<meta name="copyright" content="Copyright 2006-2011 by the Dojo Foundation" />
	<meta name="company" content="Dojo Foundation" />
	
	<link rel="shortcut icon" href="http://dojotoolkit.org/dojango/dojo-media/release/1.4.0-20100212/dtk/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="resources/demos.css" type="text/css" media="all" />

	<script src="../dojo/dojo.js" data-dojo-config="async: true"></script>
	<script>
	require(["dojo/ready", "dojo/query", "dojo/on", "dojo/mouse", "dojo/_base/fx", "dojo/NodeList-dom"],
		function(ready, query, on, mouse, fx){
		ready(function(){

			query("body").removeClass("no-js");

			var list = query("#mainlist li");
			var props = {
				i: { width:128, height:128, top:-16, left:-136 },
				o: { width:96, height:96, top:0, left:-120 }
			};

			list.forEach(function(n){

				var img = query("img", n)[0], a;
				on(n, mouse.enter, function(e){
					a && a.stop();
					a = fx.anim(img, props.i, 175);
				});

				on(n, mouse.leave, function(e){
					a && a.stop();
					a = fx.anim(img, props.o, 175, null, null, 75);
				});

			});
		});
	});
	</script>
</head>
<body class="claro no-js">
		
	<div class="accessibility">
		<a href="#intro">Skip to Content</a>
		|
		<a href="#nav">Skip to Navigation</a>
	</div>
	<hr class="hide" />
	<div id="page" class="homePage">
		<div id="header">
			<div class="container">
				<span id="logo"><a href="http://dojotoolkit.org/" title="Dojo Homepage"><img src="http://dojotoolkit.org/images/logo.png" alt="Dojo Toolkit" /></a></span>
				<ul id="navigation">
					<li class="download"><a href="http://dojotoolkit.org/download/">Download</a></li>
					<li class="features"><a href="http://dojotoolkit.org/features/">Features</a></li>
					<li class="docs"><a href="http://dojotoolkit.org/documentation/">Documentation</a></li>
					<li class="community"><a href="http://dojotoolkit.org/community/">Community</a></li>
					<li class="blog"><a href="http://dojotoolkit.org/blog/">Blog</a></li>
				</ul>
				<form method="GET" action="http://www.google.com/search" id="search">
					<span><input type="text" name="q" id="query" value="Search"></input>
					<button type="submit">Search</buytton>
					<div id="resultbox" style="display:none">
						<div class="googleheader"></div>
						<div id="googlesearch"></div>
						<div id="searchClose">
							<a>Close</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<hr class="hide" />
		<div id="intro">
			<div class="innerBox">
			<h1>Demo Index</h1>
			<!-- end content header -->
					<?php

					// holder for all the items
					$out = array();

					// load the demos described in the resources/ folder that we link but don't ship
					if(file_exists("resources/ext_demos.json")){
						$ext = json_decode(file_get_contents("resources/ext_demos.json"));
						// is there no better obj->array thing I can do here?
						foreach($ext->external as $e){
							$out[] = array(
								"demo" => $e->demo,
								"link" => $e->link,
								"img" => $e->img,
								"rank" => $e->rank,
								"header" => $e->header,
								"categories" => explode(',', $e->categories)
							);
						}
					}

					$exclude = array("resources", ".", "..", ".svn");
					$files = scandir("./");

					foreach($files as $demo){

						if(is_dir($demo) && !in_array($demo, $exclude)){

							// setup some item information
							$title = $demo;
							$rank = 500;
							$base = $demo . "/";
							$link = $base . "demo.html";
							$categories = array("rich");

							$readme = $base . "README";
							if(file_exists($readme)){

								// the second line of the README is the title
								$l = file($readme);
								$title = $l[1];

								// demos with README's are better than those without.
								$rank++;

								// the last line of the README is supposed to be tags etc
								// 
								// so far only @rank:### is used, but can be any @key:value pair on one line
								// to be used here for organization etc
								$tagline = $l[count($l)-1];
								preg_match_all("/@(\w+):([\-a-zA-Z0-9,]+)\ ?/", $tagline, $matches);
								if(is_array($matches[1]) && is_array($matches[2])){
									$tags = array_combine($matches[1], $matches[2]);
								}else{
									$tags = array();
								}

								switch($tags['rank']){
									// marked experimental
									case -999 : $rank = 0; break;
									// add the README rank to the overall score
									default: $rank += $tags['rank']; break;
								}

								if(array_key_exists('categories', $tags)){
									$categories = explode(',', $tags['categories']);
								}

								// with a thumbnail, they are higher ranked too
								$thumb_img = "resources/images/" . $demo . ".png";
								if(file_exists($thumb_img)){
									$rank += 20;
								}else{
									$thumb_img = "resources/images/no_thumb.gif"; // generic dojo img
								}

							}else{

								// experimental demos:
								$rank = 0;
								$categories = array("rich");
								$thumb_img = false;

							}

							// push this item
							$out[] = array(
								"demo" => $demo,
								"header" => $title,
								"link" => $link,
								"rank" => $rank,
								"img" => $thumb_img,
								"categories" => $categories
							);

						}

					}

					// sort the out array by the ranks key
					foreach ($out as $key => $row) {
						$ranks[$key]  = $row['rank'];
						$d[$key] = $row;
					}
					array_multisort($ranks, SORT_DESC, $d, SORT_ASC, $out);

					print "<h2>Graphics & Charting<h2>";
					// generate the 1st category list:
					print "<ul id='mainlist'>";
					foreach($out as $ranked){
						if(in_array("graphics", $ranked['categories'])){
							// generate the demo item
							print "\n\t<li><a href='".$ranked['link']."'>";
							if($ranked['img']){
								print "<img src='". $ranked['img'] . "' />";
							}

							// split the title in two parts around the first hyphen
							list($anchor, $desc) = explode("-", $ranked['header'], 2);
							print $anchor;
							if($desc){
								print "<span>" .$desc. "</span>";
							}
							print "</a></li>";
						}
					}
					print "</ul>";
					print "<h2>Mobile<h2>";
					// generate the 2nd category list:
					print "<ul id='mainlist'>";
					foreach($out as $ranked){
						if(in_array("mobile", $ranked['categories'])){
							// generate the demo item
							print "\n\t<li><a href='".$ranked['link']."'>";
							if($ranked['img']){
								print "<img src='". $ranked['img'] . "' />";
							}

							// split the title in two parts around the first hyphen
							list($anchor, $desc) = explode("-", $ranked['header'], 2);
							print $anchor;
							if($desc){
								print "<span>" .$desc. "</span>";
							}
							print "</a></li>";
						}
					}
					print "</ul>";
					print "<h2>Rich WebApps<h2>";
					// generate the list:
					print "<ul id='mainlist'>";
					$in_experimental = false;
					foreach($out as $ranked){
						if($ranked['rank'] === 0 && !$in_experimental){
							// we're done with top demos, close list and make a new one
							$in_experimental = true;
							print "</ul><br class='clear'>";
							print "<h2>Incomplete / Partial Demos:</h2>";
							print "<ul id='explist'>";
						}

						if(in_array("rich", $ranked['categories']) || $in_experimental){
							// generate the demo item
							print "\n\t<li><a href='".$ranked['link']."'>";
							if($ranked['img']){
								print "<img src='". $ranked['img'] . "' />";
							}

							// split the title in two parts around the first hyphen
							// some experimental demos do not have header
							if(strpos($ranked['header'], "-")){
								list($anchor, $desc) = explode("-", $ranked['header'], 2);
								print $anchor;
								if($desc){
									print "<span>" .$desc. "</span>";
								}
							}else{
								print $ranked['header'];
							}
							print "</a></li>";
						}
					}
					print "</ul>";

				?>
			<!-- 
				basic page onload script after dojo.js [if available] - degrades gracefullly 
				though none of the demos will "work" without JavaScript enabled / dojo.js
			-->
			<!-- begin footer -->
			</div>
		</div>
		<div id="main">
			<div id="content" class="innerBox">
				<div id="foot">
					<div class="innerBox">
							<span class="redundant">&copy;</span> <a href="http://www.dojofoundation.org">The Dojo Foundation</a>, All Rights Reserved.
						
					</div>
				</div>
			</div>

		</div>
		<hr class="hide" />

	</div>
	</body>
</html>

