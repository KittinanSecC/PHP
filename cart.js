document.addEventListener("DOMContentLoaded", function () {
    // ฟังก์ชันอัปเดตจำนวนสินค้า
    const updateCart = (cartId, currentQuantity, cartItem, subtotalElement, totalElement) => {
        fetch("update_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `cart_id=${cartId}&quantity=${currentQuantity}`,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // อัปเดตราคาใหม่
                    const newTotalPrice = (currentQuantity * parseFloat(data.new_price_per_item)).toFixed(2);
                    cartItem.querySelector(".quantity-value").textContent = currentQuantity;
                    cartItem.querySelector(".product-details p:nth-of-type(2)").textContent = `฿${parseFloat(newTotalPrice).toLocaleString("th-TH", { minimumFractionDigits: 2 })}`;

                    // อัปเดตยอดรวม
                    subtotalElement.textContent = parseFloat(data.new_subtotal).toLocaleString("th-TH", { minimumFractionDigits: 2 });
                    totalElement.textContent = (parseFloat(data.new_subtotal) + 150).toLocaleString("th-TH", { minimumFractionDigits: 2 });
                } else {
                    alert(data.message || "❌ อัปเดตสินค้าไม่สำเร็จ!");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
            });
    };

    // จัดการปุ่มเพิ่ม/ลดสินค้า
    document.querySelectorAll(".qty-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const cartItem = this.closest(".cart-item");
            const cartId = this.getAttribute("data-cart-id");
            const quantitySpan = cartItem.querySelector(".quantity-value");
            let currentQuantity = parseInt(quantitySpan.textContent);
            const subtotalElement = document.querySelector(".subtotal");
            const totalElement = document.querySelector(".total");

            if (this.textContent === "+") {
                currentQuantity++;
            } else if (currentQuantity > 1) {
                currentQuantity--;
            } else {
                return;
            }

            updateCart(cartId, currentQuantity, cartItem, subtotalElement, totalElement);
        });
    });

    // จัดการปุ่มลบสินค้า
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            const cartId = this.dataset.cartId;
            const cartItem = this.closest(".cart-item");

            if (!confirm("คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?")) {
                return;
            }

            fetch("delete_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `cart_id=${cartId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItem.remove(); // ลบสินค้าออกจาก DOM
                        document.querySelector(".subtotal").textContent = parseFloat(data.new_total).toLocaleString("th-TH", { minimumFractionDigits: 2 });
                        document.querySelector(".total").textContent = (parseFloat(data.new_total) + 150).toLocaleString("th-TH", { minimumFractionDigits: 2 });

                        // ถ้าตะกร้าว่าง
                        if (document.querySelectorAll(".cart-item").length === 0) {
                            document.querySelector(".cart-container").innerHTML = `
                                <h2>ตะกร้าสินค้า</h2>
                                <p class="empty-cart">ตะกร้าของคุณว่างเปล่า 🛒</p>
                            `;
                        }
                    } else {
                        alert(data.message || "❌ ลบสินค้าไม่สำเร็จ!");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
                });
        });
    });
});
