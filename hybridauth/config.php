<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => SITE_DOMAIN . BASE_URL . "hybridauth/", 

		"providers" => array ( 
			// openid providers
			"OpenID" => array (
				"enabled" => false
			),

			"AOL"  => array ( 
				"enabled" => false 
			),

			"Yahoo" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" )
			),

			"Google" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" )
			),

			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "XXXX", "secret" => "XXXXXXX" )
			),

			"Twitter" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			// windows live
			"Live" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),

			"MySpace" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			"LinkedIn" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			"Foursquare" => array (
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => ""
	);
