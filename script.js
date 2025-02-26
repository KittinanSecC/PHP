document.addEventListener("DOMContentLoaded", function () {
    const productContainer = document.querySelector(".product-list");
    const sortSelect = document.getElementById("sort-options");

    function fetchProducts(filter = "", sort = "") {
        let url = `fetch_products.php?filter=${encodeURIComponent(filter)}&sort=${encodeURIComponent(sort)}`;

        fetch(url)
            .then(response => response.json())
            .then(products => {
                productContainer.innerHTML = ""; // ล้างรายการเก่า
                if (products.length === 0) {
                    productContainer.innerHTML = "<p>ไม่พบสินค้า</p>";
                    return;
                }

                products.forEach(product => {
                    let productCard = `
                        <div class="product-card">
                            <a href="product-detail.php?id=${product.ID}">
                                <img src="myfile/${product.FilesName}" alt="${product.Name}" class="product-image">
                                <h3>${product.Name}</h3>
                                <p>฿${product.Price}</p>
                            </a>
                        </div>
                    `;
                    productContainer.innerHTML += productCard;
                });
            })
            .catch(error => console.error("Error loading products:", error));
    }

    // โหลดสินค้าครั้งแรก
    fetchProducts();

    // อัปเดตเมื่อเปลี่ยนค่าการเรียงลำดับ
    sortSelect.addEventListener("change", function () {
        let sortValue = sortSelect.value;
        fetchProducts("", sortValue);
    });
});
