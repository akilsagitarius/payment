<?php

namespace Akill\Payment\App;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Akill\Payment\Midtrans\Midtrans;
use Akill\Payment\Midtrans\Veritrans;
use Akill\Message\Message;
use Akill\Payment\Helpers\InMethodPayment;
use phpDocumentor\Reflection\Types\Array_;

class MidtransController extends AbstractPayment implements InMethodPayment
{
    /**
     * MidtransController constructor.
     * @param null $transaction_details
     */
    public function __construct($transaction_details = null){
        Midtrans::$serverKey = config('payment.midtrans_server_key');
        Midtrans::$isProduction = config('payment.production');

        Veritrans::$serverKey = config('payment.midtrans_server_key');
        Veritrans::$isProduction = config('payment.production');

        if(!Midtrans::$serverKey) {
            return die('Please complete the Midtrans settings');
        }
        if(!Veritrans::$serverKey) {
            return die('Please complete the Midtrans settings');
        }

        $this->create($transaction_details);
    }

    /** Actually send request to MIDTRANS server
     *
     * @param array transaction_details ['order_id', 'gross_amount']
     * @return MidtransController
     */
    public function create($transaction_details = null){
        $this->transaction_data = array('transaction_details'=> $transaction_details);

        try
        {
            $token = null;

            $result = [
                'unique_code' => $transaction_details['order_id'],
                'token' => $token,
                'amount' => $transaction_details['gross_amount']
            ];

            $this->transaction_data['result'] = $result;
            return $this;
        }
        catch (Exception $e)
        {
            return Message::Error(400)->get();
        }
    }

    /** Generate data customer request to MIDTRANS server
     *
     * @param array customer_details [
     *      'first_name', //optional
     *      'last_name', //optional
     *      'email',
     *      'phone',
     *      'billing_address', //optional
     *      'shipping_address' //optional
     * ]
     * @param null $billing_address
     * @param null $shipping_address
     * @return MidtransController
     * @throws Exception
     */
    public function customer($customer_details, $billing_address = null, $shipping_address = null){

        $this->customer_details = $customer_details;

        if( empty($billing_address) && empty($shipping_address)) {
            return $this;
        }

        if(!is_array($billing_address) && !is_array($shipping_address)) {
            throw new Exception('Billing address not array');
        }

        // Optional
        $this->billing_address = $billing_address;

        // Optional
        $this->shipping_address = $shipping_address;

        $this->customer_details['billing_address'] = $this->billing_address;
        $this->customer_details['shipping_address'] = $this->shipping_address;
        $this->transaction_data['customer_details'] = $this->customer_details;

        return $this;
    }

    /** Credit Card secure
     *
     * @param boolean $bool
     * @return MidtransController
     */
    public function cc($bool){
        $this->transaction_data['credit_card'] = array('secure' => $bool);
        return $this;
    }

    /** Expiry time active
     *
     * @param int  $duration
     * @return MidtransController
     */
    public function expiry(int $duration = 1){
        $time = time();
        $custom_expiry = array(
            'start_time' => date("Y-m-d H:i:s O",$time),
            'unit'       => 'hour',
            'duration'   => $duration
        );
        $this->transaction_data['custom_expiry'] = $custom_expiry;
        return $this;
    }

    /**
   	* Retrieve transaction status
   	* @param string $id Order ID or transaction ID
    * @return mixed[]
    */
    public function status($id){
        $result = Veritrans::status($id);
        return response()->json($result);
    }

  	/**
   	* Appove challenge transaction
   	* @param string $id Order ID or transaction ID
   	* @return string
   	*/
    public function approve($id){
        $result = Veritrans::approve($id);
        return response()->json($result);
    }

  	/**
   	* Cancel transaction before it's setteled
   	* @param string $id Order ID or transaction ID
   	* @return string
   	*/
    public function cancel($id){
        $result = Veritrans::cancel($id);
        return response()->json($result);
    }

   /**
    * Expire transaction before it's setteled
    * @param string $id Order ID or transaction ID
    * @return mixed[]
    */
    public function expire($id){
        $result = Veritrans::expire($id);
        return response()->json($result);
    }
}
