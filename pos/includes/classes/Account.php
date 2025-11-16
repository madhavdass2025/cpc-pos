<?php
class Account {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create_voucher($voucher_type, $amount, $description, $voucher_date) {
        $query = "INSERT INTO vouchers SET voucher_type=:voucher_type, amount=:amount, description=:description, voucher_date=:voucher_date";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $voucher_type = htmlspecialchars(strip_tags($voucher_type));
        $amount = htmlspecialchars(strip_tags($amount));
        $description = htmlspecialchars(strip_tags($description));
        $voucher_date = htmlspecialchars(strip_tags($voucher_date));

        $stmt->bindParam(":voucher_type", $voucher_type);
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":voucher_date", $voucher_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function get_collection_report($start_date, $end_date, $payment_method = null) {
        $query = "SELECT p.payment_date, p.invoice_id, p.payment_method, p.amount_paid, si.invoice_number FROM payments p JOIN sales_invoices si ON p.invoice_id = si.id WHERE p.payment_date BETWEEN :start_date AND :end_date";
        if ($payment_method) {
            $query .= " AND p.payment_method = :payment_method";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        if ($payment_method) {
            $stmt->bindParam(':payment_method', $payment_method);
        }
        $stmt->execute();
        return $stmt;
    }

    public function get_sales_register($start_date, $end_date) {
        $query = "SELECT si.*, c.ownnam FROM sales_invoices si LEFT JOIN customers c ON si.customer_id = c.RegID WHERE si.invoice_date BETWEEN :start_date AND :end_date ORDER BY si.invoice_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt;
    }

    public function get_purchase_register($start_date, $end_date) {
        $query = "SELECT pb.*, s.supplier_name FROM purchase_bills pb LEFT JOIN suppliers s ON pb.supplier_id = s.id WHERE pb.bill_date BETWEEN :start_date AND :end_date ORDER BY pb.bill_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        return $stmt;
    }

    public function get_supplier_ledger($supplier_id) {
        // This is a simplified ledger. A real-world ledger would be more complex.
        $query = "SELECT 'Purchase' as transaction_type, bill_date as date, total_amount as amount FROM purchase_bills WHERE supplier_id = :supplier_id
                  UNION ALL
                  SELECT 'Payment' as transaction_type, payment_date as date, -amount as amount FROM supplier_payments WHERE supplier_id = :supplier_id
                  ORDER BY date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->execute();
        return $stmt;
    }

    public function get_customer_credit_report() {
        $query = "SELECT c.ownnam, SUM(cl.debit_amount) - SUM(cl.credit_amount) as outstanding_balance FROM customer_ledger cl JOIN customers c ON cl.customer_id = c.RegID GROUP BY c.RegID HAVING outstanding_balance > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create_supplier_payment($supplier_id, $payment_date, $amount, $payment_method, $notes) {
        $query = "INSERT INTO supplier_payments SET supplier_id=:supplier_id, payment_date=:payment_date, amount=:amount, payment_method=:payment_method, notes=:notes";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $supplier_id = htmlspecialchars(strip_tags($supplier_id));
        $payment_date = htmlspecialchars(strip_tags($payment_date));
        $amount = htmlspecialchars(strip_tags($amount));
        $payment_method = htmlspecialchars(strip_tags($payment_method));
        $notes = htmlspecialchars(strip_tags($notes));

        $stmt->bindParam(":supplier_id", $supplier_id);
        $stmt->bindParam(":payment_date", $payment_date);
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":payment_method", $payment_method);
        $stmt->bindParam(":notes", $notes);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
