<?php
/**
 * pay4later Gateway Class.
 */
class WC_pay4later36 extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'pay4later36';
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Proceed to Pay4later', 'pay4later36' );
		$this->method_title       = __( '19.5% APR - 36 Months', 'pay4later36' );
		$this->method_description = __( '19.5% APR - 36 Months works by sending customers to pay4later where they can enter their payment information.', 'pay4later36' );
		$this->supports           = array(
			'products',
			'refunds'
		);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
 		// Define user set variables.
        $this->title= $this->settings['title'];
        $this->description= $this->settings['description'];
		$this->instructions= $this->get_option( 'instructions' );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
		$this->merchant_key= $this->get_option( 'merchant_key' );
		$this->installation_id= $this->get_option( 'installation_id' );
		$this->finance_product_code= $this->get_option( 'finance_product_code' );
		$this->deposit_amount= $this->get_option( 'deposit_amount' );
		$this->mode= $this->get_option( 'mode' );

  		// Actions.
        if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) )
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
        else
            add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );


	}

	/**
	 * Logging method
	 * @param  string $message
	 */
	public function log( $message ) {
		if ( $this->debug ) {
			if ( empty( $this->log ) ) {
				$this->log = new WC_Logger();
			}
			$this->log->add( 'pay4later36', $message );
		}
	}



	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {
		return in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_pay4later36_supported_currencies', array(  'GBP','USD' ) ) );
	}

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
			?>
			<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( 'pay4later does not support your store currency.', 'woocommerce' ); ?></p></div>
			<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$shipping_methods = array();
		$this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'pay4later36' ),
                'type' => 'checkbox',
                'label' => __( 'Enable 19.5% APR - 36 Months', 'pay4later36' ),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __( 'Title', 'pay4later36' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'pay4later36' ),
                'desc_tip' => true,
                'default' => __( '19.5% APR - 36 Months', 'pay4later36' )
            ),
            'description' => array(
                'title' => __( 'Description', 'pay4later36' ),
                'type' => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'pay4later36' ),
                'default' => __( 'Desctiptions for 19.5% APR - 36 Months.', 'pay4later36' )
            ),
			'instructions' => array(
				'title' => __( 'Instructions', 'pay4later36' ),
				'type' => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'pay4later36' ),
				'default' => __( 'Instructions for 19.5% APR - 36 Months.', 'pay4later36' )
			),
			
			
		 'merchant_key' => array(
                'title' => __( 'Merchant key', 'pay4later36' ),
                'type' => 'text',
                'description' => __( 'Merchant Key.', 'pay4later36' ),
                'desc_tip' => true,
                'default' => __( '', 'pay4later36' )
            ),
			     'installation_id' => array(
                'title' => __( 'Installation ID', 'pay4later36' ),
                'type' => 'text',
                'description' => __( 'Installation ID.', 'pay4later36' ),
                'desc_tip' => true,
                'default' => __( '', 'pay4later36' )
            ),
			     
			     'finance_product_code' => array(
                'title' => __( 'Finance product code', 'pay4later36' ),
                'type' => 'text',
                'description' => __( 'Finance product code.', 'pay4later36' ),
                'desc_tip' => true,
                'default' => __( '', 'pay4later36' )
            ),
			
			
			     'deposit_amount' => array(
                'title' => __( 'Deposit amount', 'pay4later36' ),
                'type' => 'text',
                'description' => __( 'Deposit amount', 'pay4later36' ),
                'desc_tip' => true,
                'default' => __( '', 'pay4later36' )
            ),
			
		
			
			'mode' => array(
				'title' 		=> __( 'Test/Live mode', 'pay4later36' ),
				'type' 			=> 'select',
				'class'			=> 'chosen_select',
				'css'			=> 'width: 450px;',
				'default' 		=> '',
				'description' 	=> __( 'Test/Live mode', 'pay4later36' ),
				'options'     => array(
						'test'          => __( 'Test', 'woocommerce' ),
						'live' => __( 'Live', 'woocommerce' )
									),
				'desc_tip'      => true,
			)
        );

	}

	/**
	 * Get the transaction URL.
	 *
	 * @param  WC_Order $order
	 *
	 * @return string
	 */
	
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
	$order = wc_get_order( $order_id );
	$order->update_status('wc-processing', __( 'Your order is processing.', 'woocommerce' ));
	
		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$woocommerce->cart->empty_cart();
		
	if ( $this->mode=='test' ) {
			$environment_url = 'https://test.pay4later.com/credit-application/form/';
		} else {
			$environment_url = 'https://secure.pay4later.com/credit-application/form/';
		}			
	$get_total=$order->get_total(); 
	$deposit=$get_total*10/100;
	$payu_args =array(
				'Identification[api_key]'=> ($this->get_option( 'merchant_key' )),
				'Identification[RetailerUniqueRef]'=> ($order->get_order_number()),
				'Identification[InstallationID]'=>($this->get_option( 'installation_id' )),
				'Goods[0][Description]'=> ($this->get_option( 'description' )),
				'Goods[0][Quantity]'=> 1,
				'Goods[0][Price]' =>($get_total),
				'Finance[Code]' =>($this->get_option( 'finance_product_code' )),
				'Finance[Deposit]'=> ($deposit),
				'Consumer[Forename]'=> $order->billing_first_name,
				'Consumer[Surname]'=> $order->billing_last_name,
				'Consumer[EmailAddress]' => $order->billing_email
			
				
			);		

 		$payu_args_array = array();
        foreach($payu_args as $key => $value){
          $payu_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
        }
		
        $msg= "<form action='".$environment_url."' method='post' id='payment_form'>".implode('', $payu_args_array)."<input type='submit' class='button-alt' id='submit_payment_form' value='".__('Pay Now', 'payment_form')."' /></form>";
		return array(
			'result'   => 'success',
			'post_form'=>$msg,
				'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('thanks'))))
		
		);
	}

	
	
}
