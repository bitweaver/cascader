<?php
/**
 * @version      $Header: /cvsroot/bitweaver/_bit_cascader/properties.php,v 1.4 2008/06/19 07:00:56 lsces Exp $
 *
 * @author       xing  <xing@synapse.plus.com>
 * @copyright    2003-2006 bitweaver
 * @package      cascader
 * @subpackage   functions
 */

/**
 * Initialize
 */
// list of availabe properties that can be set
// array key needs to be unique using only [A-Za-z_]
$properties = array(
	"body" => array(
		"title" => "Body background",
		"selector" => "body",
		"property" => "background-color",
		"group" => "core",
	),
	"font" => array(
		"title" => "Font color",
		"selector" => "body",
		"property" => "color",
		"group" => "core",
	),
	"link" => array(
		"title" => "Link color",
		"selector" => ":link,\n:visited",
		"property" => "color",
		"group" => "advanced",
	),
	"linkh" => array(
		"title" => "Link hover background",
		"selector" => ":link:hover,\n:visited:hover",
		"property" => "background-color",
		"group" => "advanced",
	),
	"container" => array(
		"title" => "#container",
		"selector" => "#container",
		"property" => "background-color",
		"group" => "core",
	),
	"header" => array(
		"title" => "#header",
		"selector" => "#header",
		"property" => "background-color",
		"group" => "core",
	),
	"wrapper" => array(
		"title" => "#wrapper",
		"selector" => "#wrapper",
		"property" => "background-color",
		"group" => "core",
	),
	"content" => array(
		"title" => "#content",
		"selector" => "#content .display,\n#content .admin,\n#content .listing,\n#content .edit",
		"property" => "background-color",
		"group" => "core",
	),
	"navigation" => array(
		"title" => "#navigation",
		"selector" => "#navigation",
		"property" => "background-color",
		"group" => "core",
	),
	"extra" => array(
		"title" => "#extra",
		"selector" => "#extra",
		"property" => "background-color",
		"group" => "core",
	),
	"footer" => array(
		"title" => "#footer",
		"selector" => "#footer",
		"property" => "background-color",
		"group" => "core",
	),
	"odd" => array(
		"title" => ".odd items in listings",
		"selector" => ".odd",
		"property" => "background-color",
		"group" => "advanced",
	),
	"even" => array(
		"title" => ".even items in listings",
		"selector" => ".even",
		"property" => "background-color",
		"group" => "advanced",
	),
	"module" => array(
		"title" => ".module in side columns",
		"selector" => ".module",
		"property" => "background-color",
		"group" => "advanced",
	),
);
?>
