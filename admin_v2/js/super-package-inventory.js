/**
 * Package Inventory Management JavaScript
 * 패키지 재고/가격 관리 페이지 스크립트
 */

// 전역 변수
let selectedPackageId = null;
let selectedPackageData = null;
let selectedProductInModal = null;
let inventoryData = [];
let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth() + 1;
let editingDateId = null;
let hasChanges = false;

// API 기본 URL
const API_BASE = window.location.origin;

// DOM 로드 후 초기화
document.addEventListener('DOMContentLoaded', function () {
    initPage();
});

/**
 * 페이지 초기화
 */
function initPage() {
    // URL에서 packageId 파라미터 확인
    const urlParams = new URLSearchParams(window.location.search);
    const packageIdFromUrl = urlParams.get('packageId');

    if (packageIdFromUrl) {
        loadProductById(parseInt(packageIdFromUrl, 10));
    }

    // 검색 입력 엔터 키 이벤트
    const searchInput = document.getElementById('product-search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }

    // 저장 버튼 이벤트
    const saveAllBtn = document.getElementById('saveAllBtn');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', saveAllChanges);
    }
}

/**
 * 모달 열기
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

/**
 * 모달 닫기
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// 전역 함수로 등록
window.closeModal = closeModal;
window.openModal = openModal;

/**
 * 상품 검색 모달 열기
 */
function openProductSearchModal() {
    selectedProductInModal = null;
    document.getElementById('product-search-input').value = '';
    document.getElementById('product-search-results').innerHTML = '';
    openModal('product-search-modal');
    // 전체 상품 목록 자동 로드
    loadProductList();
}
window.openProductSearchModal = openProductSearchModal;

/**
 * 상품 검색
 */
async function searchProducts() {
    const searchInput = document.getElementById('product-search-input');
    const searchTerm = searchInput.value.trim();
    loadProductList(searchTerm);
}
window.searchProducts = searchProducts;

/**
 * 상품 목록 로드
 */
async function loadProductList(searchTerm = '') {
    const resultsContainer = document.getElementById('product-search-results');

    try {
        resultsContainer.innerHTML = '<div class="is-center">로딩 중...</div>';

        let apiUrl = `${API_BASE}/backend/api/packages.php?limit=50`;
        if (searchTerm) {
            apiUrl += `&search=${encodeURIComponent(searchTerm)}`;
        }

        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.success && result.data && result.data.length > 0) {
            let html = '<div class="product-list">';
            result.data.forEach(pkg => {
                const isSelected = selectedProductInModal === pkg.packageId ? 'selected' : '';
                html += `
                    <div class="product-item ${isSelected}" data-package-id="${pkg.packageId}" onclick="selectProductInModal(${pkg.packageId})">
                        <div class="product-name">${escapeHtml(pkg.packageName || '')}</div>
                        <div class="product-price">₱${formatCurrency(pkg.packagePrice || 0)}</div>
                    </div>
                `;
            });
            html += '</div>';
            resultsContainer.innerHTML = html;
        } else {
            resultsContainer.innerHTML = '<div class="is-center">검색 결과가 없습니다</div>';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        resultsContainer.innerHTML = '<div class="is-center">오류가 발생했습니다</div>';
    }
}

/**
 * 모달에서 상품 선택
 */
function selectProductInModal(packageId) {
    // 이전 선택 제거
    document.querySelectorAll('.product-item').forEach(item => {
        item.classList.remove('selected');
    });

    // 현재 선택 표시
    const selectedItem = document.querySelector(`[data-package-id="${packageId}"]`);
    if (selectedItem) {
        selectedItem.classList.add('selected');
    }

    selectedProductInModal = packageId;
}
window.selectProductInModal = selectProductInModal;

/**
 * 상품 선택 확인
 */
function confirmProductSelection() {
    if (!selectedProductInModal) {
        alert('상품을 선택해주세요.');
        return;
    }

    loadProductById(selectedProductInModal);
    closeModal('product-search-modal');
}
window.confirmProductSelection = confirmProductSelection;

/**
 * 상품 ID로 상품 정보 로드
 */
