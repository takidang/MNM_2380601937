// ===== API HELPER =====
const API_BASE = '/api';

// --- JWT ---
const Auth = {
    getToken:  () => localStorage.getItem('jwt_token'),
    setToken:  (t) => localStorage.setItem('jwt_token', t),
    getUser:   () => { const u = localStorage.getItem('jwt_user'); return u ? JSON.parse(u) : null; },
    setUser:   (u) => localStorage.setItem('jwt_user', JSON.stringify(u)),
    logout:    () => { localStorage.removeItem('jwt_token'); localStorage.removeItem('jwt_user'); },
    isLoggedIn:() => !!localStorage.getItem('jwt_token'),
    isAdmin:   () => { const u = Auth.getUser(); return u && u.role === 'admin'; },
    requireLogin: (redirect = '/frontend/login.html') => {
        if (!Auth.isLoggedIn()) { window.location.href = redirect; return false; }
        return true;
    },
    requireAdmin: () => {
        if (!Auth.isLoggedIn()) { window.location.href = '/frontend/login.html'; return false; }
        if (!Auth.isAdmin())    { window.location.href = '/frontend/index.html'; return false; }
        return true;
    },
};

// --- Fetch wrapper ---
async function apiRequest(method, endpoint, body = null) {
    const headers = { 'Content-Type': 'application/json' };
    const token = Auth.getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    const opts = { method, headers };
    if (body !== null) opts.body = JSON.stringify(body);

    const res = await fetch(API_BASE + endpoint, opts);
    const data = await res.json().catch(() => ({}));

    if (res.status === 401) {
        Auth.logout();
        window.location.href = '/frontend/login.html';
        return null;
    }
    return { ok: res.ok, status: res.status, data };
}

const GET    = (url)         => apiRequest('GET',    url);
const POST   = (url, body)   => apiRequest('POST',   url, body);
const PUT    = (url, body)   => apiRequest('PUT',    url, body);
const DELETE = (url)         => apiRequest('DELETE', url);

// --- Notification ---
function showToast(msg, type = 'success') {
    const d = document.createElement('div');
    d.className = `fixed top-4 right-4 z-50 px-5 py-3 rounded shadow-lg text-white text-sm transition-all
                   ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'}`;
    d.textContent = msg;
    document.body.appendChild(d);
    setTimeout(() => d.remove(), 3000);
}

function showError(el, msg) {
    if (el) { el.textContent = msg; el.classList.remove('hidden'); }
}

// --- Render nav ---
function renderNav(containerId = 'nav') {
    const el = document.getElementById(containerId);
    if (!el) return;
    const user = Auth.getUser();
    const isAdmin = user && user.role === 'admin';

    el.innerHTML = `
    <nav class="bg-gray-900 text-white px-6 py-3 flex items-center justify-between">
      <a href="/frontend/index.html" class="text-xl font-bold text-blue-400">TECH-SPECTRUM</a>
      <div class="flex items-center gap-5 text-sm">
        <a href="/frontend/index.html" class="hover:text-blue-400">Sản phẩm</a>
        <a href="/frontend/cart.html" class="hover:text-blue-400">🛒 Giỏ hàng</a>
        ${user ? `
          <a href="/frontend/orders.html" class="hover:text-blue-400">Đơn hàng</a>
          ${isAdmin ? `<a href="/frontend/admin/index.html" class="text-yellow-400 hover:text-yellow-300">Admin</a>` : ''}
          <a href="/frontend/profile.html" class="flex items-center gap-1.5 hover:text-blue-400">
            ${user.avatar
              ? `<img src="/public/images/avatars/${user.avatar}" class="w-7 h-7 rounded-full object-cover border border-gray-500">`
              : `<span class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold">${(user.fullname||user.username||'U')[0].toUpperCase()}</span>`}
            <span class="text-gray-300 text-sm">${user.fullname || user.username}</span>
          </a>
          <button onclick="doLogout()" class="bg-red-600 px-3 py-1 rounded hover:bg-red-700">Đăng xuất</button>
        ` : `
          <a href="/frontend/login.html" class="bg-blue-600 px-3 py-1 rounded hover:bg-blue-700">Đăng nhập</a>
          <a href="/frontend/register.html" class="bg-gray-700 px-3 py-1 rounded hover:bg-gray-600">Đăng ký</a>
        `}
      </div>
    </nav>`;
}

async function doLogout() {
    await POST('/auth/logout', {}).catch(() => {});
    Auth.logout();
    window.location.href = '/frontend/login.html';
}

// --- Format tiền ---
function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function imgUrl(filename) {
    return filename ? `/public/images/products/${filename}` : '/public/images/no-image.png';
}
