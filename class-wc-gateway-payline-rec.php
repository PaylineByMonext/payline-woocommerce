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


if ( ! class_exists( 'WC_Abstract_Recurring_Payline_NX', false ) ) {
    include_once 'class-wc-abstract-recurring-payline.php';
}


class WC_Gateway_Payline_REC extends WC_Abstract_Recurring_Payline_NX {


    protected $paymentMode = 'REC';

    public $id = 'payline_rec';

    public $method_title = 'Payline par Abonnement';

    function init_form_fields()
    {
        parent::init_form_fields();

        $this->form_fields['max_records'] = array(
            'title' => __('Maximum records', 'payline'),
            'default' => '36',
            'type' => 'text',
            'description' => __('Set a number maximum of records or leave empty', 'payline')
        );
    }

    /**
     * @see https://docs.payline.com/pages/viewpage.action?pageId=747147142
     * @param WC_Refund|bool|WC_Order $order
     * @return mixed|void
     */
    protected function getWebPaymentRequest(WC_Order $order) {

        $requestParams = parent::getWebPaymentRequest($order);

        $totalAmount = $requestParams['payment']['amount'];
        $requestParams['recurring']['firstAmount'] = $totalAmount;

        $requestParams['recurring']['amount'] = $totalAmount;
        $requestParams['recurring']['billingCycle'] = $this->settings['billing_cycle'];


        $requestParams['recurring']['billingDay'] = '01'; //  [01 à 30]

        //$today    = current_time( 'd/m/Y' );
        $requestParams['recurring']['startDate'] = ''; // dd/mm/yyyy


        if($this->settings['billing_cycle'] && $this->settings['max_records']) {
            $numberDaysToLastDate = $this->getDaysForCycles($this->settings['billing_cycle']) * $this->settings['max_records'];
            $requestParams['recurring']['endDate'] = current_datetime()->modify( '+' .$numberDaysToLastDate. ' day' )->format( 'd/m/Y' );;

        }

        do_action('payline_before_do_web_payment_rec', $requestParams, $this);

        return $requestParams;
    }
}