async function loadProductById(packageId) {
    try {
        const apiUrl = `${API_BASE}/backend/api/packages.php?id=${encodeURIComponent(packageId)}`;
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.success && result.data) {
            selectedPackageId = packageId;
            selectedPackageData = result.data;

            // UI 업데이트
            document.getElementById('no-product-selected').style.display = 'none';
            document.getElementById('selected-product-info').style.display = 'flex';
            document.getElementById('calendar-section').style.display = 'block';
            document.getElementById('saveAllBtn').style.display = 'flex';

            document.getElementById('selected-product-name').textContent = result.data.packageName || '-';
            document.getElementById('selected-product-price').textContent = `₱${formatCurrency(result.data.packagePrice || 0)}`;

            // URL 업데이트 (히스토리에 기록)
            const newUrl = `${window.location.pathname}?packageId=${packageId}`;
            window.history.pushState({ packageId }, '', newUrl);

            // 재고 데이터 로드
            loadInventoryData();
        } else {
            alert('상품 정보를 불러올 수 없습니다.');
        }
    } catch (error) {
        console.error('Error loading product:', error);
        alert('상품 정보 로드 중 오류가 발생했습니다.');
    }
}

/**
 * 재고 데이터 로드
 */
async function loadInventoryData() {
    if (!selectedPackageId) return;

    const calendarContainer = document.getElementById('inventory-calendar');
    const loadingEl = document.getElementById('calendar-loading');
    const emptyEl = document.getElementById('calendar-empty');

    // 기존 캘린더 셀 제거 (헤더 제외)
    const existingCells = calendarContainer.querySelectorAll('.inventory-day');
    existingCells.forEach(cell => cell.remove());

    loadingEl.style.display = 'block';
    emptyEl.style.display = 'none';

    try {
        const apiUrl = `${API_BASE}/backend/api/package-availability-admin.php?packageId=${selectedPackageId}`;
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        loadingEl.style.display = 'none';

        if (result.success && result.data && result.data.length > 0) {
            inventoryData = result.data;

            // 예약 가능한(open) 첫 번째 날짜가 있는 달로 이동
            const firstAvailableMonth = findFirstAvailableMonth();
            if (firstAvailableMonth) {
                currentYear = firstAvailableMonth.year;
                currentMonth = firstAvailableMonth.month;
            }

            renderCalendar();
        } else {
            emptyEl.style.display = 'block';
            inventoryData = [];
        }
    } catch (error) {
        console.error('Error loading inventory:', error);
        loadingEl.style.display = 'none';
        emptyEl.style.display = 'block';
        alert('재고 데이터 로드 중 오류가 발생했습니다.');
    }
}

/**
 * 예약 가능한(open) 첫 번째 날짜가 있는 달 찾기
 * @returns {Object|null} { year, month } 또는 null
 */
function findFirstAvailableMonth() {
    if (!inventoryData || inventoryData.length === 0) return null;

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = formatDateStr(today);

    // 오늘 이후의 open 상태인 날짜들 필터링
    const availableDates = inventoryData.filter(item => {
        return item.status === 'open' && item.availableDate >= todayStr;
    });

    if (availableDates.length === 0) {
        // open 날짜가 없으면 데이터가 있는 첫 번째 달로 (과거 포함)
        const sortedDates = [...inventoryData].sort((a, b) =>
            a.availableDate.localeCompare(b.availableDate)
        );

        if (sortedDates.length > 0) {
            const firstDate = sortedDates[0].availableDate;
            const [year, month] = firstDate.split('-').map(Number);
            return { year, month };
        }
        return null;
    }

    // 가장 빠른 open 날짜 찾기
    const sortedAvailable = availableDates.sort((a, b) =>
        a.availableDate.localeCompare(b.availableDate)
    );

    const firstAvailableDate = sortedAvailable[0].availableDate;
    const [year, month] = firstAvailableDate.split('-').map(Number);

    return { year, month };
}

/**
 * Date 객체를 YYYY-MM-DD 문자열로 변환
 */
