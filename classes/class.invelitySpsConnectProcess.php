<?php
require_once(__DIR__ . '/SoapRequestWebshipAPI.php');
require_once(__DIR__ . '/WebServiceAPIClasses.php');
define("wsdlLink", "https://webship.sps-sro.sk/services/WebshipWebService?wsdl");

class InvelitySpsConnectProcess
{
    private $launcher;
    private $options;
    public $successful = [];
    public $unsuccessful = [];

    /**
     * Loads plugin textdomain and sets the options attribute from database
     */
    public function __construct(InvelitySpsConnect $launecher)
    {
        $this->launcher = $launecher;
        load_plugin_textdomain($this->launcher->getPluginSlug(), false, dirname(plugin_basename(__FILE__)) . '/lang/');
        $this->options = get_option('invelity_sps_connect_options');
        add_action('admin_footer-edit.php', [$this, 'custom_bulk_admin_footer']);
        add_action('load-edit.php', [$this, 'custom_bulk_action']);
        add_action('wp_before_admin_bar_render', [$this, 'custom_bulk_admin_notices']);
    }

    /**
     * Adds option to export invoices to orders page bulk select
     */
    function custom_bulk_admin_footer()
    {
        global $post_type;

        if ($post_type == 'shop_order') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('<option>').val('sps_listky').text('<?php _e('Stiahnuť SPS lístky')?>').appendTo("select[name='action']");
                    jQuery('<option>').val('sps_listky').text('<?php _e('Stiahnuť SPS lístky')?>').appendTo("select[name='action2']");
                });
            </script>
            <?php
        }
    }

    /**
     * Sets up action to be taken after export option is selected
     * If export is selected, provides export and refreshes page
     * After refresh, notices are shown
     */
    function custom_bulk_action()
    {

        global $typenow;
        $post_type = $typenow;

        if ($post_type == 'shop_order') {
            $wp_list_table = _get_list_table('WP_Posts_List_Table');  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
            $action = $wp_list_table->current_action();


            $allowed_actions = ["sps_listky"];
            if (!in_array($action, $allowed_actions)) {
                return;
            }

            // security check
            check_admin_referer('bulk-posts');

            // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'

            if (isset($_REQUEST['post'])) {
                $post_ids = array_map('intval', $_REQUEST['post']);
            }


            if (empty($post_ids)) {
                return;
            }

            // this is based on wp-admin/edit.php
            $sendback = remove_query_arg(['exported', 'untrashed', 'deleted', 'ids'], wp_get_referer());
            if (!$sendback) {
                $sendback = admin_url("edit.php?post_type=$post_type");
            }

            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg('paged', $pagenum, $sendback);
            $client = new SoapRequestWebshipAPI();

            switch ($action) {
                case 'sps_listky':

                    date_default_timezone_set("Europe/Bratislava");

//                    require_once($this->launcher->settings['plugin-path'] . 'lib/nusoap/nusoap.php');
//
//                    $client = new nusoap_client('https://webship.sps-sro.sk/services/WebshipWebService?wsdl', 'wsdl');
//                    $client->soap_defencoding = 'UTF-8';
//                    $client->decode_utf8 = false;
//
//                    $err = $client->getError();
//                    if ($err) {
//                        echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
//                        die();
//                    }

//                    $pickupdate = date('Y-m-d', strtotime('+1 day'));
//                    while ($this->isSviatok($pickupdate)) {
//                        $pickupdate = date('Y-m-d', strtotime('+1 day', $pickupdate));
//                    }
//
//                    $weekDay = date('w', strtotime($pickupdate));
//
//                    if (($weekDay == 5)) //Check if the day is saturday or not.
//                    {
//                        $pickupdate = date('Y-m-d', strtotime('+2 day', $pickupdate));
//                    } elseif ($weekDay == 6) {
//                        $pickupdate = date('Y-m-d', strtotime('+1 day', $pickupdate));
//                    }

                    $sendback = remove_query_arg(['exported', 'untrashed', 'deleted', 'ids'], wp_get_referer());
                    if (!$sendback) {
                        $sendback = admin_url("edit.php?post_type=$post_type");
                    }
                    $pagenum = $wp_list_table->get_pagenum();
                    $sendback = add_query_arg('paged', $pagenum, $sendback);

                    $error = false;
                    foreach ($post_ids as $postId) {
                        global $woocommerce;
                        $order = new WC_Order($postId);

                        if ($order->has_shipping_method('local_pickup')) {
                            $this->unsuccessful[] = [
                                'orderId' => $order->get_order_number(),
                                'message' => __('Order has local pickup shipping method', $this->launcher->getPluginSlug()),
                            ];
                            continue;
                        }

                        $paymentMethod = $order->get_payment_method();
                        $total = $order->get_total();

                        $addr = new ShipmentAddress(
                            $this->filter_shipping_city($order),
                            $this->filter_shipping_postcode($order),
                            $this->filter_shipping_country($order),
                            $this->filter_shipping_address_1($order),
                            $this->filter_name($order),
                            $this->filter_contact_person($order),
                            $this->filter_phone($order),
                            $this->filter_email($order),
                            $this->filter_phone($order)
                        );

                        $ar_packages = [];
                        $package = new WebServicePackage($order->get_order_number(), "1.00");
                        $ar_packages[] = $package;


                        $paymentMethod = $this->filter_cod_shipping_plugins_support($paymentMethod);

                        $webServiceShipment = new WebServiceShipment(
                            ($paymentMethod == 'cod') ? new Cod(round(floatval($total), 2)) : null,
                            (!$this->options['insurvalue']) ? $order->get_total() : floatval($this->options['insurvalue']),
                            (!$this->options['notifytype']) ? 0 : $this->options['notifytype'],
                            (!$this->options['productdesc']) ? '' : $this->options['productdesc'],
                            ($paymentMethod == 'cod') ? 1 : 0,
                            (!$this->options['returnshipment']) ? 0 : $this->options['returnshipment'],
                            (!$this->options['saturdayshipment']) ? 0 : $this->options['saturdayshipment'],
                            (!$this->options['servicename']) ? 'expres' : $this->options['servicename'],
                            null,
                            (!$this->options['units']) ? 'kg' : $this->options['units'],
                            $ar_packages,
                            null,
                            $addr,
                            null,
                            codAttribute::CASH, null);

                        $shipment = new createCifShipment($this->options['username'], $this->options['password'], $webServiceShipment, webServiceShipmentType::TLAC);


                        $result = $client->createCifShipment($shipment);
                        $errors = $result->getCreateCifShipmentReturn()->getResult()->getErrors();


                        // Check for errors

                        if ($errors) {
                            $this->unsuccessful[] = [
                                'orderId' => $order->get_order_number(),
                                'message' => __($errors, $this->launcher->getPluginSlug()),
                            ];

                        } else {
                            $this->successful[] = [
                                'orderId' => $postId,
                            ];
                        }

                    }

                    $printLabels = new printShipmentLabels($this->options['username'], $this->options['password']);
                    $result = $client->printShipmentLabels($printLabels);

                    if (is_soap_fault($result) || !$result->getPrintShipmentLabelsReturn()->getDocumentUrl()) {
                        $stitkyDocument = null;
                    } else {
                        $stitkyDocument = urlencode($result->getPrintShipmentLabelsReturn()->getDocumentUrl());
                    }

                    $printEndOfDay = new printEndOfDay($this->options['username'], $this->options['password']);
                    $result = $client->printEndOfDay($printEndOfDay);

                    if (is_soap_fault($result) || !$result->getprintEndOfDayReturn()->getDocumentUrl()) {
                        $endOfDayDocument = null;
                    } else {
                        $endOfDayDocument = urlencode($result->getprintEndOfDayReturn()->getDocumentUrl());
                    }

                    $sucessfull = urlencode(serialize($this->successful));
                    $unsucessfull = urlencode(serialize($this->unsuccessful));


                    $sendback = add_query_arg([
                        'sps-sucessfull'   => $sucessfull,
                        'sps-unsucessfull' => $unsucessfull,
                        'stitky-url'       => $stitkyDocument,
                        'endofday-url'     => $endOfDayDocument,
                    ], $sendback);
                    $sendback = remove_query_arg([
                        'action',
                        'action2',
                        'tags_input',
                        'post_author',
                        'comment_status',
                        'ping_status',
                        '_status',
                        'post',
                        'bulk_edit',
                        'post_view',
                    ], $sendback);
                    wp_redirect($sendback);
                    exit();
                    break;
                default:
                    return;
            }
        }
    }

    /**
     * Displays the notice
     */
    function custom_bulk_admin_notices()
    {
        global $post_type, $pagenow;

        if ($pagenow == 'edit.php' && $post_type == 'shop_order' && (isset($_REQUEST['sps-sucessfull']) || isset($_REQUEST['sps-unsucessfull']) || isset($_REQUEST['document-url']))) {
            $sucessfull = unserialize(str_replace('\\', '', urldecode($_REQUEST['sps-sucessfull'])));
            $unsucessfull = unserialize(str_replace('\\', '', urldecode($_REQUEST['sps-unsucessfull'])));
            $stitkyDocument = urldecode($_REQUEST['stitky-url']);
            $endOfDayDocument = urldecode($_REQUEST['endofday-url']);
            if (count($sucessfull) != 0 || $stitkyDocument || $endOfDayDocument) {
                echo "<div class=\"updated\">";
                foreach ($sucessfull as $message) {
                    $messageContent = sprintf(__('Order no. %s Sucessfully generated', $this->launcher->getPluginSlug()), $message['orderId']);
                    echo "<p>{$messageContent}</p>";
                }
                if (isset($stitkyDocument)) {
                    echo '<p><a target="_blank" href="' . $stitkyDocument . '">Stiahnuť štítky</a></p>';
                }
                if (isset($endOfDayDocument)) {
                    echo '<p><a target="_blank" href="' . $endOfDayDocument . '">Stiahnuť preberaci protokol</a></p>';
                }
                echo "</div>";
            }
            if (count($unsucessfull) != 0) {
                echo "<div class=\"error\">";
                foreach ($unsucessfull as $message) {
                    $messageContent = sprintf(__('Order no. %s Was not generated. Error : %s', $this->launcher->getPluginSlug()), $message['orderId'], $message['message']);
                    echo "<p>{$messageContent}</p>";
                }
                echo "</div>";
            }

        }
    }

    function filter_cod_shipping_plugins_support($paymentType){
        switch($paymentType){
            case 'dobirka' :
                return 'cod';
                break;
            case 'codpf' :
                return 'cod';
                break;
            default:
                return $paymentType;
                break;
        }
    }

    function filter_order_id($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->id;
        } else {
            return $order->get_id();
        }
    }

    function filter_name($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            if (isset($order->shipping_company) && $order->shipping_company) {
                return $order->shipping_company;
            } else {
                return $order->shipping_first_name . ' ' . $order->shipping_last_name;
            }
        } else {
            if ($order->get_shipping_company()) {
                return $order->get_shipping_company();
            } else {
                return $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
            }
        }
    }

    function filter_contact_person($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->shipping_first_name . ' ' . $order->shipping_last_name;
        } else {
            return $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
        }
    }

    function filter_phone($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            if ($order->shipping_phone != '') {
                return $order->shipping_phone;
            } else {
                return $order->billing_phone;
            }
        } else {
            return $order->get_billing_phone();
        }
    }

    function filter_email($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            if ($order->shipping_email != '') {
                return $order->shipping_email;
            } else {
                return $order->billing_email;
            }
        } else {
            return $order->get_billing_email();
        }
    }

    function filter_shipping_address_1($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->shipping_address_1;
        } else {
            return $order->get_shipping_address_1();
        }
    }

    function filter_shipping_city($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->shipping_city;
        } else {
            return $order->get_shipping_city();
        }
    }

    function filter_shipping_postcode($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->shipping_postcode;
        } else {
            return $order->get_shipping_postcode();
        }
    }

    function filter_shipping_country($order)
    {
        if (!defined('WC_VERSION')) {
            return '';
        }
        if (version_compare(WC_VERSION, '3.0', '<')) {
            return $order->shipping_country;
        } else {
            return $order->get_shipping_country();
        }
    }

}