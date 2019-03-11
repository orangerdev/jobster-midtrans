<?php
namespace Jobmid\Admin;

class PaymentGateway
{
    public $priority    = 1111;
    public $unique_slug = 'midtrans';
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
}
