const products = window.cashierProducts || [];
const searchInput = document.getElementById('searchProduct');
const resultsBox = document.getElementById('productResults');
const cartBody = document.getElementById('cartBody');
let cart = [];
let paymentMethod = 'Tunai';

const rupiah = (value) => new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
}).format(value || 0);

const valueOf = (id) => Number.parseFloat(document.getElementById(id).value) || 0;

function getTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) * item.qty), 0);
    const percentDiscount = subtotal * Math.min(100, Math.max(0, valueOf('discountPercent'))) / 100;
    const discount = percentDiscount + Math.max(0, valueOf('discountAmount'));
    return {
        subtotal,
        total: Math.max(0, subtotal - discount + Math.max(0, valueOf('tax')) + Math.max(0, valueOf('otherFee'))),
    };
}

function renderResults() {
    const keyword = searchInput.value.trim().toLowerCase();
    if (!keyword) {
        resultsBox.classList.add('hidden');
        return;
    }

    const matches = products.filter((product) => [product.name, product.category || '', product.id].some((field) => String(field).toLowerCase().includes(keyword)));
    resultsBox.innerHTML = matches.length ? matches.map((product) => `
        <button type="button" class="product-result" data-product-id="${product.id}">
            <span><strong>${product.name}</strong><small>${product.category || 'Produk'} · Stok ${product.stock}</small></span>
            <strong>${rupiah(product.price)}</strong>
        </button>
    `).join('') : '<p class="px-3 py-4 text-center text-sm text-slate-500">Barang tidak ditemukan.</p>';
    resultsBox.classList.remove('hidden');
}

function addToCart(productId) {
    const product = products.find((item) => item.id === Number(productId));
    if (!product) return;
    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
        if (existing.qty < product.stock) existing.qty += 1;
    } else {
        cart.push({ ...product, qty: 1 });
    }
    searchInput.value = '';
    resultsBox.classList.add('hidden');
    renderCart();
}

function renderCart() {
    if (!cart.length) {
        cartBody.innerHTML = '<tr><td colspan="6" class="empty-cart">Keranjang masih kosong.<br><span>Silakan cari atau scan barang.</span></td></tr>';
    } else {
        cartBody.innerHTML = cart.map((item, index) => `
            <tr>
                <td class="px-5 py-4 text-slate-400">${index + 1}</td>
                <td class="px-3 py-4"><strong class="text-slate-800">${item.name}</strong><small class="mt-1 block text-xs text-slate-400">${item.category || 'Produk'}</small></td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-slate-600">${rupiah(item.price)}</td>
                <td class="px-3 py-4"><div class="qty-control"><button type="button" data-action="decrease" data-product-id="${item.id}">-</button><span>${item.qty}</span><button type="button" data-action="increase" data-product-id="${item.id}">+</button></div></td>
                <td class="whitespace-nowrap px-3 py-4 text-right font-bold text-slate-800">${rupiah(item.price * item.qty)}</td>
                <td class="px-5 py-4 text-right"><button type="button" class="remove-item" data-product-id="${item.id}" title="Hapus barang">&times;</button></td>
            </tr>
        `).join('');
    }
    const quantity = cart.reduce((sum, item) => sum + item.qty, 0);
    const totals = getTotal();
    document.getElementById('itemCount').textContent = `${quantity} Item`;
    document.getElementById('totalQty').textContent = quantity;
    document.getElementById('subtotal').textContent = rupiah(totals.subtotal);
    document.getElementById('grandTotal').textContent = rupiah(totals.total);
    updateChange();
}

function updateChange() {
    const difference = valueOf('payment') - getTotal().total;
    const changeBox = document.getElementById('changeBox');
    const label = document.getElementById('changeLabel');
    changeBox.classList.toggle('short-payment', difference < 0);
    label.textContent = difference < 0 ? 'Uang Kurang' : 'Kembalian';
    document.getElementById('change').textContent = rupiah(Math.abs(difference));
}

function resetTransaction() {
    cart = [];
    ['payment', 'discountPercent', 'discountAmount', 'tax', 'otherFee'].forEach((id) => { document.getElementById(id).value = id === 'payment' ? '' : '0'; });
    renderCart();
    searchInput.focus();
}

searchInput.addEventListener('input', renderResults);
searchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        const firstResult = resultsBox.querySelector('[data-product-id]');
        if (firstResult) addToCart(firstResult.dataset.productId);
    }
});
document.getElementById('searchButton').addEventListener('click', () => {
    const firstResult = resultsBox.querySelector('[data-product-id]');
    if (firstResult) addToCart(firstResult.dataset.productId);
    else renderResults();
});
resultsBox.addEventListener('click', (event) => {
    const result = event.target.closest('[data-product-id]');
    if (result) addToCart(result.dataset.productId);
});
cartBody.addEventListener('click', (event) => {
    const button = event.target.closest('[data-product-id]');
    if (!button) return;
    const item = cart.find((entry) => entry.id === Number(button.dataset.productId));
    if (!item) return;
    if (button.dataset.action === 'increase' && item.qty < item.stock) item.qty += 1;
    if (button.dataset.action === 'decrease') item.qty -= 1;
    if (button.classList.contains('remove-item') || item.qty < 1) cart = cart.filter((entry) => entry.id !== item.id);
    renderCart();
});
['discountPercent', 'discountAmount', 'tax', 'otherFee'].forEach((id) => document.getElementById(id).addEventListener('input', renderCart));
document.getElementById('payment').addEventListener('input', updateChange);
document.querySelectorAll('.payment-method').forEach((button) => button.addEventListener('click', () => {
    document.querySelectorAll('.payment-method').forEach((item) => item.classList.remove('active'));
    button.classList.add('active');
    paymentMethod = button.dataset.method;
}));
document.getElementById('cancelButton').addEventListener('click', () => {
    if (cart.length && window.confirm('Batalkan transaksi ini?')) resetTransaction();
});
document.getElementById('holdButton').addEventListener('click', () => {
    window.alert(cart.length ? 'Transaksi berhasil ditahan untuk sementara.' : 'Tidak ada transaksi untuk ditahan.');
});
document.getElementById('payButton').addEventListener('click', () => {
    const total = getTotal().total;
    if (!cart.length) return window.alert('Keranjang masih kosong.');
    if (valueOf('payment') < total) return window.alert('Uang pembayaran masih kurang.');
    window.alert(`Pembayaran ${paymentMethod} berhasil.\nTotal: ${rupiah(total)}\nKembalian: ${rupiah(valueOf('payment') - total)}`);
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'F2') { event.preventDefault(); searchInput.focus(); }
    if (event.key === 'F4') { event.preventDefault(); document.getElementById('payment').focus(); }
    if (event.key === 'Escape' && cart.length && window.confirm('Batalkan transaksi ini?')) resetTransaction();
});
document.addEventListener('click', (event) => {
    if (!event.target.closest('#searchProduct, #productResults, #searchButton')) resultsBox.classList.add('hidden');
});

renderCart();
