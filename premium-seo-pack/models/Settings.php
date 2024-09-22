<?php
/**
 * PSP_Models_Settings
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class PSP_Models_Settings {
	public $_psp_row_count;

	public function check_psp_db( $psp_url_hash ) {
		global $wpdb;
		$query = "SELECT * FROM " . $wpdb->prefix . strtolower( _PSP_DB_ ) . " WHERE url_hash = '" . $psp_url_hash . "';";

		if ( count( $row = $wpdb->get_row( $query, OBJECT ) ) ) {
			return maybe_unserialize( stripslashes( $row->seo ) );
		}

		return false;
	}

	public function db_insert( $url, $url_hash, $seo, $date_time ) {
		global $wpdb;
		$blog_id = get_current_blog_id();

		$psp_query = "INSERT INTO " . $wpdb->prefix . strtolower( _PSP_DB_ ) . " (blog_id, URL, url_hash, seo, date_time)
                VALUES ('$blog_id','$url','$url_hash','$seo','$date_time')
                ON DUPLICATE KEY
                UPDATE blog_id = '$blog_id', URL = '$url', url_hash = '$url_hash', seo = '$seo', date_time = '$date_time'";

		return $wpdb->query( $psp_query );
	}


	/**
	 * Check the Google code saved at settings
	 *
	 * @return string
	 */
	public function checkGoogleWTCode( $code ) {

		if ( $code <> '' ) {
			if ( strpos( $code, 'content' ) !== false ) {
				preg_match( '/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}
			if ( strpos( $code, '"' ) !== false ) {
				preg_match( '/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( $code == '' ) {
				PSP_Classes_Error::setError( __( "The code for Google Webmaster Tool is incorrect.", _PSP_PLUGIN_NAME_ ) );
			}
		}

		return $code;
	}

	/**
	 * Check the Google code saved at settings
	 *
	 * @return string
	 */
	public function checkGoogleAnalyticsCode( $code ) {
		if ( $code <> '' ) {
			if ( strpos( $code, 'GoogleAnalyticsObject' ) !== false ) {
				preg_match( '/ga\(\'create\',[^\'"]*[\'"]([^\'"]+)[\'"],/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( strpos( $code, '"' ) !== false ) {
				preg_match( '/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( strpos( $code, 'UA-' ) === false ) {
				$code = '';
				PSP_Classes_Error::setError( __( "The code for Google Analytics is incorrect.", _PSP_PLUGIN_NAME_ ) );
			}
		}

		return trim( $code );
	}

	/**
	 * Check the Facebook code saved at settings
	 *
	 * @return string
	 */
	public function checkFacebookAdminCode( $code ) {
		$id = '';
		if ( is_string( $code ) && $code <> '' ) {
			$code = trim( $code );
			if ( strpos( $code, '"' ) !== false ) {
				preg_match( '/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$id = $result[1];
				}
			}

			if ( strpos( $code, 'facebook.com/' ) !== false ) {
				preg_match( '/facebook.com\/([^\/]+)/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					if ( is_string( $result[1] ) ) {
						$response = PSP_Classes_Action::apiCall( 'sq/seo/facebook-id', array( 'profile' => $result[1] ) );
						if ( $response && $json = json_decode( $response ) ) {
							$id = $json->code;
						}
					} elseif ( is_numeric( $result[1] ) ) {
						$id = $result[1];
					}
				}
			} else {
				$response = PSP_Classes_Action::apiCall( 'sq/seo/facebook-id', array( 'profile' => $code ) );
				if ( $response && $json = json_decode( $response ) ) {
					if ( isset( $json->code ) ) {
						$id = $json->code;
					}
				}
			}

			if ( $id == '' ) {
				PSP_Classes_Error::setError( __( "The code for Facebook is incorrect.", _PSP_PLUGIN_NAME_ ) );
			}
		} elseif ( is_numeric( $code ) ) {
			$id = $code;
		}

		return $id;
	}

	/**
	 * Check the Pinterest code saved at settings
	 *
	 * @return string
	 */
	public function checkPinterestCode( $code ) {
		if ( $code <> '' ) {
			if ( strpos( $code, 'content' ) !== false ) {
				preg_match( '/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( strpos( $code, '"' ) !== false ) {
				preg_match( '/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( $code == '' ) {
				PSP_Classes_Error::setError( __( "The code for Pinterest is incorrect.", _PSP_PLUGIN_NAME_ ) );
			}
		}

		return $code;
	}

	/**
	 * Check the Bing code saved at settings
	 *
	 * @return string
	 */
	public function checkBingWTCode( $code ) {
		if ( $code <> '' ) {
			if ( strpos( $code, 'content' ) !== false ) {
				preg_match( '/content\\s*=\\s*[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( strpos( $code, '"' ) !== false ) {
				preg_match( '/[\'\"]([^\'\"]+)[\'\"]/i', $code, $result );
				if ( isset( $result[1] ) && ! empty( $result[1] ) ) {
					$code = $result[1];
				}
			}

			if ( $code == '' ) {
				PSP_Classes_Error::setError( __( "The code for Bing is incorrect.", _PSP_PLUGIN_NAME_ ) );
			}
		}

		return $code;
	}

	/**
	 * Check the Twitter account
	 *
	 * @return string
	 */
	public function checkTwitterAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'twitter.' ) === false ) {
			$account = 'https://twitter.com/' . $account;
		}

		return $account;
	}

	/**
	 * Check the Google + account
	 *
	 * @return string
	 */
	public function checkGoogleAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'google.' ) === false ) {
			$account = 'https://plus.google.com/' . $account;
		}

		return str_replace( " ", "+", $account );
	}

	/**
	 * Check the LinkedIn + account
	 *
	 * @return string
	 */
	public function checkLinkeinAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'linkedin.' ) === false ) {
			$account = 'https://www.linkedin.com/in/' . $account;
		}

		return $account;
	}

	/**
	 * Check the facebook account
	 *
	 * @return string
	 */
	public function checkFacebookAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'facebook.com' ) === false ) {
			$account = 'https://www.facebook.com/' . $account;
		}

		return $account;
	}

	public function checkPinterestAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'pinterest.com' ) === false ) {
			$account = 'https://www.pinterest.com/' . $account;
		}

		return $account;
	}

	public function checkInstagramAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'instagram.com' ) === false ) {
			$account = 'https://www.instagram.com/' . $account;
		}

		return $account;
	}

	public function checkMySpaceAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'myspace.com' ) === false ) {
			$account = 'https://myspace.com/' . $account;
		}

		return $account;
	}

	public function checkYoutubeAccount( $account ) {
		if ( $account <> '' && strpos( $account, 'youtube.com' ) === false ) {
			if ( strpos( $account, 'user/' ) === false && strpos( $account, 'channel/' ) === false ) {
				$account = 'https://www.youtube.com/channel/' . $account;
			}
		}

		return $account;
	}

}
