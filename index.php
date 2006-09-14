<?php
require_once( '../bit_setup_inc.php' );
require_once( CASCADER_PKG_PATH.'Cascader.php' );

// Only admin can use this - for now
$gBitSystem->verifyPermission( 'p_admin' );

// make sure the package is active
$gBitSystem->verifyPackage( 'cascader' );

// We need to use the cascader style for this package
if( !empty( $_REQUEST['cascader_style'] ) ) {
	$gBitSystem->storeConfig( 'style', 'cascader', THEMES_PKG_NAME );
	$gPreviewStyle = 'cascader';
}

// This is appended to the css file link ensuring an up to date view on ever page load
$gBitSmarty->assign( 'refresh', '?refresh='.time() );

$feedback = array();
$gCascader = new Cascader( 'Scheme Name' );

// apply the style layout
if( !empty( $_REQUEST["site_style_layout"] ) ) {
	$gBitSystem->storeConfig( 'site_style_layout', ( ( $_REQUEST["site_style_layout"] != 'remove' ) ? $_REQUEST["site_style_layout"] : NULL ), THEMES_PKG_NAME );
}

$styleLayouts = $gBitThemes->getStyleLayouts();
$gBitSmarty->assign_by_ref( "styleLayouts", $styleLayouts );

// list of availabe properties that can be set
// array key needs to be unique using only [A-Za-z_]
$properties = array(
	"body" => array(
		"title" => "Body background",
		"selector" => "body",
		"property" => "background-color"
	),
	"font" => array(
		"title" => "Font color",
		"selector" => "body",
		"property" => "color"
	),
	"link" => array(
		"title" => "Link color",
		"selector" => ":link,:visited",
		"property" => "color"
	),
	"linkh" => array(
		"title" => "Link hover background",
		"selector" => ":link:hover,:visited:hover",
		"property" => "background-color"
	),
	"container" => array(
		"title" => "#container",
		"selector" => "#container",
		"property" => "background-color"
	),
	"header" => array(
		"title" => "#header",
		"selector" => "#header",
		"property" => "background-color"
	),
	"wrapper" => array(
		"title" => "#wrapper",
		"selector" => "#wrapper",
		"property" => "background-color"
	),
	"content" => array(
		"title" => "#content",
		"selector" => "#content",
		"property" => "background-color"
	),
	"navigation" => array(
		"title" => "#navigation",
		"selector" => "#navigation",
		"property" => "background-color"
	),
	"extra" => array(
		"title" => "#extra",
		"selector" => "#extra",
		"property" => "background-color"
	),
	"footer" => array(
		"title" => "#footer",
		"selector" => "#footer",
		"property" => "background-color"
	),
	"module" => array(
		"title" => ".module",
		"selector" => ".module",
		"property" => "background-color"
	),
);
$gBitSmarty->assign( 'properties', $properties );

// this will fetch the color scheme from the server
if( !empty( $_REQUEST['color_scheme'] ) ) {
	if( $colorScheme = $gCascader->fetchScheme( '/dcs/'.$_REQUEST['color_scheme'] ) ) {
		$gBitSmarty->assign( 'colorScheme', $colorScheme );
	}
}

// Remove all scheme files
if( !empty( $_REQUEST['clear_all_styles'] ) ) {
	unlink_r( LibertyAttachable::getStoragePath() );
	$gBitSystem->storeConfig( 'cascader_style', NULL );
}

// create a css file based on the user specifications
if( !empty( $_REQUEST['create_style'] ) ) {
	$cascaderCss  = $gCascader->createHeader( $colorScheme );
	$cascaderCss .= $gCascader->createCss( $_REQUEST['cascader'], $properties );

	// TODO: need the style name to insert here.
	//if( $cssUrl = $gCascader->writeCss( str_replace( "/", "", $_REQUEST['color_scheme'] ).date( "-Ymd-His" ).'.css', $cascaderCss ) ) {

	// We need to work out what name to use
	$storefile = str_replace( "/", "", $_REQUEST['color_scheme'] );
	$schemeFiles = array();
	$cssList = $gCascader->getList();

	$storefile .= '-'.str_replace( " ", "_", $gCascader->mTitle ).'.css';

	if( $cssUrl = $gCascader->writeCss( $storefile, $cascaderCss ) ) {
		$feedback['success'] = tra( "The css file was stored to" ).": ".$cssUrl;
	} else {
		$feedback['error'] = tra( "There was a problem storing your custom css file." );
	}

	$gBitSystem->storeConfig( 'cascader_style', $cssUrl );
}

// apply an existing style
if( !empty( $_REQUEST['apply_style'] ) ) {
	$gBitSystem->storeConfig( 'cascader_style', LibertyAttachable::getStorageUrl().$_REQUEST['apply_style'] );
}

// unset the custom style color settings
if( !empty( $_REQUEST['clear_style'] ) ) {
	$gBitSystem->storeConfig( 'cascader_style', NULL );
}

// remove a style file
if( !empty( $_REQUEST['remove_style'] ) ) {
	if( $gCascader->expunge( $_REQUEST['remove_style'] ) ) {
		// remove the config entry if we're removing the css file we're currently using
		if( preg_match( "/".$_REQUEST['remove_style']."$/", $gBitSystem->getConfig( 'cascader_style' ) ) ) {
			$gBitSystem->storeConfig( 'cascader_style', NULL );
		}
		$feedback['success'] = tra( "The css file was successfully removed from your system" );
	}
}

// create the calendar
$date = getdate( time() );
$gBitSmarty->assign( 'calendar', $gCascader->getMonthView( ( !empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : $date['mon'] ), ( !empty( $_REQUEST['year'] ) ? $_REQUEST['year'] : $date['year'] ) ) );

// get a list of stored css files
$cssList = $gCascader->getList();
$gBitSmarty->assign( 'cssList', $cssList );

// crude method of loading css styling but we can fix this later
$gBitSmarty->assign( "loadThemesCss", TRUE );
$gBitSmarty->assign( "feedback", $feedback );

$gBitSystem->display( 'bitpackage:cascader/cascader.tpl', 'Cascader' );
?>
