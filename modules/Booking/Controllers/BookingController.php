<?php
namespace Modules\Booking\Controllers;

use DebugBar\DebugBar;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use App\Helpers\ReCaptchaEngine;
use Modules\Booking\Gateways\EasypaisaGateway;

class BookingController extends \App\Http\Controllers\Controller
{
    use AuthorizesRequests;
    protected $booking;

    public function __construct()
    {
        $this->booking = Booking::class;
    }

    public function checkout()
    {
        // $test =  new EasypaisaGateway();
        // $authToken = $test->getAuthToken();
        // echo $authToken; die;
        // $test->verifyToken($authToken); die;
        if(!Cart::count()){
            return redirect()->route('booking.cart');
        }

        $data = [
            'avatar_url'=>'asdf',
            'page_title' => __('Checkout'),
            'gateways'   => $this->getGateways(),
            'user'       => Auth::user(),
            'breadcrumbs'=>[
                ['name'=>__("Checkout"),'class'=>'active']
            ]
        ];
        return view('Booking::frontend.checkout', $data);
    }

    public function cart()
    {
        $data = [
            'page_title' => __('Cart'),
            'user'       => Auth::user(),
            'breadcrumbs'=>[
                ['name'=>__("Cart"),'class'=>'active']
            ]
        ];
        return view('Booking::frontend.cart', $data);
    }

    public function checkStatusCheckout($code)
    {
        $booking = $this->booking::where('code', $code)->first();
        $data = [
            'error'    => false,
            'message'  => '',
            'redirect' => ''
        ];
        if (empty($booking)) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if ($booking->customer_id != Auth::id()) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if ($booking->status != 'draft') {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        return response()->json($data, 200);
    }

    public function doCheckout(Request $request)
    {

        if(!Cart::count()){
           return $this->sendError(__("Your cart is empty"));
        }
        /**
         * Google ReCapcha
         */
        if(ReCaptchaEngine::isEnable() and setting_item("booking_enable_recaptcha")){
            $codeCapcha = $request->input('g-recaptcha-response');
            if(!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)){
               return $this->sendError(__("Please verify the captcha"));
            }
        }
        $rules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255',
            'phone'           => 'required|string|max:255',
            'country' => 'required',
            'payment_gateway' => 'required',
            'term_conditions' => 'required'
        ];
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return $this->sendError('', ['errors' => $validator->errors()]);
            }
        }
        if (!empty($rules['payment_gateway'])) {
            $payment_gateway = $request->input('payment_gateway');
            $gateways = get_payment_gateways();
            if (empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
               return $this->sendError(__("Payment gateway not found"));
            }
            $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);
            if (!$gatewayObj->isAvailable()) {
               return $this->sendError(__("Payment gateway is not available"));
            }
        }
        try {
            $booking = new Booking();
            // Normal Checkout
            $booking->status = 'draft';
            $booking->first_name = $request->input('first_name');
            $booking->last_name = $request->input('last_name');
            $booking->email = $request->input('email');
            $booking->phone = $request->input('phone');
            $booking->address = $request->input('address_line_1');
            $booking->address2 = $request->input('address_line_2');
            $booking->city = $request->input('city');
            $booking->state = $request->input('state');
            $booking->zip_code = $request->input('zip_code');
            $booking->country = $request->input('country');
            $booking->gateway = $payment_gateway;
            $booking->total = Cart::total();
            $booking->customer_id = Auth::id();
            $booking->save();
            $booking->saveItems();


            $user = Auth::user();
            $user->billing_first_name = $request->input('first_name');
            $user->billing_last_name = $request->input('last_name');
            $user->billing_phone = $request->input('phone');
            $user->billing_address = $request->input('address_line_1');
            $user->billing_address2 = $request->input('address_line_2');
            $user->billing_city = $request->input('city');
            $user->billing_state = $request->input('state');
            $user->billing_zip_code = $request->input('zip_code');
            $user->billing_country = $request->input('country');
            $user->save();

            $booking->addMeta('locale', app()->getLocale());
            Cart::destroy();
            return $gatewayObj->process($request, $booking);

        }catch (\Exception $exception)
        {
            $booking->delete();

           return $this->sendError($exception->getMessage());
        }
    }

    public function confirmPayment(Request $request, $gateway)
    {
        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
           return $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
           return $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->confirmPayment($request);
    }

    public function cancelPayment(Request $request, $gateway)
    {

        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
           return $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
           return $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->cancelPayment($request);
    }

    /**
     * @todo Handle Add To Cart Validate
     *
     * @param Request $request
     * @return string json
     */
    public function addToCart(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id'   => 'required|integer',
            'type' => 'required'
        ]);
        if ($validator->fails()) {
           return $this->sendError('', ['errors' => $validator->errors()]);
        }
        $service_type = $request->input('type');
        $service_id = $request->input('id');
        $allServices = get_bookable_services();
        if (empty($allServices[$service_type])) {
           return $this->sendError(__('Service type not found'));
        }
        $module = $allServices[$service_type];
        $service = $module::find($service_id);

        if (empty($service) or !is_subclass_of($service, '\\Modules\\Booking\\Models\\Bookable')) {
           return $this->sendError(__('Service not found'));
        }
        if (!$service->isBookable()) {
           return $this->sendError(__('Service is not bookable'));
        }
        return $service->addToCart($request);
    }

    protected function getGateways()
    {

        $all = get_payment_gateways();
        $res = [];
        foreach ($all as $k => $item) {
            if (class_exists($item)) {
                $obj = new $item($k);
                if ($obj->isAvailable()) {
                    $res[$k] = $obj;
                }
            }
        }
        return $res;
    }

    public function detail(Request $request, $code)
    {

        $booking = Booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }

        if ($booking->status == 'draft') {
            return redirect($booking->getCheckoutUrl());
        }
        if ($booking->customer_id != Auth::id()) {
            abort(404);
        }
        $data = [
            'page_title' => __('Order Details'),
            'booking'    => $booking,
            'hideBc'=>1
        ];
        if ($booking->gateway) {
            $data['gateway'] = get_payment_gateway_obj($booking->gateway);
        }
        return view('Booking::frontend/detail', $data);
    }

    public function removeCartItem(Request $request){
        $validator = Validator::make($request->all(), [
            'id'   => 'required',
        ]);
        if ($validator->fails()) {
           return $this->sendError('', ['errors' => $validator->errors()]);
        }

        Cart::remove($request->input('id'));

        return $this->sendSuccess([
            'fragments'=>get_cart_fragments(),
            'reload'=>Cart::count()  ? false: true,
        ],__("Item removed"));
    }

	public function exportIcal($service_type = 'tour', $id)
	{
		\Debugbar::disable();
		$allServices = get_bookable_services();
		if (empty($allServices[$service_type])) {
			return $this->sendError(__('Service type not found'));
		}
		$module = $allServices[$service_type];

		$path ='/ical/';
		$fileName = 'booking_' . $service_type . '_' . $id . '.ics';
		$fullPath = $path.$fileName;

		$content  = $this->booking::getContentCalendarIcal($service_type,$id,$module);
		Storage::disk('uploads')->put($fullPath, $content);
		$file = Storage::disk('uploads')->get($fullPath);

		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $fileName . '"');

		echo ($file);
	}


}
