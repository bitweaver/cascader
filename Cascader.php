<?php
/**
 * @version:      $Header: /cvsroot/bitweaver/_bit_cascader/Cascader.php,v 1.2 2006/09/14 09:50:17 squareing Exp $
 *
 * @author:       xing  <xing@synapse.plus.com>
 * @version:      $Revision: 1.2 $
 * @created:      Monday Jul 03, 2006   11:53:42 CEST
 * @package:      treasury
 * @copyright:    2003-2006 bitweaver
 * @license:      LGPL {@link http://www.gnu.org/licenses/lgpl.html}
 **/
require_once( CASCADER_PKG_PATH.'Calendar.php' );

/**
 * Cascader 
 * 
 * @uses Calendar
 */
class Cascader extends Calendar {
	/**
	 * Scheme Title
	 */
	var $mTitle;

	/**
	 * Initiate class
	 *
	 * @param $pContentId content id of the treasury - use either one of the ids.
	 * @param $pStructureId structure id of the treasury - use either one of the ids.
	 * @return none
	 * @access public
	 **/
	function Cascader( $pTitle = NULL ) {
		$this->mTitle = $pTitle;
	}

	/**
	 * Provide our own day link
	 * 
	 * @param array $day 
	 * @param array $month 
	 * @param array $year 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDateLink( $day, $month, $year ) {
		return CASCADER_PKG_URL."index.php?color_scheme=$year/".str_pad( $month, 2, 0, STR_PAD_LEFT )."/".str_pad( $day, 2, 0, STR_PAD_LEFT )."#picker";
	}

	/**
	 * Allow for monthly navigation
	 * 
	 * @param array $month 
	 * @param array $year 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getCalendarLink( $month, $year ) {
		return CASCADER_PKG_URL."index.php?month=$month&year=$year";
	}

	/**
	 * fetch the color scheme
	 * 
	 * @param array $pRemoteFile 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function fetchScheme( $pRemoteFile ) {
		$ret = FALSE;
		if( $ret = BitSystem::fetchRemoteFile( 'xing.hopto.org', $pRemoteFile ) ) {
			if( preg_match( "/not found/i", $ret ) ) {
				$ret = FALSE;
			} else {
				$ret = explode( ' ', trim( $ret ) );
				$ret[] = "#000000";
				$ret[] = "#FFFFFF";
			}
		}
		return $ret;
	}

	/**
	 * Get a list of stored styles
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getList() {
		$branch = LibertyAttachable::getStorageBranch();
		$ret = array();
		if( $h = opendir( BIT_ROOT_PATH.$branch ) ) {
			while( FALSE !== ( $file = readdir( $h ) ) ) {
				if( !preg_match( "#^\.#", $file ) ) {
					$ret[$file]['url']  = BIT_ROOT_URL.$branch.$file;
					$ret[$file]['path'] = BIT_ROOT_PATH.$branch.$file;
				}
			}
			closedir( $h );
			ksort( $ret );
		}
		return $ret;
	}

	/**
	 * Create a valid css file based on a set of colors and the appropriate ids
	 * 
	 * @param array $pColorHash Set of colors where the keys need to match the keys of the $pKeys hash
	 * @param array $pKeys Set of CSS IDs and Classes where the keys need to match the keys in $pColorHash
	 * @access public
	 * @return CSS as a string
	 */
	function createCss( $pColorSettings, $pKeys ) {
		$ret = '';
		if( is_array( $pColorSettings ) ) {
			foreach( $pColorSettings as $id => $value ) {
				if( !empty( $value ) ) {
					$ret .= $pKeys[$id]['selector']." {".$pKeys[$id]['property'].":$value;}\n";
				}
			}
		}
		return $ret;
	}

	/**
	 * Create a header for the css file
	 * 
	 * @param array $pSchemeHash Scheme information including color set and title
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function createHeader( $pColorHash = NULL ) {
		global $gBitSmarty;
		$scheme['title'] = $this->mTitle;
		$scheme['colors'] = $pColorHash;
		$gBitSmarty->assign( 'scheme', $scheme );
		return $gBitSmarty->fetch( 'bitpackage:cascader/css_header.tpl' );
	}

	/**
	 * Write CSS to file
	 * 
	 * @param array $pStyleName Name of the file to store it to
	 * @param array $pString 
	 * @access public
	 * @return the URL to the file just stored - ready for linking
	 */
	function writeCss( $pStyleName = NULL, $pString = NULL ) {
		if( !empty( $pString ) && $branch = LibertyAttachable::getStorageBranch() ) {
			if( empty( $pStyleName ) ) {
				$pStyleName = 'temp.css';
			}
			$fh = fopen( BIT_ROOT_PATH.$branch.$pStyleName, 'w' );
			fwrite( $fh, $pString );
			fclose( $fh );
			return BIT_ROOT_URL.$branch.$pStyleName;
		}
	}

	/**
	 * remove a specific css file
	 * 
	 * @param array $pStyleName Name of the file
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function expunge( $pStyleName ) {
		$path = LibertyAttachable::getStoragePath();
		if( is_file( $path.$pStyleName ) ) {
			unlink( $path.$pStyleName );
			return TRUE;
		}
		return FALSE;
	}
}
?>
