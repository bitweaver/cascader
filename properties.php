<?php
// list of availabe properties that can be set
// array key needs to be unique using only [A-Za-z_]
$properties = array(
	"body" => array(
		"title" => "Body background",
		"selector" => "body",
		"property" => "background-color",
		"group" => "basic",
	),
	"font" => array(
		"title" => "Font color",
		"selector" => "body",
		"property" => "color",
		"group" => "basic",
	),
	"link" => array(
		"title" => "Link color",
		"selector" => ":link,\n:visited",
		"property" => "color",
		"group" => "extra",
	),
	"linkh" => array(
		"title" => "Link hover background",
		"selector" => ":link:hover,\n:visited:hover",
		"property" => "background-color",
		"group" => "extra",
	),
	"container" => array(
		"title" => "#container",
		"selector" => "#container",
		"property" => "background-color",
		"group" => "basic",
	),
	"header" => array(
		"title" => "#header",
		"selector" => "#header",
		"property" => "background-color",
		"group" => "basic",
	),
	"wrapper" => array(
		"title" => "#wrapper",
		"selector" => "#wrapper",
		"property" => "background-color",
		"group" => "basic",
	),
	"content" => array(
		"title" => "#content",
		"selector" => "#content .display,\n#content .admin,\n#content .listing,\n#content .edit",
		"property" => "background-color",
		"group" => "basic",
	),
	"navigation" => array(
		"title" => "#navigation",
		"selector" => "#navigation",
		"property" => "background-color",
		"group" => "basic",
	),
	"extra" => array(
		"title" => "#extra",
		"selector" => "#extra",
		"property" => "background-color",
		"group" => "basic",
	),
	"footer" => array(
		"title" => "#footer",
		"selector" => "#footer",
		"property" => "background-color",
		"group" => "basic",
	),
	"odd" => array(
		"title" => ".odd items in listings",
		"selector" => ".odd",
		"property" => "background-color",
		"group" => "extra",
	),
	"even" => array(
		"title" => ".even items in listings",
		"selector" => ".even",
		"property" => "background-color",
		"group" => "extra",
	),
	"module" => array(
		"title" => ".module in side columns",
		"selector" => ".module",
		"property" => "background-color",
		"group" => "extra",
	),
);
?>
