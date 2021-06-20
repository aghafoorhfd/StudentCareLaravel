<?php
namespace Modules\Booking\Gateways;

use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\Booking\Events\BookingCreatedEvent;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Payment;
use Omnipay\Omnipay;
use Omnipay\Stripe\Gateway;
use PHPUnit\Framework\Error\Warning;
use Validator;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;

use App\Helpers\Assets;

class EasypaisaGateway extends BaseGateway
{
    protected $id = 'easypaisa';

    public $name = 'Easypaisa Checkout';

    protected $gateway;

    public function getOptionsConfigs()
    {
        return [
            [
                'type'  => 'checkbox',
                'id'    => 'enable',
                'label' => __('Enable Stripe Standard?')
            ],
            [
                'type'  => 'input',
                'id'    => 'name',
                'label' => __('Custom Name'),
                'std'   => __("Stripe")
            ],
            [
                'type'  => 'upload',
                'id'    => 'logo_id',
                'label' => __('Custom Logo'),
            ],
            [
                'type'  => 'editor',
                'id'    => 'html',
                'label' => __('Custom HTML Description')
            ],
            [
                'type'       => 'input',
                'id'        => 'stripe_secret_key',
                'label'     => __('Secret Key'),
            ],
            [
                'type'       => 'input',
                'id'        => 'stripe_publishable_key',
                'label'     => __('Publishable Key'),
            ],
            [
                'type'       => 'checkbox',
                'id'        => 'stripe_enable_sandbox',
                'label'     => __('Enable Sandbox Mode'),
            ],
            [
                'type'       => 'input',
                'id'        => 'stripe_test_secret_key',
                'label'     => __('Test Secret Key'),
            ],
            [
                'type'       => 'input',
                'id'        => 'stripe_test_publishable_key',
                'label'     => __('Test Publishable Key'),
            ]
        ];
    }

    public function process(Request $request, $booking)
    {
        if (in_array($booking->status, [
            $booking::PAID,
            $booking::COMPLETED,
            $booking::CANCELLED
        ])) {

            throw new Exception(__("Order status does need to be paid"));
        }
        if (!$booking->total) {
            throw new Exception(__("Order total is zero. Can not process payment gateway!"));
        }
        $this->getGateway();
        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->payment_gateway = $this->id;
        $payment->status = 'draft';

        $authToken = $this->getAuthToken($booking);
        if (!empty($authToken)) {

            $payment->save();
            $booking->status = $booking::UNPAID;
            $booking->payment_id = $payment->id;
            $booking->save();
            // redirect to offsite payment gateway
            return response()->json([
                'authToken' => $authToken
            ]);
        } else {
            throw new Exception("Easypaisa Gateway: Couldn't complete the request, Token is missing.");
        }
    }

    public function getGateway()
    {
        $this->gateway = Omnipay::create('Stripe');
        $this->gateway->setApiKey($this->getOption('stripe_secret_key'));
        if ($this->getOption('stripe_enable_sandbox')) {
            $this->gateway->setApiKey($this->getOption('stripe_test_secret_key'));
        }
    }

    public function confirmPayment(Request $request)
    {
        $data = $request->all();

        $c = $request->query('orderRefNumber');
        $booking = Booking::where('code', $c)->first();
        // echo '<pre>'; print_r($data); die;
        if (!empty($booking) and in_array($booking->status, [$booking::UNPAID])) {
            if (empty($data['message'])) {
                $payment = $booking->payment;
                if ($payment) {
                    $payment->status = 'completed';
                    $payment->logs = json_encode($data);
                    $payment->save();
                }
                try{
                    $booking->markAsPaid();

                } catch(\Swift_TransportException $e){
                    Log::warning($e->getMessage());
                }
                return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
            } else {

                $payment = $booking->payment;
                if ($payment) {
                    $payment->status = 'fail';
                    $payment->logs = json_encode($data);
                    $payment->save();
                }
                try{
                    $booking->markAsPaymentFailed();

                } catch(\Swift_TransportException $e){
                    Log::warning($e->getMessage());
                }
                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
            }
        }
        if (!empty($booking)) {
            return redirect($booking->getDetailUrl(false));
        } else {
            return redirect(url('/'));
        }

    }

