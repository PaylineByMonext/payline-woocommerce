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


if ( ! class_exists( 'WC_Abstract_Payline', false ) ) {
    include_once 'class-wc-abstract-payline.php';
}


abstract class WC_Abstract_Recurring_Payline_NX extends WC_Abstract_Payline {


    protected $paymentMode = 'NX';

    public $id = 'payline_nx';

    public $method_title = 'Payline N Fois';

    function init_form_fields() {
        parent::init_form_fields();

        $this->form_fields['payment_action']['default'] = '101';
        $this->form_fields['payment_action']['options'] = array(
            '101' => __('Authorization + Capture', 'payline')
        );

        /*
         * Payment Settings
         */
        $this->form_fields[$this->id] = array(
            'title' => strtoupper(__($this->method_title, 'payline' )),
            'type' => 'title'
        );

        $this->form_fields['billing_cycle'] = array(
            'title' => __('Payment frequencies', 'payline'),
            'type' => 'select',
            'default' => '40',
            'options' => array(
                '10' => __('One transaction per day', 'payline'),
                '20' => __('One transaction every seven days', 'payline'),
                '30' => __('Two transactions per month', 'payline'),
                '40' => __('One transaction per month', 'payline'),
                '50' => __('One transaction every two months', 'payline'),
                '60' => __('One transaction every three months', 'payline'),
                '70' => __('One transaction every six months', 'payline'),
                '80' => __('One transaction per year', 'payline'),
                '90' => __('A transaction every two years', 'payline')
            )
        );

    }

    protected function getDaysForCycles($code)
    {
        $cyclesDays = array('10' => 1,
            '20' => 7,
            '30' => 15,
            '40' => 30,
            '50' => 60,
            '60' => 90,
            '70' => 180,
            '80' => 360,
            '90' => 720
        );

        return !empty($cyclesDays[$code]) ? $cyclesDays[$code] : 0;
    }


    /**
     * @param WC_Order $order
     * @param array $res
     * @return false
     */
    protected function paylineCancelWebPaymentDetails(WC_Order $order, array $res) {
        return false;
    }

    /**
     * @param WC_Order $order
     * @param array $res
     * @return bool
     */
    protected function paylineSuccessWebPaymentDetails(WC_Order $order, array $res) {

        if($res['result']['code'] == '02500') {
            $orderId = $order->get_id();

            // Store transaction details
            update_post_meta((int) $orderId, 'Transaction ID', $res['transaction']['id']);

            update_post_meta((int) $orderId, '_contract_number', $res['payment']['contractNumber']);

            $order->payment_complete($res['transaction']['id']);

            $order->update_status('completed', 'First payment validated');
            return true;
        }

        return false;
    }

}
