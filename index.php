<?php
// Initial setup
require_once( '../bit_setup_inc.php' );
require_once( CASCADER_PKG_PATH.'Cascader.php' );

// Only admin can use this - for now
$gBitSystem->verifyPermission( 'p_admin' );

// make sure the package is active
$gBitSystem->verifyPackage( 'cascader' );

// This is appended to the css file link ensuring an up to date view on ever page load
$gBitSmarty->assign( 'refresh', '?refresh='.time() );

// array used to feedback information to the user
$feedback = array();


// We need to use the cascader style for this package
if( !empty( $_REQUEST['cascader_style'] ) ) {
	$gBitSystem->storeConfig( 'style', 'cascader', THEMES_PKG_NAME );
	$gPreviewStyle = 'cascader';
}

// apply the style layout
$styleLayouts = $gBitThemes->getStyleLayouts();
$gBitSmarty->assign_by_ref( "styleLayouts", $styleLayouts );

if( !empty( $_REQUEST["site_style_layout"] ) ) {
	$gBitSystem->storeConfig( 'site_style_layout', ( ( $_REQUEST["site_style_layout"] != 'remove' ) ? $_REQUEST["site_style_layout"] : NULL ), THEMES_PKG_NAME );
}


// set the remoteId
if( !empty( $_REQUEST['scheme'] ) ) {
	$remoteId = '/dcs/'.$_REQUEST['scheme'];
} else {
	$remoteId = NULL;
}
$gCascader = new Cascader( $remoteId );
if( !$gCascader->load() ) {
	$feedback['error'] = $gCascader->mErrors;
}

// Make sure gCascader is avalable in the templates
$gBitSmarty->assign_by_ref( 'gCascader', $gCascader );

// Process form requests

// Remove all scheme files
if( !empty( $_REQUEST['clear_all_styles'] ) ) {
	unlink_r( LibertyAttachable::getStoragePath() );
	$gBitSystem->storeConfig( 'cascader_style', NULL );
}

// create a css file based on the user specifications
if( !empty( $_REQUEST['create_style'] ) ) {
	$cascaderCss  = $gCascader->createHeader();
	$cascaderCss .= $gCascader->createCss( $_REQUEST['cascader'] );

	if( $cssUrl = $gCascader->writeCss( $cascaderCss ) ) {
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
$calendar = new CascaderCalendar();
$gBitSmarty->assign( 'calendar', $calendar->getMonthView( ( !empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : $date['mon'] ), ( !empty( $_REQUEST['year'] ) ? $_REQUEST['year'] : $date['year'] ) ) );

// get a list of stored css files
$cssList = $gCascader->getList();
$gBitSmarty->assign( 'cssList', $cssList );

// crude method of loading css styling but we can fix this later
$gBitSmarty->assign( "loadThemesCss", TRUE );
$gBitSmarty->assign( "feedback", $feedback );

$gBitSystem->display( 'bitpackage:cascader/cascader.tpl', 'Cascader' );
?>