function formatDateStr(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * 캘린더 렌더링
 */
function renderCalendar() {
    const calendarContainer = document.getElementById('inventory-calendar');
    const monthDisplay = document.getElementById('month-display');

    // 기존 캘린더 셀 제거 (헤더 제외)
    const existingCells = calendarContainer.querySelectorAll('.inventory-day');
    existingCells.forEach(cell => cell.remove());

    // 월 표시 업데이트
    const monthNames = ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'];
    monthDisplay.textContent = `${currentYear}년 ${monthNames[currentMonth - 1]}`;

    // 해당 월의 첫날과 마지막 날
    const firstDay = new Date(currentYear, currentMonth - 1, 1);
    const lastDay = new Date(currentYear, currentMonth, 0);
    const daysInMonth = lastDay.getDate();
    const startDayOfWeek = firstDay.getDay();

    // 오늘 날짜
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // 이번 달 데이터 필터링
    const monthStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}`;
    const monthData = inventoryData.filter(item =>
        item.availableDate && item.availableDate.startsWith(monthStr)
    );

    // 날짜별 데이터 맵 생성
    const dataByDate = {};
    monthData.forEach(item => {
        dataByDate[item.availableDate] = item;
    });

    // 이전 달 빈 셀 추가
    for (let i = 0; i < startDayOfWeek; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'inventory-day inactive';
        calendarContainer.appendChild(emptyCell);
    }

    // 날짜 셀 추가
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dateObj = new Date(currentYear, currentMonth - 1, day);
        const dayOfWeek = dateObj.getDay();
        const isPast = dateObj < today;

        const dayData = dataByDate[dateStr];

        const cell = document.createElement('div');
        cell.className = 'inventory-day';

        if (dayOfWeek === 0) cell.classList.add('sunday');
        if (dayOfWeek === 6) cell.classList.add('saturday');
        if (isPast) cell.classList.add('past');

        if (dayData) {
            const statusClass = dayData.status === 'open' ? 'open' : 'closed';
            const statusText = dayData.status === 'open' ? '오픈' : '마감';
            const capacityText = dayData.capacity !== null ? dayData.capacity : '-';
            const reservedCount = dayData.reserved || 0;
            const priceText = dayData.price !== null ? `₱${formatCurrency(dayData.price)}` : '-';

            // 예약률 색상 결정
            let reservedColor = '#10b981'; // 녹색 (여유)
            if (dayData.capacity !== null && dayData.capacity > 0) {
                const ratio = reservedCount / dayData.capacity;
                if (ratio >= 1) reservedColor = '#ef4444'; // 빨강 (마감)
                else if (ratio >= 0.8) reservedColor = '#f59e0b'; // 주황 (거의 마감)
            }

            cell.innerHTML = `
                <div class="day-number">${day}</div>
                <div class="day-info">
                    <div class="info-row">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">예약:</span>
                        <span class="info-value" style="color: ${reservedColor}; font-weight: 600;">${reservedCount}${capacityText !== '-' ? '/' + capacityText : ''}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">가격:</span>
                        <span class="info-value">${priceText}</span>
                    </div>
                </div>
                <button type="button" class="edit-btn" onclick="openDateEditModal(${dayData.id}, '${dateStr}')">수정</button>
            `;
        } else {
            cell.innerHTML = `
                <div class="day-number">${day}</div>
                <div class="day-info">
                    <div class="info-row" style="color: #999; font-size: 11px;">등록된 데이터 없음</div>
                </div>
            `;
            if (!isPast) {
                cell.classList.add('inactive');
            }
        }

        calendarContainer.appendChild(cell);
    }

    // 다음 달 빈 셀 추가 (7열 맞추기)
    const totalCells = startDayOfWeek + daysInMonth;
    const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 0; i < remainingCells; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'inventory-day inactive';
        calendarContainer.appendChild(emptyCell);
    }
}

/**
 * 월 변경
 */
function changeMonth(delta) {
    currentMonth += delta;

    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    } else if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }

    renderCalendar();
}
window.changeMonth = changeMonth;

/**
 * 날짜 수정 모달 열기
 */
function openDateEditModal(dateId, dateStr) {
    editingDateId = dateId;

    const dayData = inventoryData.find(item => item.id === dateId);
    if (!dayData) {
        alert('데이터를 찾을 수 없습니다.');
        return;
    }

    document.getElementById('edit-modal-title').textContent = `${dateStr} 수정`;
    document.getElementById('edit-date').value = dateStr;
    document.getElementById('edit-status').value = dayData.status || 'open';
    document.getElementById('edit-capacity').value = dayData.capacity !== null ? dayData.capacity : '';
    document.getElementById('edit-price').value = dayData.price !== null ? dayData.price : '';
    document.getElementById('edit-child-price').value = dayData.childPrice !== null ? dayData.childPrice : '';
    document.getElementById('edit-single-price').value = dayData.singlePrice !== null ? dayData.singlePrice : '';

    openModal('date-edit-modal');
}
window.openDateEditModal = openDateEditModal;

/**
 * 날짜 수정 저장
 */
async function saveDateEdit() {
    if (!editingDateId || !selectedPackageId) return;

    const status = document.getElementById('edit-status').value;
    const capacityVal = document.getElementById('edit-capacity').value;
    const priceVal = document.getElementById('edit-price').value;
    const childPriceVal = document.getElementById('edit-child-price').value;
    const singlePriceVal = document.getElementById('edit-single-price').value;

    const rowData = {
        id: editingDateId,
        status: status,
        capacity: capacityVal === '' ? null : Number(capacityVal),
        price: priceVal === '' ? null : Number(priceVal),
        childPrice: childPriceVal === '' ? null : Number(childPriceVal),
        singlePrice: singlePriceVal === '' ? null : Number(singlePriceVal)
    };

    try {
        const apiUrl = `${API_BASE}/backend/api/package-availability-admin.php?packageId=${selectedPackageId}`;
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify({ rows: [rowData] })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            alert('저장되었습니다.');
            closeModal('date-edit-modal');
            // 데이터 새로고침
            loadInventoryData();
        } else {
            alert(result.message || '저장에 실패했습니다.');
        }
    } catch (error) {
        console.error('Error saving date edit:', error);
        alert('저장 중 오류가 발생했습니다.');
    }
}
window.saveDateEdit = saveDateEdit;

/**
 * 일괄 수정 모달 열기
 */
function openBulkEditModal() {
    // 현재 월의 첫날과 마지막 날로 기본값 설정
    const firstDay = `${currentYear}-${String(currentMonth).padStart(2, '0')}-01`;
    const lastDay = new Date(currentYear, currentMonth, 0);
    const lastDayStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(lastDay.getDate()).padStart(2, '0')}`;

    document.getElementById('bulk-start-date').value = firstDay;
    document.getElementById('bulk-end-date').value = lastDayStr;
    document.getElementById('bulk-status').value = '';
    document.getElementById('bulk-capacity').value = '';
    document.getElementById('bulk-price').value = '';
    document.getElementById('bulk-child-price').value = '';
    document.getElementById('bulk-single-price').value = '';

    openModal('bulk-edit-modal');
}
window.openBulkEditModal = openBulkEditModal;

