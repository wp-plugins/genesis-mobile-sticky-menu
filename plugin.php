<?php
/*
	Plugin Name: Genesis Mobile & Sticky Menu
	Plugin URI: http://ronimarinkovic.com/wordpress/genesis-mobile-menu/
	Description: Hamburger style sliding off-canvas mobile menu and sticky navigation menu for Genesis framework
	Author: Roni Marinkovic
	Version: 1.2
	Author URI: http://ronimarinkovic.com/
	License: GPL2
*/

/*	Copyright 2015  Roni Marinkovic  (email : ronimarin@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) OR exit;
// activation / deactivation of plugin, add default values or remove db entries of default values
register_activation_hook(__FILE__, 'gmm_add_defaults');
register_deactivation_hook( __FILE__, 'gmm_remove' );
//initialize and create menu item
add_action('admin_init', 'gmm_options_init' );
add_action('admin_menu', 'gmm_create_menu');

add_action( 'init', 'gmm_set_method' );
function gmm_set_method() {
	if ( !defined( 'GENESIS_MOBILE_MENU_METHOD' ) )
		define( 'GENESIS_MOBILE_MENU_METHOD', 'breakpoint' );
}

function gmm_create_menu() {
	$gmm_admin_page = add_submenu_page( 'genesis', __('Genesis Mobile & Sticky Menu Plugin Settings', 'genesis') , __('Mobile menu', 'genesis'), 'administrator', 'genesis-mobile-sticky-menu', 'gmm_options_page' );
	add_action('load-'.$gmm_admin_page, 'gmm_help_tab');
}

function gmm_add_defaults() {
	if ( !current_user_can( 'activate_plugins' ) )
		return;
	$tmp = get_option('gmm_options');
	if( !is_array($tmp) ) {
		$arr = array(
			"gmm_breakpoint" => "970",
			"gmm_sticky" => "Yes",
			"gmm_sticky_m" => "Secondary",
			"gmm_sticky_w" => "1024",
			"gmm_sticky_h" => "550"
		);
		update_option('gmm_options', $arr);
	}
}

function gmm_settings_link($links) { 
	$settings_link = '<a href="admin.php?page=genesis-mobile-sticky-menu">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
 
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'gmm_settings_link' );

function gmm_remove() {
	delete_option('gmm_options');
}

function gmm_options_init(){
	if ( !function_exists('genesis_get_option') ) {
		unset( $_GET['activate'] );
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action('admin_notices','genesis_required');
	}
	register_setting('gmm_options', 'gmm_options', 'gmm_options_validate' );
	add_settings_section('hamby_section', __('Hamburger mobile menu', 'genesis') , 'hamby_desc', __FILE__);
	add_settings_section('sticky_section',  __('Sticky navigation', 'genesis'), 'sticky_desc', __FILE__);
	add_settings_field('gmm_breakpoint',  __('Hamburger menu breakpoint', 'genesis'), 'hamby_break', __FILE__, 'hamby_section');
	add_settings_field('gmm_sticky',  __('Use sticky menu', 'genesis'), 'setting_sticky', __FILE__, 'sticky_section');
	add_settings_field('gmm_sticky_m',  __('Make sticky', 'genesis'), 'setting_sticky_m', __FILE__, 'sticky_section');
	add_settings_field('gmm_sticky_w',  __('Show sticky menu if screen is wider than', 'genesis'), 'setting_sticky_w', __FILE__, 'sticky_section');
	add_settings_field('gmm_sticky_h',  __('Show sticky menu after scrolling', 'genesis'), 'setting_sticky_h', __FILE__, 'sticky_section');
}

function genesis_required() {
    echo '<div class="error"><p>'. __( 'Genesis Mobile & Sticky Menu plugin requires Genesis framework. Plugin has been deactivated. Sorry about that. You can get Genesis <a href="http://ronimarinkovic.com/getgenesis">here</a>', 'genesis' ).'</p></div>';
}

function  hamby_desc() {
	echo '<p><span class=description">' . __('Enter number (pixels width) to define at which screen width mobile menu will show instead of main navigation menu', 'genesis') . '</span></p>';
}

function  sticky_desc() {
	echo '<p><span class=description">' . __('Set menu bar to stick to the top of website', 'genesis') . '</span></p>';
}

function hamby_break() {
	$options = get_option('gmm_options');
	echo "<input id='gmm_breakpoint' name='gmm_options[gmm_breakpoint]' size='5' type='text' value='{$options['gmm_breakpoint']}' /> pixels";
}

function setting_sticky() {
	$options = get_option('gmm_options');
        $items = array("Yes", "No");
        echo "<select name='gmm_options[gmm_sticky]'>";
        foreach ($items as $item) {
                $selected = ( $options['gmm_sticky'] === $item ) ? 'selected = "selected"' : '';
                echo "<option value='$item' $selected>$item</option>";
        }
        echo "</select>";
}

function setting_sticky_m() {
	$options = get_option('gmm_options');
        $items = array("Primary", "Secondary");
        echo "<select name='gmm_options[gmm_sticky_m]'>";
        foreach ($items as $item) {
                $selected = ( $options['gmm_sticky_m'] === $item ) ? 'selected = "selected"' : '';
                echo "<option value='$item' $selected>$item</option>";
        }
        echo "</select> menu";
}

function setting_sticky_w() {
	$options = get_option('gmm_options');
	echo "<input id='gmm_sticky_w' name='gmm_options[gmm_sticky_w]' size='5' type='text' value='{$options['gmm_sticky_w']}' /> pixels";
}

function setting_sticky_h() {
	$options = get_option('gmm_options');
	echo "<input id='gmm_sticky_h' name='gmm_options[gmm_sticky_h]' size='5' type='text' value='{$options['gmm_sticky_h']}' /> pixels";
}

function gmm_options_page() {
	if($_REQUEST['settings-updated']){
                echo '<div class="updated"><p>' . __('Settings updated', 'genesis') . '</p></div>';
        }
        if(isset($_POST['reset'])) {
		update_option('gmm_options', gmm_defaults() );
		echo '<div class="update-nag">' . __('Settings have been reset and default values are loaded', 'genesis') . '</div>'; 
	}
?>
		<div class="wrap genesis-metaboxes">
		<h2><?php esc_attr_e('Genesis mobile menu & Sticky navigation', 'genesis'); ?></h2>
		
		<form action="options.php" method="post">
			<p class="top-buttons">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'genesis'); ?>" />
				<input type="submit" name="reset" formaction="<?php echo admin_url( 'admin.php?page=genesis-mobile-sticky-menu&settings-reset=true' ); ?>" class="button-secondary" value="<?php esc_attr_e('Reset to default settings', 'genesis'); ?>" >
			</p>

			<div class="metabox-holder postbox">
				<div class="inside">
				<?php settings_fields('gmm_options'); ?>
				<?php do_settings_sections(__FILE__); ?>
				</div>
			</div>
			<p class="bottom-buttons submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'genesis'); ?>" />
				<input type="submit" name="reset" formaction="<?php echo admin_url( 'admin.php?page=genesis-mobile-sticky-menu&settings-reset=true' ); ?>" class="button-secondary" value="<?php esc_attr_e('Reset to default settings', 'genesis'); ?>" >
			</p>
		</form>
		</div>
<?php
}

function gmm_options_validate($input) {
	$input['gmm_breakpoint'] =  wp_filter_nohtml_kses($input['gmm_breakpoint']);
	$input['gmm_sticky_w'] =  wp_filter_nohtml_kses($input['gmm_sticky_w']);
	$input['gmm_sticky_h'] =  wp_filter_nohtml_kses($input['gmm_sticky_h']);
	return $input;
}

function gmm_defaults() {
	$defaults = array (
		"gmm_breakpoint" => "970",
		"gmm_sticky" => "Yes",
		"gmm_sticky_m" => "Secondary",
		"gmm_sticky_w" => "1024",
		"gmm_sticky_h" => "550"
	);
	return $defaults; 

}

function gmm_help_tab() {
	$screen = get_current_screen();
	$screen->add_help_tab( array(
	'id'		=> 'gmm_mobile_tab',
	'title'		=> __('Genesis Mobile Menu', 'genesis'),
	'content'	=> '<p>' . __( 'Enter number (pixels width) to define under which screen width hamburger style mobile menu will show instead of both (Primary navigation & Secondary navigation) menues', 'genesis' ) . '</p>',
	) );
	$screen->add_help_tab( array(
	'id'		=> 'gmm_sticky_tab',
	'title'		=> __('Genesis Sticky Menu', 'genesis'),
	'content'	=> '<p>' . __( 'Choose wheter or not you would like to have sticky menu on top after user scrolls down the page. Choose which Genesis navigation menu (Primary or Secondary) you want to stick on top of the page. Enter number (pixels width) to define at which minimum screen width sticky menu will show.', 'genesis' ) . '</p>',
	) );
}

add_action( 'wp_footer', 'genesis_sticky_nav', 99);
function genesis_sticky_nav() {
	$options = get_option('gmm_options');
	$sticky = $options['gmm_sticky'];
	$stickmenu = $options['gmm_sticky_m'];
	$stickwidth = $options['gmm_sticky_w'];
	$stickheight = $options['gmm_sticky_h'];
	$hover = 'Yes';
	if (!$stickwidth) {
		$stickwidth = 960;
	}
	if (!$stickheight) {
		$stickheight = 550;
	}
	if ($stickmenu == 'Primary') {
		$stickclass = '.nav-primary';
	} else {
		$stickclass = '.nav-secondary';
	}	
	if ( $sticky == 'Yes' ) {
		?><script type="text/javascript">
		var j = jQuery.noConflict();
		var  mn = j("<?php echo $stickclass; ?>");
		s = "gmm-sticky";
		hdr = j("site-header").height();
		
		j(window).scroll(function() {
		  if( j(this).scrollTop() > <?php echo $stickheight; ?> && j(window).width() > <?php echo $stickwidth; ?> ) {
		    mn.addClass(s);
		  } else {
		    mn.removeClass(s);
		  }
		});</script>
<?php }
}

add_action( 'wp_enqueue_scripts', 'gmm_enqueue_script', 99 );
function gmm_enqueue_script() {
	$options = get_option('gmm_options');
	$sticky = $options['gmm_sticky'];
	$breakpoint = $options['gmm_breakpoint'];
	
	wp_enqueue_style( 'genesis-mobile-menu', plugins_url( 'css/gmm.css' , __FILE__ ), $deps = array(), $ver = null, $media = false );
	
	if ( 'breakpoint' == GENESIS_MOBILE_MENU_METHOD ) {
		wp_enqueue_script( 'genesis-mobile-menu', plugins_url( 'js/gmm.js' , __FILE__ ), $deps = array( 'jquery' ), $ver = null, $in_footer = true );
		if (!$breakpoint) {
			$breakpoint = 480;
		}
		$values = array(
			'breakpoint' => $breakpoint,
			);
		wp_localize_script( 'genesis-mobile-menu', 'genesisMobileMenuBP', $values );
        } elseif ( 'device' == GENESIS_MOBILE_MENU_METHOD ) {
		wp_enqueue_script( 'genesis-mobile-menu', plugins_url( 'js/gmm-device.js' , __FILE__ ), $deps = array( 'jquery' ), $ver = null, $in_footer = true );
	}
}