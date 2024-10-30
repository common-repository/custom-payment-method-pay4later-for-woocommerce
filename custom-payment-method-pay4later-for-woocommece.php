<?php
/*
Plugin Name: Custom Payment Method Pay4later for Woocommerce
Description:  Custom payment method to add pay4later payment method to your woocommerce site.
Version: 1.0.0
Author: Matthew Crisp
License: GPLv2

*/

//Additional links on the plugin page
add_filter( 'plugin_row_meta', 'pay4later_register_plugin_links', 10, 2 );
function pay4later_register_plugin_links($links, $file) {
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="http://www.crispwebdesign.co.uk/" target="_blank">' . __( 'Crisp Webdesign', 'crisp' ) . '</a>';
	}
	return $links;
}



/* WooCommerce fallback notice. */
function pay4later_fallback_notice() {
    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Custom Payment Gateways depends on the last version of %s to work!', 'pay4later' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
}

/* Load functions. */
function pay4later_load() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        add_action( 'admin_notices', 'pay4later_fallback_notice' );
        return;
    }
   
    function WC_pay4later_add_gateway( $methods ) {
		
        $methods[] = 'WC_pay4later12';
		$methods[] = 'WC_pay4later24';
		$methods[] = 'WC_pay4later36'; 
		$methods[] = 'WC_pay4later48';  
        return $methods;
    }
	add_filter( 'woocommerce_payment_gateways', 'WC_pay4later_add_gateway' );
	
	
    // Include  Custom Payment Gateways classes.
    require_once  plugin_dir_path( __FILE__ ) . 'class-payment_gateway_pay4later12.php';
    require_once  plugin_dir_path( __FILE__ ) . 'class-payment_gateway_pay4later24.php';	
    require_once  plugin_dir_path( __FILE__ ) . 'class-payment_gateway_pay4later36.php';	
    require_once  plugin_dir_path( __FILE__ ) . 'class-payment_gateway_pay4later48.php';	
 
}

add_action( 'plugins_loaded', 'pay4later_load', 0 );


//display pay4later based on cart price
add_filter('woocommerce_available_payment_gateways','filter_gateways',1);
function filter_gateways($gateways){
global $woocommerce;
if($woocommerce->cart->total<500){
	unset($gateways['pay4later24']);
	unset($gateways['pay4later36']);
	unset($gateways['pay4later48']);
	unset($gateways['pay4later12']);                                }
return $gateways;
}

/* Adds custom settings url in plugins page. */
function pay4later_action_links( $links ) {
    $settings = array(
		'settings' => sprintf(
		'<a href="%s">%s</a>',
		admin_url( 'admin.php?page=wc-settings&tab=checkout' ),
		__( 'Settings', 'pay4later' )
		)
    );

    return array_merge( $settings, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pay4later_action_links' );


?>