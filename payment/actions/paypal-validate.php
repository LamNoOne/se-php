<?php

// Include the PayPal API library 
require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . '/classes/controllers/paypal.php';
require_once dirname(dirname(__DIR__)) . '/classes/controllers/order.php';
$paypal = new PaypalCheckout();

$response = array('status' => 0, 'msg' => 'Transaction Failed!');
if (!empty($_POST['paypal_order_check']) && !empty($_POST['order_id'])) {
    $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
    // Validate and get order details with PayPal API 
    try {
        $invoice = $paypal->validate($_POST['order_id']);
    } catch (Exception $e) {
        $api_error = $e->getMessage();
    }

    if (!empty($invoice)) {
        $invoice_id = $invoice['id'];
        $intent = $invoice['intent'];
        $invoice_status = $invoice['status'];
        $invoice_time = date("Y-m-d H:i:s", strtotime($invoice['create_time']));

        if (!empty($invoice['purchase_units'][0])) {
            $purchase_unit = $invoice['purchase_units'][0];

            $order_id = $purchase_unit['custom_id'];

            if (!empty($purchase_unit['amount'])) {
                $currency_code = $purchase_unit['amount']['currency_code'];
                $amount_value = $purchase_unit['amount']['value'];
            }

            if (!empty($purchase_unit['payments']['captures'][0])) {
                $payment_capture = $purchase_unit['payments']['captures'][0];
                $transaction_id = $payment_capture['id'];
                $payment_status = $payment_capture['status'];
            }

            if (!empty($purchase_unit['payee'])) {
                $payee = $purchase_unit['payee'];
                $payee_email_address = $payee['email_address'];
                $merchant_id = $payee['merchant_id'];
            }
        }

        $payment_source = '';
        if (!empty($invoice['payment_source'])) {
            foreach ($invoice['payment_source'] as $key => $value) {
                $payment_source = $key;
            }
        }

        if (!empty($invoice['payer'])) {
            $payer = $invoice['payer'];
            $payer_id = $payer['payer_id'];
            $payer_name = $payer['name'];
            $payer_given_name = !empty($payer_name['given_name']) ? $payer_name['given_name'] : '';
            $payer_surname = !empty($payer_name['surname']) ? $payer_name['surname'] : '';
            $payer_full_name = trim($payer_given_name . ' ' . $payer_surname);
            $payer_full_name = filter_var($payer_full_name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

            $payer_email_address = $payer['email_address'];
            $payer_address = $payer['address'];
            $payer_country_code = !empty($payer_address['country_code']) ? $payer_address['country_code'] : '';
        }

        if (!empty($invoice_id) && $invoice_status == 'COMPLETED') {
            // Check if any transaction data is exists already with the same TXN ID 
            $row_id = Payment::getPayment($conn, $transaction_id);

            $payment_id = 0;
            if (!empty($row_id)) {
                $payment_id = $row_id;
            } else {
                // Insert transaction data into the database 
                $newPayment = new Payment(null, $order_id, $invoice_id, $transaction_id, $payer_id, $payer_full_name, $payer_email_address, $payer_country_code, $merchant_id, $payee_email_address, $amount_value, $currency_code, $payment_source, $payment_status, $invoice_time);
                $createdPaymentResponse = $newPayment->createPayment($conn);
                if ($createdPaymentResponse['status']) {
                    $payment_id = $createdPaymentResponse['data']['payment_id'];
                    $status = Order::updateOrderStatus($conn, $order_id, PAID)['status'];
                }
            }

            if (!empty($payment_id) && $status) {
                $ref_id_enc = base64_encode($transaction_id);
                $response = array('status' => 1, 'msg' => 'Transaction completed!', 'ref_id' => $ref_id_enc);
            }
        }
    } else {
        $response['msg'] = $api_error;
    }
}
echo json_encode($response);
