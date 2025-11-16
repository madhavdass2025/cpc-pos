<?php
class Sale {
    private $conn;
    private $table_name = "sales_invoices";

    public $id;
    public $invoice_number;
    public $invoice_date;
    public $customer_id;
    public $net_amount;
    public $user_id;
    public $payment_status;
    public $products; // This will be an array of products in the sale
    public $payment_method;
    public $amount_paid;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        try {
            $this->conn->beginTransaction();

            // 1. Create the sales invoice
            $invoice_query = "INSERT INTO " . $this->table_name . " SET invoice_number=:invoice_number, invoice_date=:invoice_date, customer_id=:customer_id, net_amount=:net_amount, user_id=:user_id, payment_status=:payment_status";
            $invoice_stmt = $this->conn->prepare($invoice_query);

            // Sanitize and bind
            $this->invoice_number=htmlspecialchars(strip_tags($this->invoice_number));
            $this->invoice_date=htmlspecialchars(strip_tags($this->invoice_date));
            $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
            $this->net_amount=htmlspecialchars(strip_tags($this->net_amount));
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->payment_status=htmlspecialchars(strip_tags($this->payment_status));

            $invoice_stmt->bindParam(":invoice_number", $this->invoice_number);
            $invoice_stmt->bindParam(":invoice_date", $this->invoice_date);
            $invoice_stmt->bindParam(":customer_id", $this->customer_id);
            $invoice_stmt->bindParam(":net_amount", $this->net_amount);
            $invoice_stmt->bindParam(":user_id", $this->user_id);
            $invoice_stmt->bindParam(":payment_status", $this->payment_status);

            if (!$invoice_stmt->execute()) {
                throw new Exception("Failed to create sales invoice.");
            }
            $this->id = $this->conn->lastInsertId();

            // 2. Record the payment
            $payment_query = "INSERT INTO payments SET invoice_id=:invoice_id, payment_method=:payment_method, amount_paid=:amount_paid";
            $payment_stmt = $this->conn->prepare($payment_query);

            // Sanitize and bind
            $this->payment_method=htmlspecialchars(strip_tags($this->payment_method));
            $this->amount_paid=htmlspecialchars(strip_tags($this->amount_paid));

            $payment_stmt->bindParam(":invoice_id", $this->id);
            $payment_stmt->bindParam(":payment_method", $this->payment_method);
            $payment_stmt->bindParam(":amount_paid", $this->amount_paid);

            if (!$payment_stmt->execute()) {
                throw new Exception("Failed to record payment.");
            }

            // 3. Handle credit ledger if payment_method is 'Credit'
            if ($this->payment_method === 'Credit') {
                $ledger_query = "INSERT INTO customer_ledger SET customer_id=:customer_id, transaction_type='Sale', reference_id=:reference_id, debit_amount=:debit_amount";
                $ledger_stmt = $this->conn->prepare($ledger_query);

                $ledger_stmt->bindParam(":customer_id", $this->customer_id);
                $ledger_stmt->bindParam(":reference_id", $this->id);
                $ledger_stmt->bindParam(":debit_amount", $this->net_amount);

                if (!$ledger_stmt->execute()) {
                    throw new Exception("Failed to update customer ledger.");
                }
            }

            // 4. Update stock (decrement quantity)
            foreach ($this->products as $product) {
                // FEFO Logic: Find the batch with the earliest expiry date that has enough stock.
                // This is a simplified version. A real-world scenario might need to handle sales from multiple batches.
                $batch_query = "SELECT id, current_qty FROM stock_batches WHERE product_id = :product_id AND current_qty >= :quantity ORDER BY expiry_date ASC LIMIT 1";
                $batch_stmt = $this->conn->prepare($batch_query);
                $batch_stmt->bindParam(':product_id', $product['product_id']);
                $batch_stmt->bindParam(':quantity', $product['quantity']);
                $batch_stmt->execute();

                if ($batch_stmt->rowCount() > 0) {
                    $batch = $batch_stmt->fetch(PDO::FETCH_ASSOC);
                    $batch_id = $batch['id'];

                    // Decrement stock_batches
                    $update_batch_query = "UPDATE stock_batches SET current_qty = current_qty - :quantity WHERE id = :id";
                    $update_batch_stmt = $this->conn->prepare($update_batch_query);
                    $update_batch_stmt->bindParam(':quantity', $product['quantity']);
                    $update_batch_stmt->bindParam(':id', $batch_id);
                    if(!$update_batch_stmt->execute()){
                        throw new Exception("Failed to update stock batch.");
                    }

                    // Insert into stock_ledger
                    $ledger_query = "INSERT INTO stock_ledger SET product_id=:product_id, batch_id=:batch_id, transaction_type='OUT', quantity=:quantity, reference_id=:reference_id";
                    $ledger_stmt = $this->conn->prepare($ledger_query);

                    $ledger_stmt->bindParam(":product_id", $product['product_id']);
                    $ledger_stmt->bindParam(":batch_id", $batch_id);
                    $ledger_stmt->bindParam(":quantity", $product['quantity']);
                    $ledger_stmt->bindParam(":reference_id", $this->id);

                    if(!$ledger_stmt->execute()){
                        throw new Exception("Failed to create stock ledger entry for sale.");
                    }
                } else {
                    throw new Exception("Insufficient stock for product ID: " . $product['product_id']);
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function read() {
        $query = "SELECT s.*, c.ownnam FROM " . $this->table_name . " s LEFT JOIN customers c ON s.customer_id = c.RegID ORDER BY s.invoice_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOneWithDetails() {
        $query = "SELECT s.*, c.ownnam, u.name as user_name FROM " . $this->table_name . " s LEFT JOIN customers c ON s.customer_id = c.RegID LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->invoice_number = $row['invoice_number'];
        $this->invoice_date = $row['invoice_date'];
        $this->ownnam = $row['ownnam'];
        $this->net_amount = $row['net_amount'];
        $this->payment_status = $row['payment_status'];
        $this->user_name = $row['user_name'];

        // Get the products for this sale
        $products_query = "SELECT p.product_name, sl.quantity, (sl.quantity * sb.purchase_price) as total_price FROM stock_ledger sl JOIN products p ON sl.product_id = p.id JOIN stock_batches sb ON sl.batch_id = sb.id WHERE sl.reference_id = ? AND sl.transaction_type = 'OUT'";
        $products_stmt = $this->conn->prepare($products_query);
        $products_stmt->bindParam(1, $this->id);
        $products_stmt->execute();
        $this->products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
