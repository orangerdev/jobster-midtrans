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
    protected $log = false;
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
        $this->log   = fopen(plugin_dir_path(dirname(__FILE__)).'log.txt','a');

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
            update_option('wpjobster_midtrans_enable',              $_POST['wpjobster_midtrans_enable']);
            update_option('wpjobster_midtrans_enable_topup',        $_POST['wpjobster_midtrans_enable_topup']);
            update_option('wpjobster_midtrans_enable_featured',     $_POST['wpjobster_midtrans_enable_featured']);
            update_option('wpjobster_midtrans_enable_custom_extra', $_POST['wpjobster_midtrans_enable_custom_extra']);
            update_option('wpjobster_midtrans_enable_tips',         $_POST['wpjobster_midtrans_enable_tips']);
            update_option('wpjobster_midtrans_enable_sandbox',      $_POST['wpjobster_midtrans_enable_sandbox']);
            update_option('wpjobster_midtrans_button_caption',      $_POST['wpjobster_midtrans_button_caption']);
            update_option('wpjobster_midtrans_merchant_id',         $_POST['wpjobster_midtrans_merchant_id']);
            update_option('wpjobster_midtrans_client_key',          $_POST['wpjobster_midtrans_client_key']);
            update_option('wpjobster_midtrans_server_key',          $_POST['wpjobster_midtrans_server_key']);
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
     * Save payment type order
     * @param  string $payment_type [description]
     * @param  int    $order_id     [description]
     * @return void
     */
    protected function save_payment_type_from_order(string $payment_type,int $order_id)
    {
        $payments            = get_option('jobmid_payments');
        $payments            = (!is_array($payments)) ? [] : $payments;
        $payments[$order_id] = $payment_type;

        update_option('jobmid_payments',$payments);
    }

    /**
     * Get order payment type
     * @param  int    $order_id
     * @return string
     */
    protected function get_payment_type(int $order_id)
    {
        $payment_type = false;
        $payments     = get_option('jobmid_payments');

        if(isset($payments[$order_id])) :
            $payment_type = $payments[$order_id];
        endif;

        return $payment_type;
    }

    /**
     * Save notification status from ipn
     * @param  string $status
     * @param  int    $order_id
     * @return void
     */
    protected function save_notification_status(string $status, int $order_id)
    {
        $notifications            = get_option('jobmid_notifications');
        $notifications            = (!is_array($notifications)) ? [] : $notifications;
        $notifications[$order_id] = $status;

        update_option('jobmid_notifications',$notifications);
    }

    /**
     * Get order notification status
     * @param  integer $order_id
     * @return string
     */
    protected function get_notification(int $order_id)
    {
        $status = false;
        $notifications     = get_option('jobmid_notifications');

        if(isset($notifications[$order_id])) :
            $status = $notifications[$order_id];
        endif;

        return $status;
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
            $this->save_payment_type_from_order($payment_type,intval($detail['order_id']));
            wp_redirect(\Veritrans_VtWeb::getRedirectionUrl($transaction));
            exit;
        }
        catch (\Exception $e) {
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

    /**
     * Verify transaction with ipn notification
     * @return [type] [description]
     */
    protected function verify_transaction($payment_type)
    {
        $this->set_veritrans_config();
        fwrite($this->log,"\n".current_time('Y-m-d H:i').' called');

        try {
            $notification  = new \Veritrans_Notification();
            $order_id      = $notification->order_id;
            $transaction   = $notification->transaction_status;
            $fraud         = $notification->fraud_status;
            $payment       = $notification->payment_type;
            $text          = "Order ID $notification->order_id: "."transaction status = $transaction, fraud staus = $fraud, payment type = $payment";
            $order_details = wpjobster_get_order_details_by_orderid($order_id);
            $payment_type  = $this->get_payment_type(intval($notification->order_id));

            if(isset($_GET['action']) && 'notification' === $_GET['action']) :

                if(in_array($transaction,['capture','settlement'])) :

                    $payment_details = "success action returned"; // any info you may find useful for debug

                    do_action( "wpjobster_" . $payment_type . "_payment_success",
                        $order_id,
                        $this->unique_slug,
                        $payment_details,
                        maybe_serialize($_REQUEST)
                    );

                elseif(in_array($_GET['action'],['deny','cancel'])) :

                    do_action( "wpjobster_" . $payment_type . "_payment_failed",
                        $order_id,
                        $this->unique_slug,
                        $payment_details,
                        maybe_unserialize($_REQUEST)
                    );
                    die();

                endif;
            endif;

            fwrite($this->log,"\n".$text);
        }
        catch (\Exception $e) {
            fwrite($this->log,"\n duhh ".$e->getMessage());
        }

        fclose($this->log);

        die();
    }

    /**
     * Check transaction request
     * Hooked via action template_redirect, priority 999
     * -- Hooked via action wpjobster_processafter_midtrans_gateway, priority 999
     * @return void
     */
    public function check_transaction()
    {
        if(
            isset($_GET['payment_response']) && 'midtrans' === $_GET['payment_response'] &&
            isset($_GET['action'])
        ) :
            if('notification' === $_GET['action']) :
                $this->verify_transaction($payment_type);
            elseif(isset($_GET['order_id'])) :

                $payment_type   = $this->get_payment_type(intval($_GET['order_id']));
                $payment_status = $_GET['action'];
                $order_details  = wpjobster_get_order_details_by_orderid(intval($_GET['order_id']));

                if('finish' === $_GET['action']) :

                    $title   = __('Transaction Success','jobmid');
                    $message = get_option('wpjobster_midtrans_finish_message');

                elseif(in_array($_GET['action'],['unfinish','error'])) :

                    if('unfinish' === $_GET['action']) :
                        $title   = __('Transaction Unfinish','jobmid');
                        $message = get_option('wpjobster_midtrans_unfinish_message');
                    else :
                        $title   = __('Transaction Error','jobmid');
                        $message = get_option('wpjobster_midtrans_error_message');
                    endif;

                    $payment_details = "Failed action returned";

                    do_action( "wpjobster_" . $payment_type . "_payment_failed",
                        $order_id,
                        $this->unique_slug,
                        $payment_details,
                        maybe_unserialize($_REQUEST)
                    );

                endif;
            endif;

            require plugin_dir_path(dirname(__FILE__)).'public/partials/message.php';

            exit;
        endif;

    }
}
