function init(options) {
	const start = async () => {
		await runInit(options);
		layoutNav();
		nav_status();

		const lang = getCookie('lang') || 'eng';
		setCookie('lang', lang, 365);
		await Promise.resolve(language_apply(lang));
		jw_select();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start, { once: true });
	} else {
		start();
	}
}

// 슬롯 로더: Promise 반환
function loadIntoSlot(slotSelector, url) {
	const slotEl = document.querySelector(slotSelector);
	if (!slotEl || !url) return Promise.resolve(false);
	return fetch(url)
		.then(res => res.text())
		.then(htmlText => {
			slotEl.innerHTML = htmlText.trim();
			return true;
		})
		.catch(err => {
			console.error('include load error:', slotSelector, url, err);
			return false;
		});
}

// 헤더/네비 로드 모두 끝난 뒤에 이어서 동작
function runInit(options) {
	options = options || {};
	const pHeader = loadIntoSlot('.layout-header', options.headerUrl);
	const pNav = loadIntoSlot('.layout-nav', options.navUrl);
	return Promise.all([pHeader, pNav]).then(() => {
		// 헤더 로드 후 사용자 정보 업데이트
		loadUserInfo();
		return true;
	});
}

// 사용자 정보 로드 및 표시
function loadUserInfo() {
	fetch('../backend/api/agent-api.php?action=getUserInfo')
		.then(res => {
			if (!res.ok) {
				throw new Error('Failed to fetch user info: ' + res.status);
			}
			return res.json();
		})
		.then(data => {
			if (data.success && data.data) {
				const user = data.data;
				console.log('User info loaded:', user); // 디버깅용

				// 전역 변수로 저장 (다른 곳에서 사용 가능)
				window.currentUser = user;

				// 헤더의 사용자 이름 업데이트
				updateHeaderUserName(user);
			} else {
				console.error('User info API returned error:', data.message);
			}
		})
		.catch(err => {
			console.error('Failed to load user info:', err);
		});
}

// 헤더의 사용자 이름 업데이트 (재시도 로직 포함)
function updateHeaderUserName(user) {
	const userNameEl = document.querySelector('.header-right .user-name');
	if (userNameEl) {
		userNameEl.textContent = user.displayName || user.username || 'User';
		console.log('Header user name updated to:', userNameEl.textContent); // 디버깅용
	} else {
		// 헤더가 아직 로드되지 않았을 수 있으므로 재시도
		console.warn('Header user-name element not found, retrying...');
		setTimeout(() => {
			const el = document.querySelector('.header-right .user-name');
			if (el) {
				el.textContent = user.displayName || user.username || 'User';
				console.log('Header user name updated (retry) to:', el.textContent);
			}
		}, 200);
	}
}

function layoutNav() {
	const nav = document.querySelector('.layout-nav'); if (!nav) return;
	let t = null;
	nav.addEventListener('click', e => {
		const i = e.target.closest('.nav-item'); if (!i || !nav.contains(i)) return;
		if (t) clearTimeout(t);
		nav.querySelectorAll('.nav-item.on').forEach(el => el.classList.remove('on'));
		t = setTimeout(() => { i.classList.add('on'); t = null; }, 100);
	}, { passive: true });
}

function nav_status() {
	let file = location.pathname.split('/').pop() || 'index';
	file = decodeURIComponent(file).replace(/\.(html?)$/i, '').toLowerCase();

	document.querySelectorAll('.side-link').forEach(a => {
		const pages = (a.dataset.page || a.getAttribute('data-page') || '')
			.split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
		if (pages.includes(file)) {
			a.classList.add('on');
			a.setAttribute('aria-current', 'page');
			a.closest('.nav-item')?.classList.add('on');
		}
	});
}
