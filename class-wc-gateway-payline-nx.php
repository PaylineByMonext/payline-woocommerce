<?php

use Payline\PaylineSDK;

/**
 * Payline module for WooCommerce
 *
 * @class 		WC_Payline
 * @package		WooCommerce
 * @category	Payment Gateways
 *
 * WC tested up to: 4.0.1
 */


class WC_Gateway_Payline_NX extends WC_Abstract_Recurring_Payline_NX {


    protected $paymentMode = 'NX';

    public $id = 'payline_nx';

    public $method_title = 'Payline N Fois';

    function init_form_fields()
    {
        parent::init_form_fields();

        $this->form_fields['billing_left'] = array(
            'title' => __('Billing left', 'payline'),
            'default' => '3',
            'type' => 'text',
            'description' => __('Recurring billing number. Mandatory field', 'payline')
        );
    }

    /**
     * @see https://docs.payline.com/pages/viewpage.action?pageId=747147142
     * @param WC_Refund|bool|WC_Order $order
     * @return mixed|void
     */
    protected function getWebPaymentRequest(WC_Order $order) {

        $requestParams = parent::getWebPaymentRequest($order);

        $billingLeft = (int)$this->settings['billing_left'];
        if(empty($billingLeft) or $billingLeft<2) {
            $billingLeft = 2;
        }
        $requestParams['recurring']['billingLeft'] = $billingLeft; // Nombre d’échéance

        $totalAmount = $requestParams['payment']['amount'];
        $recurringAmount = round(( $totalAmount / 100 ) / $billingLeft) * 100;

        $requestParams['recurring']['firstAmount'] = round($totalAmount - ($recurringAmount * ($billingLeft-1)));


        $requestParams['recurring']['amount'] = $recurringAmount;
        $requestParams['recurring']['billingCycle'] = $this->settings['billing_cycle'];


        $requestParams['recurring']['billingDay'] = '01'; //  [01 à 30]

        //$today    = current_time( 'd/m/Y' );
        $requestParams['recurring']['startDate'] = ''; // dd/mm/yyyy

        do_action('payline_before_do_web_payment_nx', $requestParams, $this);

        return $requestParams;
    }

    /**
     * Check if the gateway is available for use.
     *
     * @return bool
     */
    public function is_available() {

        $is_available = parent::is_available();
        $cart = WC()->cart;
        if ($is_available && $cart) {
            return $this->settings['billing_left'] > 0;
        }
        return $is_available;
    }

}
