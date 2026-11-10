/* =========================================
   COFNA JAVASCRIPT
========================================= */

/* =========================================
   DATA KERANJANG
========================================= */

let cart = JSON.parse(
    localStorage.getItem("cofnaCart")
) || [];


/* =========================================
   SIMPAN KERANJANG
========================================= */

function saveCart() {

    localStorage.setItem(
        "cofnaCart",
        JSON.stringify(cart)
    );

}


/* =========================================
   JUMLAH KERANJANG DI NAVBAR
========================================= */

function updateCartCount() {

    const cartCount =
        document.getElementById("cart-count");

    if (!cartCount) return;


    const total = cart.reduce(
        (sum, item) => sum + item.jumlah,
        0
    );


    cartCount.textContent = total;

}


/* =========================================
   FILTER MENU
========================================= */

function filterMenu(category, button) {

    const menuCards =
        document.querySelectorAll(".menu-card");

    const categoryButtons =
        document.querySelectorAll(".category-btn");


    categoryButtons.forEach(btn => {
        btn.classList.remove("active");
    });


    button.classList.add("active");


    menuCards.forEach(card => {

        if (
            category === "all" ||
            card.classList.contains(category)
        ) {

            card.style.display = "flex";

        } else {

            card.style.display = "none";

        }

    });

}


/* =========================================
   FAVORITE MENU
========================================= */

function toggleFavorite(button) {

    const card =
        button.closest(".menu-card");


    button.classList.toggle("active");


    if (button.classList.contains("active")) {

        button.innerHTML = "♥";

        card.classList.add("favorite");

    } else {

        button.innerHTML = "♡";

        card.classList.remove("favorite");

    }

}


/* =========================================
   FAVORITE DI HOME
========================================= */

function toggleHomeFavorite(button) {

    button.classList.toggle("active");


    if (button.classList.contains("active")) {

        button.innerHTML = "♥";

    } else {

        button.innerHTML = "♡";

    }

}


/* =========================================
   TAMBAH KE KERANJANG
========================================= */

function addToCart(id_menu, nama, harga, gambar = "") {

    if (typeof id_menu === "string" && typeof nama === "number") {

        gambar = getMenuImage(id_menu);
        harga = nama;
        nama = id_menu;
        id_menu = 0;
    }

    const existingProduct =
        cart.find(item => item.nama === nama);

    if (existingProduct) {

        existingProduct.jumlah++;

        if (gambar) {
            existingProduct.gambar = gambar;
        }

    } else {

        cart.push({
            id_menu: Number(id_menu),
            nama: nama,
            harga: Number(harga),
            jumlah: 1,
            gambar: gambar
        });

    }

    saveCart();
    updateCartCount();

    alert(
        nama +
        " berhasil ditambahkan ke keranjang! ☕"
    );
}

/* =========================================
   TAMPILKAN KERANJANG
========================================= */

    function displayCart() {

    const cartItems =
        document.getElementById("cart-items");

    if (!cartItems) {
        return;
    }

    const cart = JSON.parse(
        localStorage.getItem("cofnaCart")
    ) || [];


    cartItems.innerHTML = "";


    const totalItem =
        document.getElementById("cart-total-item");

    const subtotalElement =
        document.getElementById("subtotal");

    const serviceFeeElement =
        document.getElementById("service-fee");

    const grandTotalElement =
        document.getElementById("cart-grand-total");


    /* KERANJANG KOSONG */

    if (cart.length === 0) {

        cartItems.innerHTML = `

            <div class="empty-cart">

                <div class="empty-cart-icon">
                    🛒
                </div>

                <h3>
                    Keranjangmu masih kosong
                </h3>

                <p>
                    Yuk pilih minuman favoritmu dulu!
                </p>

                <a
                    href="../menu.php"
                    class="btn-primary"
                >
                    Lihat Menu →
                </a>

            </div>

        `;


        if (totalItem)
            totalItem.textContent = "0 item";

        if (subtotalElement)
            subtotalElement.textContent = "Rp0";

        if (serviceFeeElement)
            serviceFeeElement.textContent = "Rp0";

        if (grandTotalElement)
            grandTotalElement.textContent = "Rp0";

        return;

    }


    /* TAMPILKAN PRODUK */

    cartItems.innerHTML = "";


    let subtotal = 0;

    let jumlahItem = 0;


    cart.forEach((item, index) => {

        const totalHarga =
            item.harga * item.jumlah;


        subtotal += totalHarga;

        jumlahItem += item.jumlah;


        const cartItem =
            document.createElement("div");


        cartItem.className = "cart-item";


        cartItem.innerHTML = `

            <div class="cart-item-image">

                <img
                src="${item.gambar ? '../image_cofna/' + item.gambar : getMenuImage(item.nama)}"
                alt="${item.nama}"
>
            </div>


            <div class="cart-item-info">

                <h3>
                    ${item.nama}
                </h3>

                <div class="cart-item-price">
                    ${formatRupiah(item.harga)}
                </div>


                <div class="quantity-control">

                    <button
                        onclick="decreaseQuantity(${index})"
                    >
                        −
                    </button>

                    <span>
                        ${item.jumlah}
                    </span>

                    <button
                        onclick="increaseQuantity(${index})"
                    >
                        +
                    </button>

                </div>

            </div>


            <div class="cart-item-total">

                <strong>
                    ${formatRupiah(totalHarga)}
                </strong>

                <button
                    class="remove-item"
                    onclick="removeItem(${index})"
                >
                    Hapus
                </button>

            </div>

        `;


        cartItems.appendChild(cartItem);

    });


    /* BIAYA */

    const serviceFee =
        subtotal > 0 ? 2000 : 0;


    const grandTotal =
        subtotal + serviceFee;


    totalItem.textContent =
        jumlahItem + " item";


    subtotalElement.textContent =
        formatRupiah(subtotal);


    serviceFeeElement.textContent =
        formatRupiah(serviceFee);


    grandTotalElement.textContent =
        formatRupiah(grandTotal);

}

