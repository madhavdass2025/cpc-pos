<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Customer.php';

$customer = new Customer($conn);
$customer_stmt = $customer->read();

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Point of Sale (POS)</h2>
    <form id="pos-form" method="post" action="process_sale.php">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control">
                        <option value="">Select Customer</option>
                        <?php while ($row = $customer_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['RegID']; ?>"><?php echo $row['ownnam']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-search">Search Product</label>
                    <input type="text" id="product-search" class="form-control" placeholder="Start typing to search...">
                    <div id="product-suggestions"></div>
                </div>
            </div>
        </div>

        <h3>Order Items</h3>
        <table class="table" id="order-items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- Order items will be added here dynamically -->
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="UPI">UPI</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="totals">
                    <h4>Grand Total: <span id="grand-total">0.00</span></h4>
                </div>
            </div>
        </div>
        <input type="hidden" name="net_amount" id="net_amount_hidden">
        <button type="submit" class="btn btn-primary">Process Sale</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('product-search');
    const productSuggestions = document.getElementById('product-suggestions');
    const orderItemsTableBody = document.querySelector('#order-items tbody');
    const grandTotalSpan = document.getElementById('grand-total');
    let orderItems = [];

    productSearch.addEventListener('keyup', function() {
        const searchTerm = this.value;
        if (searchTerm.length < 2) {
            productSuggestions.innerHTML = '';
            return;
        }

        fetch(`ajax_product_search.php?term=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                let suggestionsHTML = '<ul class="list-group">';
                data.forEach(product => {
                    suggestionsHTML += `<li class="list-group-item list-group-item-action" data-product-id="${product.id}" data-product-name="${product.product_name}" data-product-price="${product.mrp}" data-product-stock="${product.stock}">${product.product_name} (Stock: ${product.stock})</li>`;
                });
                suggestionsHTML += '</ul>';
                productSuggestions.innerHTML = suggestionsHTML;
            });
    });

    productSuggestions.addEventListener('click', function(e) {
        if (e.target.tagName === 'LI') {
            const product = {
                id: e.target.dataset.productId,
                name: e.target.dataset.productName,
                price: parseFloat(e.target.dataset.productPrice),
                stock: parseInt(e.target.dataset.productStock),
                quantity: 1
            };

            // Check if product is already in the order
            const existingProduct = orderItems.find(item => item.id === product.id);
            if (!existingProduct) {
                orderItems.push(product);
                renderOrderItems();
            }

            productSearch.value = '';
            productSuggestions.innerHTML = '';
        }
    });

    orderItemsTableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-quantity')) {
            const productId = e.target.dataset.productId;
            const newQuantity = parseInt(e.target.value);
            const product = orderItems.find(item => item.id === productId);
            if (product && newQuantity > 0 && newQuantity <= product.stock) {
                product.quantity = newQuantity;
            } else {
                e.target.value = product.quantity; // Reset to previous quantity if invalid
            }
            renderOrderItems();
        }
    });

    orderItemsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const productId = e.target.dataset.productId;
            orderItems = orderItems.filter(item => item.id !== productId);
            renderOrderItems();
        }
    });

    function renderOrderItems() {
        let tableHTML = '';
        let grandTotal = 0;
        orderItems.forEach((item, index) => {
            const total = item.quantity * item.price;
            grandTotal += total;
            tableHTML += `
                <tr>
                    <td>${item.name}<input type="hidden" name="products[${index}][product_id]" value="${item.id}"></td>
                    <td>${item.stock}</td>
                    <td><input type="number" class="form-control item-quantity" data-product-id="${item.id}" name="products[${index}][quantity]" value="${item.quantity}" min="1" max="${item.stock}"></td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${total.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item" data-product-id="${item.id}">X</button></td>
                </tr>
            `;
        });
        orderItemsTableBody.innerHTML = tableHTML;
        grandTotalSpan.textContent = grandTotal.toFixed(2);
        document.getElementById('net_amount_hidden').value = grandTotal.toFixed(2);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
