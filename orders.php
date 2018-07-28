<?php

/* Receive callbacks from SamCart and post to Improvely  */

$requestBody = file_get_contents('php://input');
$inputData = json_decode($requestBody);

class SC_Data {

    private $sc_type;
    private $sc_api_key;
    private $product;
    private $customer;
    private $order;
    private $affiliate;
    private $woopra_project = 'startupbros.com'; // set this to your woopra host

    function __construct($sc_data)
    {
        $this->sc_type = $sc_data->type;
        $this->sc_api_key = $sc_data->api_key;
        $this->product = $sc_data->product;
        $this->customer = $sc_data->customer;
        $this->order = $sc_data->order;
        $this->affiliate = $sc_data->affiliate;
        $this->coupon = $sc_data->coupon;
    }

    function get_post_data()
    {
        $revenue = $this->product->price;
        $woopra_event = $this->get_goal($this->sc_type);
        if ($woopra_event == "Refund") {
            $revenue = -1 * abs($revenue);
        }

        $post_data = array(
            'project' => $this->woopra_project,
            'event' => $woopra_event,
            'cv_email' => $this->customer->email,
            'cv_firstname' => $this->customer->first_name,
            'cv_lastname' => $this->customer->last_name,
            'cv_phone_number' => $this->customer->phone_number,
            'ce_product' => $this->product->name,
            'ce_reference' => $this->order->id,
            'ce_revenue' => $revenue,
            'ce_affiliate' => $this->affiliate->id,
            'ce_coupon' => $this->order->coupon,
            'ip' => $this->order->ip_address,
        );

        return $post_data;
    }

    function get_goal($sc_type)
    {
        switch ($sc_type) {
            case "Order":
                $goal = "Order";
                break;
            case "Refund":
                $goal = "Refund";
                break;
            case "Cancel":
                $goal = "Cancel";
                break;
            case "RecurringPaymentFailed":
                $goal = "RecurringPaymentFailed";
                break;
            case "RecurringPaymentRecovered":
                $goal = "RecurringPaymentRecovered";
                break;
            case "RecurringPaymentSucceeded":
                $goal = "RecurringPaymentSucceeded";
                break;
            default:
                $goal = "Samcart";
        }
        return $goal;
    }

}


class Custom_Curl {
    private $url = "http://www.woopra.com/track/ce";
    private $options;
    private $ch;
    private $response_data;

    function __construct($post_data)
    {
        $this->options = array(
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => true,         // return web page
            CURLOPT_HEADER         => false,        // (don't) return headers
            CURLOPT_FOLLOWLOCATION => true,         // follow redirects
            CURLOPT_ENCODING       => "utf-8",      // handle all encodings
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 20,           // timeout on connect
            CURLOPT_TIMEOUT        => 20,           // timeout on response
            CURLOPT_POST           => 1,            // i am sending post data
            CURLOPT_POSTFIELDS     => http_build_query($post_data),        // POST vars, Raw JSON, etc
            CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false,        //
            CURLOPT_VERBOSE        => 1,      // this is my original header
            CURLOPT_HTTPHEADER     => array('Content-Type: application/x-www-form-urlencoded'),
        );
        $this->ch = curl_init();
    }

    function send()
    {
        curl_setopt_array($this->ch, $this->options);

        $this->response_data = curl_exec($this->ch);
        curl_close($this->ch);
        return $this->response_data;
    }
}


$sc_data = new SC_Data($inputData);
$post_data = $sc_data->get_post_data();


//file_put_contents("data.json",json_encode($post_data). PHP_EOL, FILE_APPEND);
//exit();

$custom_curl = new Custom_Curl($post_data);
$response_data = $custom_curl->send();

//file_put_contents("data.json",json_encode($response_data). PHP_EOL, FILE_APPEND);
//exit();

$everything = get_defined_vars();
ksort($everything);
$all_variables = print_r($everything, true);

// send debug email
$msg = $post_data . "\n\n" . print_r($response_data, true) . "\n\n" . $all_variables;
mail("EMAIL","SamCart Integration Event Log", $msg);

// Send a 200 OK back to the Samcart server
$httpStatusCode = 200;
$httpStatusMsg  = 'OK';
$phpSapiName    = substr(php_sapi_name(), 0, 3);
if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
    header('Status: '.$httpStatusCode.' '.$httpStatusMsg);
} else {
    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    header($protocol.' '.$httpStatusCode.' '.$httpStatusMsg);
}
//file_put_contents("data.json",json_encode($response_data). PHP_EOL, FILE_APPEND);
?>