/* =========================================
   GAMBAR MENU
========================================= */

function getMenuImage(nama) {

    const images = {

        "Americano":
            "../image_cofna/menu-americano.jpeg",

        "Cappuccino":
            "../image_cofna/menu-cappuccino.jpeg",

        "Caramel Latte":
            "../image_cofna/menu-caramel latte.jpeg",

        "Matcha Latte":
            "../image_cofna/menu-matcha latte.jpeg",

        "Cookies & Cream":
            "../image_cofna/menu-cookies cream.jpeg",

        "Choco Milk":
            "../image_cofna/menu-choco milk.jpeg",

        "Strawberry Milk":
            "../image_cofna/menu-strawberry milk.jpeg",

        "Manggo Milk":
            "../image_cofna/menu-manggo milk.jpeg",

        "Kopi Susu":
            "../image_cofna/menu-kopi susu.jpeg"

    };


    return images[nama] || "";
}

/* =========================================
   TAMBAH JUMLAH
========================================= */

function increaseQuantity(index) {

    cart[index].jumlah++;

    saveCart();

    displayCart();

    updateCartCount();

}


/* =========================================
   KURANG JUMLAH
========================================= */

function decreaseQuantity(index) {

    if (cart[index].jumlah > 1) {

        cart[index].jumlah--;

    } else {

        cart.splice(index, 1);

    }


    saveCart();

    displayCart();

    updateCartCount();

}


/* =========================================
   HAPUS ITEM
========================================= */

function removeItem(index) {

    cart.splice(index, 1);

    saveCart();

    displayCart();

    updateCartCount();

}


/* =========================================
   FORMAT RUPIAH
========================================= */

function formatRupiah(angka) {

    angka = Number(angka) || 0;

    return "Rp" +
        angka.toLocaleString("id-ID");

}

/* =========================================
   CHECKOUT
========================================= */

function checkout() {
    if (cart.length === 0) {
        alert("Keranjang kamu masih kosong! ☕");
        return;
    }

    window.location.href = "checkout_cofna.php";
}

/* =========================================
   JALANKAN SAAT HALAMAN DIBUKA
========================================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        updateCartCount();

        displayCart();

    }
);

/* =========================================
   CHECKOUT
========================================= */


/* TAMPILKAN PESANAN DI CHECKOUT */

