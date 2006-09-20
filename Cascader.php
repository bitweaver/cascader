<?php
/**
 * @version:      $Header: /cvsroot/bitweaver/_bit_cascader/Cascader.php,v 1.6 2006/09/20 06:50:52 squareing Exp $
 *
 * @author:       xing  <xing@synapse.plus.com>
 * @version:      $Revision: 1.6 $
 * @created:      Monday Jul 03, 2006   11:53:42 CEST
 * @package:      treasury
 * @copyright:    2003-2006 bitweaver
 * @license:      LGPL {@link http://www.gnu.org/licenses/lgpl.html}
 **/
require_once( CASCADER_PKG_PATH.'Calendar.php' );

/**
 * Cascader 
 * 
 */
class Cascader {
	/**
	 * Scheme Title
	 */
	var $mTitle;

	/**
	 * This is the unique path on the remote server
	 */
	var $mRemotePath;

	/**
	 * Unique ID for this sheme
	 */
	var $mCascaderId;

	/**
	 * Information about this scheme
	 */
	var $mInfo = array();

	/**
	 * Errors
	 */
	var $mErrors = array();

	/**
	 * Initiate class
	 *
	 * @param $pContentId content id of the treasury - use either one of the ids.
	 * @param $pStructureId structure id of the treasury - use either one of the ids.
	 * @return none
	 * @access public
	 **/
	function Cascader( $pRemotePath = NULL ) {
		// this string is likely to be messed up. lets turn it into something nice
		if( !empty( $pRemotePath ) ) {
			preg_match( "#(/[\d]+)*$#", $pRemotePath, $match );
			$id = str_replace( "/", "", $match[0] );
		} else {
			$id = 0;
		}
		$this->mCascaderId = $id;
		$this->mRemotePath = $pRemotePath;
	}

	/**
	 * Load the color scheme
	 * 
	 * @param array $pRemotePath Remote path to daily color scheme e.g.: 2006/04/28
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		$scheme = array();
		if( $this->isValid() && $scheme = BitSystem::fetchRemoteFile( 'xing.hopto.org', $this->mRemotePath ) ) {
			if( preg_match( "/not found/i", $scheme ) ) {
				$this->mErrors['load'] = tra( 'There is no scheme for this day.' );
			} else {
				$scheme = explode( ' ', trim( $scheme ) );
				$scheme[] = "#000000";
				$scheme[] = "#FFFFFF";
			}
		} elseif( $this->isValid() ) {
			$this->mErrors['load'] = tra( 'There is no scheme for this day.' );
		}
		$this->mTitle = "Scheme Name";
		$this->mInfo['scheme'] = $scheme;
		$this->mInfo['title'] = $this->mTitle;
		$this->loadProperties();
		return( !empty( $scheme ) );
	}

	/**
	 * Load Properties 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function loadProperties() {
		// properties are kept in a seperate file to maintain readability
		require_once( CASCADER_PKG_PATH.'properties.php' );
		$this->mInfo['properties'] = $properties;
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
	 * @param array $pColorHash Set of colors
	 * @access public
	 * @return CSS as a string
	 */
	function createCss( $pColorSettings ) {
		$ret = '';
		if( empty( $this->mInfo['properties'] ) ) {
			$this->loadProperties();
		}
		$props = $this->mInfo['properties'];

		if( is_array( $pColorSettings ) ) {
			foreach( $pColorSettings as $id => $value ) {
				if( !empty( $value ) ) {
					$ret .= $props[$id]['selector']." {\n\t".$props[$id]['property'].": $value !important;\n}\n\n";
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
		$gBitSmarty->assign( 'gCascader', $this );
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
	function writeCss( $pString = NULL ) {
		if( $this->isValid() && !empty( $pString ) && $branch = LibertyAttachable::getStorageBranch() ) {
			// We need to work out what name to use
			$storefile = $this->mCascaderId.'-'.str_replace( " ", "_", $this->mTitle ).'.css';
			$fh = fopen( BIT_ROOT_PATH.$branch.$storefile, 'w' );
			fwrite( $fh, $pString );
			fclose( $fh );
			return BIT_ROOT_URL.$branch.$storefile;
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

	/**
	 * Check if class is valid
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( !empty( $this->mCascaderId ) );
	}
}

class CascaderCalendar extends Calendar {
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
		global $gBitSystem;
		if( $month >= 9 && $year >= 2006 && $gBitSystem->mServerTimestamp->mktime( 0, 0, 0, $month, $day, $year ) < $gBitSystem->mServerTimestamp->getUTCTime() ) {
			$scheme = "$year/".str_pad( $month, 2, 0, STR_PAD_LEFT )."/".str_pad( $day, 2, 0, STR_PAD_LEFT );
			return CASCADER_PKG_URL."index.php?day=$day&amp;month=$month&amp;year=$year&amp;scheme=$scheme#picker";
		}
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
		return CASCADER_PKG_URL."index.php?month=$month&amp;year=$year";
	}
}
?>
