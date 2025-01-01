// Wait for the DOM to fully load
document.addEventListener("DOMContentLoaded", () => {
    displayOrderSummary();
    setupNavigationButtons();
});

// Display order summary (mocked data for now; adjust if orders are saved in a database)
function displayOrderSummary() {
    const orderSummaryContainer = document.getElementById("orderSummary");
    const lastOrder = JSON.parse(localStorage.getItem("lastOrder"));

    if (lastOrder && lastOrder.length > 0) {
        let total = 0;

        // Generate the order summary dynamically
        const orderHTML = lastOrder
            .map((item) => {
                const subtotal = item.price * item.quantity;
                total += subtotal;

                return `
                <div class="order-item">
                    <h4>${item.name}</h4>
                    <p>Price: RM${item.price.toFixed(2)} x ${item.quantity}</p>
                    <p>Subtotal: RM${subtotal.toFixed(2)}</p>
                </div>`;
            })
            .join("");

        // Set the order summary HTML
        orderSummaryContainer.innerHTML = `
            <h2>Your Order Summary</h2>
            ${orderHTML}
            <p class="total">Total Paid: RM${total.toFixed(2)}</p>
        `;
    } else {
        // Fallback message if no order is stored
        orderSummaryContainer.innerHTML = "<p>Order details not found. Please try again.</p>";
    }
}

// Setup navigation button functionality
function setupNavigationButtons() {
    const goToHomeButton = document.getElementById("goToHome");
    const viewOrdersButton = document.getElementById("viewOrders");

    // Navigate to the homepage
    goToHomeButton.addEventListener("click", () => {
        location.href = "dashboard.php"; // Replace with your homepage URL
    });

    // Navigate to the orders page
    viewOrdersButton.addEventListener("click", () => {
        location.href = "view_orders.html"; // Replace with your orders page URL
    });
}
