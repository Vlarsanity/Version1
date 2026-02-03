/* ============================
   CONFIG
   ============================ */
const BASE_PATH = '/html/public'; // SPA base folder

let currentUserRole = 'admin'; // default for now

const ROLE_FOLDER = {
    admin: 'admin',
    staff: 'staff',
    user: 'user'
};

const PAGE_ACCESS = {
    dashboard: ['admin', 'staff', 'user'],
    products:  ['admin'],
    orders:    ['admin', 'staff']
};

const NOT_FOUND_PAGE    = '/html/pages/404.php';
const UNAUTHORIZED_PAGE = '/html/pages/403.php';

/* ============================
   RENDER FUNCTION
   ============================ */
function render(html) {
    const wrapper = document.querySelector('#app .content-wrapper');
    if (!wrapper) return;
    wrapper.innerHTML = html;
}

/* ============================
   HELPER: GO TO DASHBOARD (ROLE ADAPTIVE)
   ============================ */
function goToDashboard() {
    navigate('dashboard');
}
window.goToDashboard = goToDashboard;

/* ============================
   PAGE LOADER
   ============================ */
async function loadPage(page) {
    try {
        console.log('Loading page:', page);
        console.log('Current role:', currentUserRole);

        // 1. Check route exists
        if (!PAGE_ACCESS[page]) throw new Error('NotFound');

        // 2. Authorization check
        if (!PAGE_ACCESS[page].includes(currentUserRole)) throw new Error('Unauthorized');

        // 3. Resolve role folder
        const roleFolder = ROLE_FOLDER[currentUserRole];
        console.log('Resolved folder:', roleFolder);
        if (!roleFolder) throw new Error('InvalidRole');

        // 4. Fetch page from absolute path
        const path = `/html/pages/${roleFolder}/${page}.php`;
        console.log('Fetching path:', path);

        const res = await fetch(path, { headers: { 'X-Requested-With': 'SPA' } });

        if (!res.ok) {
            console.error('Fetch failed:', res.status, res.statusText);
            throw new Error('NotFound');
        }

        const html = await res.text();
        render(html);
        console.log('Page loaded successfully:', page);

    } catch (err) {
        console.error('loadPage error:', err.message);

        if (err.message === 'Unauthorized') {
            const res403 = await fetch(UNAUTHORIZED_PAGE);
            render(await res403.text());
        } else {
            const res404 = await fetch(NOT_FOUND_PAGE);
            render(await res404.text());

            const btn = document.querySelector('.not-found-btn');
            if (btn) btn.onclick = goToDashboard;
        }
    }
}

/* ============================
   ROUTES
   ============================ */
const routes = {
    dashboard: () => loadPage('dashboard'),
    products:  () => loadPage('products'),
    orders:    () => loadPage('orders')
};

/* ============================
   NAVIGATION
   ============================ */
function navigate(route) {
    if (routes[route]) {
        routes[route]();
        // Normalize URL: always start from BASE_PATH
        const newUrl = `${BASE_PATH}/${route}`.replace(/\/+/g, '/');
        history.pushState({}, '', newUrl);
    } else {
        loadPage('404');
    }
}
window.navigate = navigate;

/* ============================
   ROUTE RESOLVING (on reload)
   ============================ */
function resolveRoute() {
    let path = location.pathname;

    // Remove SPA base path
    if (path.startsWith(BASE_PATH)) {
        path = path.slice(BASE_PATH.length);
    }

    // Remove leading/trailing slashes and fallback to 'dashboard'
    const route = path.replace(/^\/+|\/+$/g, '') || 'dashboard';
    console.log('Resolved route:', route);
    return route;
}

/* ============================
   INIT
   ============================ */
window.addEventListener('DOMContentLoaded', () => {
    navigate(resolveRoute());
});

window.addEventListener('popstate', () => {
    navigate(resolveRoute());
});