/**
 * 일괄 수정 적용
 */
async function applyBulkEdit() {
    if (!selectedPackageId) return;

    const startDate = document.getElementById('bulk-start-date').value;
    const endDate = document.getElementById('bulk-end-date').value;
    const status = document.getElementById('bulk-status').value;
    const capacityVal = document.getElementById('bulk-capacity').value;
    const priceVal = document.getElementById('bulk-price').value;
    const childPriceVal = document.getElementById('bulk-child-price').value;
    const singlePriceVal = document.getElementById('bulk-single-price').value;

    if (!startDate || !endDate) {
        alert('시작일과 종료일을 선택해주세요.');
        return;
    }

    if (new Date(startDate) > new Date(endDate)) {
        alert('종료일은 시작일보다 이후여야 합니다.');
        return;
    }

    // 해당 기간의 데이터 필터링
    const rowsToUpdate = inventoryData.filter(item => {
        return item.availableDate >= startDate && item.availableDate <= endDate;
    });

    if (rowsToUpdate.length === 0) {
        alert('선택한 기간에 수정할 데이터가 없습니다.');
        return;
    }

    // 업데이트할 데이터 구성
    const updatedRows = rowsToUpdate.map(item => {
        const row = {
            id: item.id,
            status: status || item.status,
            capacity: capacityVal !== '' ? Number(capacityVal) : item.capacity,
            price: priceVal !== '' ? Number(priceVal) : item.price,
            childPrice: childPriceVal !== '' ? Number(childPriceVal) : item.childPrice,
            singlePrice: singlePriceVal !== '' ? Number(singlePriceVal) : item.singlePrice
        };
        return row;
    });

    try {
        const apiUrl = `${API_BASE}/backend/api/package-availability-admin.php?packageId=${selectedPackageId}`;
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify({ rows: updatedRows })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            alert(`${updatedRows.length}개 날짜가 수정되었습니다.`);
            closeModal('bulk-edit-modal');
            // 데이터 새로고침
            loadInventoryData();
        } else {
            alert(result.message || '일괄 수정에 실패했습니다.');
        }
    } catch (error) {
        console.error('Error applying bulk edit:', error);
        alert('일괄 수정 중 오류가 발생했습니다.');
    }
}
window.applyBulkEdit = applyBulkEdit;

/**
 * 모든 변경사항 저장 (향후 로컬 수정 기능 추가 시 사용)
 */
async function saveAllChanges() {
    alert('개별 날짜 수정 또는 일괄 수정 기능을 이용해주세요.');
}
window.saveAllChanges = saveAllChanges;

/**
 * HTML 이스케이프
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * 통화 포맷팅
 */
function formatCurrency(amount) {
    if (amount === null || amount === undefined) return '0';
    return Number(amount).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}