    public function handlePurchaseData($data, $booking, $request)
    {
        $data['currency'] = setting_item('currency_main');
        $data['token'] = $request->input("token");
        $data['description'] = __("Edumy");
        return $data;
    }
    public function getDisplayHtml()
    {
        // $post_data = $this->getAuthToken();
        $data['auth_token'] = '';
        $data['postBackURL'] = env('APP_URL').'/booking/confirm/easypaisa';
        return view("Booking::frontend.gateways.easypaisa_autosubmit", compact('data'));
    }
    public function getAuthToken($booking)
    {
        date_default_timezone_set("Asia/Karachi");
        $amount = (float)$booking->total;
        $DateTime 	 = new \DateTime();
        $orderRefNum = $booking->code;//$DateTime->format('YmdHis');
        // echo $booking->code; die;
        $ExpiryDateTime = $DateTime;
        $ExpiryDateTime->modify('+' . 1 . ' hours');
        $expiryDate = $ExpiryDateTime->format('Ymd His');
        $post_data =  array(
            "storeId" 			=> 70126,
            "amount" 			=> $amount,
            "postBackURL" 		=> env('APP_URL').'/booking/confirm/easypaisa',
            "orderRefNum" 		=> $orderRefNum,
            "expiryDate" 		=> $expiryDate, 	  	//Optional
            "merchantHashedReq" => "",				  	//Optional
            "autoRedirect" 		=> "1",				  	//Optional
            "paymentMethod" 	=> "MA_PAYMENT_METHOD",	//Optional

        );

        $sortedArray = $post_data;
        ksort($sortedArray);
        $sorted_string = '';
        $i = 1;
        foreach($sortedArray as $key => $value){
            if(!empty($value))
            {
                if($i == 1)
                {
                    $sorted_string = $key. '=' .$value;
                }
                else
                {
                    $sorted_string = $sorted_string . '&' . $key. '=' .$value;
                }
            }
            $i++;
        }	
        // AES/ECB/PKCS5Padding algorithm
        $cipher = "aes-128-ecb";
        $crypttext = openssl_encrypt($sorted_string, $cipher, 'FTP0EKH68SWIJC5K', OPENSSL_RAW_DATA);
        $HashedRequest = Base64_encode($crypttext);
        //NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN

        $post_data['merchantHashedReq'] =  $HashedRequest;
        $authToken = $this->getEasypaisaToken($post_data);
        return $authToken;
        // return view("Booking::frontend.gateways.easypaisa", compact('post_data'));
    }

    // public function verifyToken($authToken){
    //     var_dump($authToken); 
    //     $postData = ['auth_token'=>$authToken,'postBackURL'=>'http://dummy-url.com'];
    //     $response = Http::asForm()->post('https://easypay.easypaisa.com.pk/easypay/Confirm.jsf', $postData);
    //     echo '<pre>'; print_r($response); die;
    //     // if($response->successful()){
    //     //     if (preg_match('/auth_token=(.*?)&postBackURL/', serialize($response), $match) == 1) {
    //     //         $token = $match[1];
    //     //     }
    //     // }
    // }

    private function getEasypaisaToken($postData){
        $token='';
        $url = 'https://easypay.easypaisa.com.pk/easypay/Index.jsf';
        $fields_string ='';
        
        //url-ify the data for the POST
        foreach($postData as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($postData));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        //close connection
        curl_close($ch);

        // print_r($info); die;
        if($result){
            if (preg_match('/auth_token=(.*?)&postBackURL/', $info['redirect_url'], $match) == 1) {
                $token = $match[1];
            }
        }
        // $response = Http::asForm()->post('https://easypay.easypaisa.com.pk/easypay/Index.jsf', $postData);
        // if($response->successful()){
        //     if (preg_match('/auth_token=(.*?)&postBackURL/', serialize($response), $match) == 1) {
        //         $token = $match[1];
        //     }
        // }
        // echo '<pre>'; print_r($response); die;
        return $token;
    }
}
