<?php
/**
 * @author   xing <xing@synapse.plus.com>
 * @version  $Revision: 1.3 $
 * @package  Treasury
 * @subpackage functions
 */
global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'cascader',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'cascader' ) ) {
	if( $gBitUser->isAdmin() ) {
		$menuHash = array(
			'package_name'  => CASCADER_PKG_NAME,
			'index_url'     => CASCADER_PKG_URL.'index.php',
		);
		$gBitSystem->registerAppMenu( $menuHash );
	}
}
?>
