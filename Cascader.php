<?php
/**
 * @version:      $Header: /cvsroot/bitweaver/_bit_cascader/Cascader.php,v 1.11 2007/11/01 16:24:30 squareing Exp $
 *
 * @author:       xing  <xing@synapse.plus.com>
 * @version:      $Revision: 1.11 $
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
	function Cascader( $pDate ) {
		// turn the date into a valid path
		if( !empty( $pDate['year'] ) && !empty( $pDate['month'] ) && !empty( $pDate['day'] ) ) {
			$remotePath =
				"/storage/".
				$pDate['year']."/".
				str_pad( $pDate['month'], 2, 0, STR_PAD_LEFT )."/".
				str_pad( $pDate['day'], 2, 0, STR_PAD_LEFT ).
				"/scheme.xml";
		} else {
			$remotePath = FALSE;
		}

		$this->mRemotePath = $remotePath;
	}

	function parseXml( $pXmlString ) {
		// create xml parser object
		$parser = xml_parser_create();

		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0);
		if( !xml_parse_into_struct( $parser, $pXmlString, $values, $index ) ) {
			$this->mErrors['xml_parsing'] = sprintf(
				"XML Error: %s at line %d",
				xml_error_string( $xml_get_error_code( $parser ) ),
				xml_get_current_line_number( $parser )
			);
		}
		xml_parser_free($parser);

		//vd($values);
		//vd($index);

		$ret = FALSE;
		foreach( $values as $element ) {
			if( $element['type'] == 'open' ) {
				if( array_key_exists( 'attributes', $element ) ) {
					list( $level[$element['level']], $extra ) = array_values( $element['attributes'] );
				} else {
					$level[$element['level']] = $element['tag'];
				}
			}

			if( $element['type'] == 'complete' ) {
				$start_level = 1;
				$evaluate = '$ret';
				while( $start_level < $element['level'] ) {
					$evaluate .= '[$level['.$start_level.']]';
					$start_level++;
				}
				$evaluate .= '[$element[\'tag\']] = $element[\'value\'];';
				eval( $evaluate );
			}
		}

		return $ret;
	}

	// Convert XML string to usable nested php array
	// nabbed from http://www.php.net/manual/nl/function.xml-parse-into-struct.php
	// peter at elemental dot org
	// 14-Jun-2005 04:09
	function xmlToArray( $xml_data ) {
		$ret = array();
		// parse the XML datastring
		$xml_parser = xml_parser_create ();
		xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0);
		if( xml_parse_into_struct( $xml_parser, $xml_data, $values, $index ) ) {
			xml_parser_free( $xml_parser );

			// convert the parsed data into a PHP datatype
			$ret = array();
			$ptrs[0] = &$ret;
			foreach( $values as $element ) {
				$level = $element['level'] - 1;
				switch( $element['type'] ) {
				case 'open':
					$tag_or_id = ( array_key_exists( 'attributes', $element ) ) ? $element['attributes']['id'] : $element['tag'];
					$ptrs[$level][$tag_or_id] = array();
					$ptrs[$level+1] = &$ptrs[$level][$tag_or_id];
					break;
				case 'complete':
					$ptrs[$level][$element['tag']] = ( isset( $element['value'] ) ) ? $element['value'] : '';
					break;
				}
			}

			// we set the mCascaderId here
			foreach( $index['scheme'] as $idx ) {
				if( $values[$idx]['type'] == 'open' ) {
					$ret['cascader_id'] = $values[$idx]['attributes']['id'];
				}
			}
		} else {
			$this->mErrors['xml_parsing'] = sprintf(
				"XML Error: %s at line %d",
				xml_error_string( xml_get_error_code( $xml_parser ) ),
				xml_get_current_line_number( $xml_parser )
			);
		}

		return $ret;
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
		if( $this->isValid() && $xml = bit_http_request( 'http://www.bitweaver.org'.$this->mRemotePath ) ) {
			if( preg_match( "/not found/i", $xml ) ) {
				$this->mErrors['load'] = tra( 'There is no scheme for this day.' );
			} else {
				$schemeInfo = $this->xmlToArray( $xml );

				foreach( $schemeInfo['service'] as $key => $info ) {
					$this->mInfo = array_merge( $this->mInfo, $info );
					// this is very dodgy...
					if( is_numeric( $key ) ) {
						$this->mCascaderId = $key;
					}

					if( !empty( $info['colors'] ) ) {
						foreach( $info['colors'] as $id => $color ) {
							$this->mInfo['colors'][$id] = '#'.$color['hex'];
						}
					}
				}

				$this->mTitle = $this->mInfo['title'];
				$this->mCascaderId = $schemeInfo['cascader_id'];

//				$this->mTitle = $this->mInfo['title'];
//				vd($this->mCascaderId);
//				vd($this->mInfo);

/*
				$parser = xml_parser_create();
				xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
				xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0);
				if( !xml_parse_into_struct( $parser, $xml, $data, $index ) ) {
					$this->mErrors['xml_parsing'] = print_error();
				}
				xml_parser_free($parser);

				//vd($data);
				//vd($index);

				// parse the data we extracted

				// cycle all <color> tags.
				// $index['color'] contains all pointers to <color> tags
				for( $i = 0; $i < count( $index['color'] ); $i++ ) {
					// extract needed information
					if( $data[$i]['type'] == 'complete' && $data[$i]['tag'] != 'hex' ) {
						$this->mInfo[$data[$i]['tag']] = $data[$i]['value'];
					}

					// since we have <color> nested inside the <colors> tag,
					// we have to check if pointer is to open type tag.
					if( $data[$index['color'][$i]]['type'] == 'open' ) {

						// extract needed information
						for( $j = $index['color'][$i]; $j < $index['color'][$i+1]; $j++ ) {
							if( $data[$j]['tag'] == 'hex' ) {
								$this->mInfo['colors'][] = '#'.$data[$j]['value'];
							}
						}
					}
				}

				foreach( $index['scheme'] as $idx ) {
					if( $data[$idx]['type'] == 'open' ) {
						$this->mCascaderId = $data[$idx]['attributes']['id'];
					}
				}

				$this->mTitle = $this->mInfo['title'];
				vd($this->mCascaderId);
				vd($this->mInfo);
*/

			}
		} elseif( $this->isValid() ) {
			$this->mErrors['load'] = tra( 'There is no scheme for this day.' );
		}

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
		// properties are kept in a separate file to maintain readability
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
		if( $this->isValid() && !empty( $pString ) && $path = LibertyAttachable::getStoragePath() ) {
			// We need to work out what name to use
			$storefile = $this->mInfo['date'].'-'.str_replace( " ", "_", $this->mTitle ).'.css';
			$fh = fopen( $path.$storefile, 'w' );
			fwrite( $fh, $pString );
			fclose( $fh );
			return LibertyAttachable::getStorageUrl().$storefile;
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
		return( !empty( $this->mRemotePath ) );
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
			return CASCADER_PKG_URL."index.php?day=$day&amp;month=$month&amp;year=$year#picker";
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

function xml_print_error() {
	global $parser;
	return( sprintf( "XML Error: %s at line %d",
		xml_error_string( $xml_get_error_code( $parser ) ),
		xml_get_current_line_number( $parser )
	) );
}
?>
