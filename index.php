<?php
require 'openid.php';

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}

try {
    $openid = new LightOpenID;
    if(!$openid->mode) {
        if(isset($_POST['openid_identifier'])) {
            $openid->identity = $_POST['openid_identifier'];
            $openid->required = array('namePerson/friendly', 'contact/email', 'namePerson/first'); 
            header('Location: ' . $openid->authUrl());
        }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>OpenID Selector</title>
	
	<!-- Simple OpenID Selector -->
	<link rel="stylesheet" href="css/openid.min.css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	<script type="text/javascript" src="js/combine-openid.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
	    openid.init('openid_identifier');
	    openid.setDemoMode(false); //Stops form submission for client javascript-only test purposes
	});
	</script>
	<!-- /Simple OpenID Selector -->
	
	<style type="text/css">
		/* Basic page formatting. */
		body {
			font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;
		}
	</style>
</head>

<body>
<!-- Simple OpenID Selector -->
<form action="" method="post" id="openid_form">
	<input type="hidden" name="action" value="verify" />

	<fieldset>
    		<legend>Sign-in or Create New Account</legend>
    		
    		<div id="openid_choice">
	    		<p>Please click your account provider:</p>
	    		<div id="openid_btns"></div>
			</div>
			
			<div id="openid_input_area">
				<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
				<input id="openid_submit" type="submit" value="Sign-In"/>
			</div>
			<noscript>
			<p>OpenID is service that allows you to log-on to many different websites using a single indentity.
			Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
			</noscript>
	</fieldset>
</form>
<!-- /Simple OpenID Selector -->

</body>
</html>
<?php
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
        $attributes = $openid->getAttributes();
        echo '<pre>';
        print_r($attributes);
        echo '</pre>';
        echo '<p>Your email: ' . $attributes['contact/email'] . '</p>';
        echo '<p>Your display picture from gravatar: ' . get_gravatar($attributes['contact/email'], 80, 'mm',  'g', true) . '</p>';
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
