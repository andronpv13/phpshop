document.addEventListener('DOMContentLoaded', () => {
  window.cart = JSON.parse(localStorage.getItem('shop_cart') || '[]');
  updateCartCount();
  document.addEventListener('click', (e) => {
    if (e.target.matches('[data-add-to-cart]')) {
      const id = e.target.dataset.id;
      const name = e.target.dataset.name || 'Товар #' + id;
      const price = parseFloat(e.target.dataset.price || 999);
      addToCart({ id, name, price, qty: 1 });
      showToast(`"${name}" добавлен в корзину`);
    }
    if (e.target.matches('[data-remove-from-cart]')) {
      const id = e.target.closest('tr').dataset.id;
      removeFromCart(id);
    }
  });
  const page = window.location.pathname.split('/').pop();
  if (page === 'cart.html') renderCartPage();
  if (page === 'checkout.html') bindCheckoutForm();
  if (page === 'admin.html') bindAdminTabs();
});
function addToCart(item) {
  const exists = window.cart.find(i => i.id === item.id);
  if (exists) exists.qty += item.qty;
  else window.cart.push(item);
  saveCart();
}
function removeFromCart(id) {
  window.cart = window.cart.filter(i => i.id !== id);
  saveCart();
  renderCartPage();
  showToast('Товар удалён');
}
function updateQty(id, delta) {
  const item = window.cart.find(i => i.id === id);
  if (item) { item.qty = Math.max(1, item.qty + delta); saveCart(); renderCartPage(); }
}
function saveCart() { localStorage.setItem('shop_cart', JSON.stringify(window.cart)); updateCartCount(); }
function updateCartCount() {
  const count = window.cart.reduce((sum, i) => sum + i.qty, 0);
  const el = document.getElementById('cart-count');
  if (el) el.textContent = count || '';
}
function renderCartPage() {
  const tbody = document.getElementById('cart-items');
  if (!tbody) return;
  tbody.innerHTML = '';
  let total = 0;
  window.cart.forEach(item => {
    const subtotal = item.price * item.qty;
    total += subtotal;
    tbody.innerHTML += `<tr data-id="${item.id}"><td>${item.name}</td><td><button onclick="updateQty('${item.id}',-1)">-</button> ${item.qty} <button onclick="updateQty('${item.id}',1)">+</button></td><td>${item.price} ₽</td><td>${subtotal} ₽</td><td><button class="btn-secondary" data-remove-from-cart>Удалить</button></td></tr>`;
  });
  document.querySelectorAll('#cart-total').forEach(el => el.textContent = total + ' ₽');
}
function bindCheckoutForm() {
  const form = document.getElementById('checkout-form');
  if (!form) return;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (window.cart.length === 0) { showToast('Корзина пуста'); return; }
    showToast('✅ Заказ успешно оформлен!');
    window.cart = []; saveCart();
    setTimeout(() => window.location.href = 'index.html', 1500);
  });
}
function bindAdminTabs() {
  document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
      showToast(`Раздел "${link.textContent}" загружен`);
    });
  });
}
function showToast(msg) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  Object.assign(toast.style, { position:'fixed', bottom:'20px', right:'20px', background:'#111', color:'#fff', padding:'12px 16px', borderRadius:'8px', zIndex:9999, opacity:'0', transition:'opacity 0.3s' });
  document.body.appendChild(toast);
  requestAnimationFrame(() => toast.style.opacity = '1');
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
