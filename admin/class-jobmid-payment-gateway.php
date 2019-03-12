<?php
namespace Jobmid\Admin;

class PaymentGateway
{
    public $priority    = 1111;
    public $unique_slug = 'midtrans';
    protected $notice   = false;
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
		$this->version = $version;

	}

    public function init_gateways()
    {
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
            update_option('wpjobster_midtrans_enable',              $_POST['wpjobster_midtrans_enable']);
            update_option('wpjobster_midtrans_enable_topup',        $_POST['wpjobster_midtrans_enable_topup']);
            update_option('wpjobster_midtrans_enable_featured',     $_POST['wpjobster_midtrans_enable_featured']);
            update_option('wpjobster_midtrans_enable_custom_extra', $_POST['wpjobster_midtrans_enable_custom_extra']);
            update_option('wpjobster_midtrans_enable_tips',         $_POST['wpjobster_midtrans_enable_tips']);
            update_option('wpjobster_midtrans_enable_sandbox',      $_POST['wpjobster_midtrans_enable_sandbox']);
            update_option('wpjobster_midtrans_button_caption',      $_POST['wpjobster_midtrans_button_caption']);
            update_option('wpjobster_midtrans_merchant_id',         $_POST['wpjobster_midtrans_merchant_id']);
            update_option('wpjobster_midtrans_client_key',          $_POST['wpjobster_midtrans_client_key']);
            update_option('wpjobster_midtrans_secret_key',          $_POST['wpjobster_midtrans_secret_key']);
            update_option('wpjobster_midtrans_finish_message',      $_POST['wpjobster_midtrans_finish_message']);
            update_option('wpjobster_midtrans_unfinish_message',    $_POST['wpjobster_midtrans_unfinish_message']);
            update_option('wpjobster_midtrans_error_message',       $_POST['wpjobster_midtrans_error_message']);

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
     * @param  [type] $payment_type [description]
     * @param  [type] $details      [description]
     * @return void
     */
    public function process_transaction($payment_type,$details)
    {
        __debug(func_get_args());
        exit;
    }
}