function displayCheckout() {

    const checkoutItems =
        document.getElementById("checkout-items");


    if (!checkoutItems) return;


    const subtotalElement =
        document.getElementById("checkout-subtotal");

    const serviceElement =
        document.getElementById("checkout-service");

    const totalElement =
        document.getElementById("checkout-total");


    /* Kalau kosong */

    if (cart.length === 0) {

        checkoutItems.innerHTML = `

            <div style="
                text-align:center;
                padding:25px 10px;
                color:#806e61;
            ">

                Keranjang kamu masih kosong.

                <br><br>

                <a
                    href="../menu.php"
                    style="
                        color:#9b571e;
                        text-decoration:none;
                        font-weight:600;
                    "
                >
                    Kembali ke Menu →
                </a>

            </div>

        `;

        subtotalElement.textContent = "Rp0";

        serviceElement.textContent = "Rp0";

        totalElement.textContent = "Rp0";

        return;

    }


    checkoutItems.innerHTML = "";


    let subtotal = 0;


    cart.forEach(item => {

        const itemTotal =
            item.harga * item.jumlah;


        subtotal += itemTotal;


        const itemElement =
            document.createElement("div");


        itemElement.className =
            "checkout-item";


        itemElement.innerHTML = `

            <div class="checkout-item-image">

                <img
                src="${getMenuImage(item.nama)}"
                alt="${item.nama}">
            </div>


            <div class="checkout-item-info">

                <h3>
                    ${item.nama}
                </h3>

                <span>
                    ${item.jumlah} ×
                    ${formatRupiah(item.harga)}
                </span>

            </div>


            <div class="checkout-item-price">

                ${formatRupiah(itemTotal)}

            </div>

        `;


        checkoutItems.appendChild(itemElement);

    });


    const serviceFee =
        subtotal > 0 ? 2000 : 0;


    const grandTotal =
        subtotal + serviceFee;


    subtotalElement.textContent =
        formatRupiah(subtotal);


    serviceElement.textContent =
        formatRupiah(serviceFee);


    totalElement.textContent =
        formatRupiah(grandTotal);

}


/* =========================================
   BUAT PESANAN
========================================= */

function placeOrder() {

    const nama =
        document.getElementById("nama").value.trim();


    const telepon =
        document.getElementById("telepon").value.trim();


    const catatan =
        document.getElementById("catatan").value.trim();


    const payment =
        document.querySelector(
            'input[name="payment"]:checked'
        );

    const gambar = getCheckoutImage(nama);


    /* CEK KERANJANG */

    if (cart.length === 0) {

        alert(
            "Keranjang kamu masih kosong! ☕"
        );

        return;

    }

    /* CEK NOMOR HP */

    if (telepon === "") {

        alert(
            "Nomor HP harus diisi yaa 📱"
        );

        document
            .getElementById("telepon")
            .focus();

        return;

    }


    /* CEK PEMBAYARAN */

    if (!payment) {

        alert(
            "Pilih metode pembayaran dulu yaa 💳"
        );

        return;

    }


    /* SIMPAN DATA ORDER */

    const orderData = {

        nama: nama,

        telepon: telepon,

        catatan: catatan,

        payment: payment.value,

        items: cart,

        tanggal: new Date().toLocaleString(
            "id-ID"
        )

    };


    localStorage.setItem(
        "cofnaOrder",
        JSON.stringify(orderData)
    );


    /* HAPUS KERANJANG */

    cart = [];

    saveCart();

    updateCartCount();


    /* PINDAH KE HALAMAN BERHASIL */

    window.location.href =
        "pesanan_berhasil.html";

}

function getCheckoutImage(nama) {

    const images = {

        "Americano":
            "../image_cofna/menu-americano.jpeg",

        "Cappuccino":
            "../image_cofna/menu-cappuccino.jpeg",

        "Caramel Latte":
            "../image_cofna/menu-caramel latte.jpeg",

        "Matcha Latte":
            "../image_cofna/menu-matcha latte.jpeg",

        "Kopi Susu":
            "../image_cofna/menu-kopi susu.jpeg",

        "Cookies & Cream":
            "../image_cofna/menu-cookies cream.jpeg",

        "Strawberry Milk":
            "../image_cofna/menu-strawberry milk.jpeg",

        "Manggo Milk":
            "../image_cofna/menu-manggo milk.jpeg",

        "Choco Milk":
            "../image_cofna/menu-choco milk.jpeg"

    };

    return images[nama] || "";
}

/* =========================================
   LANJUT KE PEMBAYARAN
========================================= */


function goToPayment() {
    const catatan =
        document.getElementById("catatan")?.value.trim() || "";

    const paymentElement =
        document.querySelector('input[name="payment"]:checked');

    if (!paymentElement) {
        alert("Silakan pilih metode pembayaran.");
        return;
    }

    const cart =
        JSON.parse(localStorage.getItem("cofnaCart")) || [];

    if (cart.length === 0) {
        alert("Keranjang kamu masih kosong.");
        return;
    }

    const payment = paymentElement.value;

    let subtotal = 0;

    cart.forEach(function(item) {
        subtotal += Number(item.harga || 0) *
                    Number(item.jumlah || 1);
    });

    const serviceFee = 2000;
    const total = subtotal + serviceFee;

    const checkoutData = {
        catatan: catatan,
        payment: payment,
        subtotal: subtotal,
        serviceFee: serviceFee,
        total: total
    };

    localStorage.setItem(
        "cofnaCheckoutData",
        JSON.stringify(checkoutData)
    );

    localStorage.setItem("cofnaPaymentTotal", total);

    window.location.href = "pembayaran_cofna.php";
}
