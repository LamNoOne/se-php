<?php
require_once dirname(__DIR__) . "/services/message.php";
class Payment
{
    public $id;
    public $order_id;
    public $invoice_id;
    public $transaction_id;
    public $payer_id;
    public $payer_name;
    public $payer_email;
    public $payer_country;
    public $merchant_id;
    public $merchant_email;
    public $paid_amount;
    public $paid_amount_currency;
    public $payment_source;
    public $payment_status;
    public $createdAt;
    public $updatedAt;

    public function __construct($id = null, $order_id = null, $invoice_id = null, $transaction_id = null, $payer_id = null, $payer_name = null, $payer_email = null, $payer_country = null, $merchant_id = null, $merchant_email = null, $paid_amount = null, $paid_amount_currency = null, $payment_source = null, $payment_status = null, $createdAt = null, $updatedAt = null)
    {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->invoice_id = $invoice_id;
        $this->transaction_id = $transaction_id;
        $this->payer_id = $payer_id;
        $this->payer_name = $payer_name;
        $this->payer_email = $payer_email;
        $this->payer_country = $payer_country;
        $this->merchant_id = $merchant_id;
        $this->merchant_email = $merchant_email;
        $this->paid_amount = $paid_amount;
        $this->paid_amount_currency = $paid_amount_currency;
        $this->payment_source = $payment_source;
        $this->payment_status = $payment_status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function getPayment($conn, $transaction_id)
    {
        try {
            $query = "SELECT id FROM payment WHERE transaction_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$transaction_id]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return false;
        }
    }

    public function createPayment($conn)
    {
        try {
            // Insert transaction data into the database 
            $query = "INSERT INTO payment (order_id,invoice_id,transaction_id,payer_id,payer_name,payer_email,payer_country,merchant_id,merchant_email,paid_amount,paid_amount_currency,payment_source,payment_status,createdAt) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $insert = $stmt->execute([
                $this->order_id,
                $this->invoice_id,
                $this->transaction_id,
                $this->payer_id,
                $this->payer_name,
                $this->payer_email,
                $this->payer_country,
                $this->merchant_id,
                $this->merchant_email,
                $this->paid_amount,
                $this->paid_amount_currency,
                $this->payment_source,
                $this->payment_status,
                $this->createdAt
            ]);
            if (!$insert)
                throw new Exception("Can not record your payment");
            return Message::messageData(true, "Payment recorded", ['payment_id' => $conn->lastInsertId()]);
        } catch (Exception $e) {
            return Message::message(false, "Can not record your payment");
        }
    }
}
