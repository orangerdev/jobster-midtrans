<?php
namespace Jobmid\Admin;

use \Veritrans_Config;
use \Veritrans_Snap;

class PaymentGateway
{
    public $priority         = 1111;
    public $unique_slug      = 'midtrans';
    protected $notice        = false;
    protected $is_production = false;
    protected $merchant_id   = false;
    protected $server_key    = false;
    protected $client_key    = false;

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

    /**
     * Set veritrans config before make calling
     */
    protected function set_veritrans_config()
    {
        Veritrans_Config::$isProduction = $this->is_production;
        Veritrans_Config::$serverKey    = $this->server_key;
    }

    /**
     * Gateway initialization
     * Hooked via action plugins_loaded, priority 1
     * @return void
     */
    public function init_gateways()
    {
        $sandbox  = get_option('wpjobster_midtrans_enable_sandbox');

        $this->is_production = ('yes' === $sandbox) ? false : true;
        $this->server_key    = get_option('wpjobster_midtrans_server_key');
        $this->client_key    = get_option('wpjobster_midtrans_client_key');
        $this->merchant_id   = get_option('wpjobster_midtrans_merchant_id');

        add_filter( 'wpjobster_payment_gateways', [$this, 'register_payment_gateway' ]);
    }

    /**
     * Add midtrans payment gateway
     * Hooked via filter wpjobster_payment_gateways, priority 1
     * @param  array  $methods
     * @return array
     */
    public function register_payment_gateway($methods)
    {

        $methods[$this->priority] = [
            'label'           => __('Midtrans','jobmid'),
            'unique_id'       => $this->unique_slug,
            'action'          => 'wpjobster_taketo_'.$this->unique_slug.'_gateway',
            'response_action' => 'wpjobster_processafter_'.$this->unique_slug.'_gateway'
        ];

        add_action('wpjobster_show_paymentgateway_forms',[$this,'display_form'],$this->priority,3);

        return $methods;
    }

    /**
     * Display setting form
     * Hooked via action wpjobster_show_paymentgateway_forms, priority based on $priority
     * @return void
     */
    public function display_form($wpjobster_payment_gateways, $arr, $arr_pages)
    {
		$tab_id = get_tab_id( $wpjobster_payment_gateways );
        require plugin_dir_path(__FILE__).'/partials/form.php';
    }

    /**
     * Update setting data
     * Hooked via action admin_init, priority 999
     * @return void
     */
    public function update_setting()
    {
        if(isset($_POST['wpjobster_save_'.$this->unique_slug])) :
            update_option('wpjobster_midtrans_enable',         $_POST['wpjobster_midtrans_enable']);
            update_option('wpjobster_midtrans_enable_sandbox', $_POST['wpjobster_midtrans_enable_sandbox']);
            update_option('wpjobster_midtrans_button_caption', $_POST['wpjobster_midtrans_button_caption']);
            update_option('wpjobster_midtrans_merchant_id',    $_POST['wpjobster_midtrans_merchant_id']);
            update_option('wpjobster_midtrans_client_key',     $_POST['wpjobster_midtrans_client_key']);
            update_option('wpjobster_midtrans_server_key',     $_POST['wpjobster_midtrans_server_key']);

            $this->notice = true;
        endif;
    }

    /**
     * Update message
     * Hooked via action admin_notices, priority 999
     * @return void
     */
    public function update_notice()
    {
        if(true === $this->notice) :
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Midtrans setting updated', 'jobmid' ); ?></p>
            </div>
            <?php
        endif;
    }

    /**
     * Set allowed currency for midtrans payment gateway
     * @param   string $currency
     * @return  string
     */
    public function set_allowed_currency($currency)
    {
        return 'IDR';
    }

    /**
     * Process the transaction from checkout to payment gateway
     * Hooked via action wpjobster_taketo_midtrans_gateway, priority 999
     * @param  string $payment_type [description]
     * @param  array $details      [description]
     * @return void
     */
    public function process_transaction(string $payment_type,array $detail)
    {
        $this->set_veritrans_config();

        $transaction = [
            'transaction_details'   => [
                'order_id'      => $detail['order_id'],
                'gross_amount'  => $detail['wpjobster_final_payable_amount']
            ],
            'customer_details'      => [
                'first_name' => $detail['current_user']->user_firstname,
                'last_name'  => $detail['current_user']->user_lastname,
                'email'      => $detail['current_user']->user_email
            ],
            'item_details'          => [
                [
                    'id'        => $detail['post']->ID,
                    'price'     => $detail['wpjobster_final_payable_amount'],
                    'quantity'  => 1,
                    'name'      => $detail['post']->post_title
                ]
            ]
        ];

        try {
            wp_redirect(\Veritrans_VtWeb::getRedirectionUrl($transaction));
            exit;
        }
        catch (Exception $e) {
            echo ',,'.$e->getMessage();
            if(strpos ($e->getMessage(), "Access denied due to unauthorized")) :
                echo "<code>";
                echo "<h4>Please set real server key from sandbox</h4>";
                echo "In file: " . __FILE__;
                echo "<br>";
                echo "<br>";
                echo htmlspecialchars('Veritrans_Config::$serverKey = \'<your server key>\';');
                die();
            endif;
        }
        exit;
    }
}
