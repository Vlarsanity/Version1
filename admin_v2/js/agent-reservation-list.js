/**
 * Agent Admin - Reservation List Page JavaScript
 */

let currentPage = 1;
let currentFilters = {
    search: '',
    travelStartDate: '',
    status: '',
    searchType: ''
};

document.addEventListener('DOMContentLoaded', function() {
    initializeReservationList();
});

function initializeReservationList() {
    // 검색 폼 이벤트 리스너
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            loadReservations();
        });
    }
    
    // 날짜 필터 변경 감지 (#travelStartDate)
    const travelStartDateInput = document.getElementById('travelStartDate');
    if (travelStartDateInput) {
        // daterangepicker의 apply 이벤트 감지
        $(travelStartDateInput).on('apply.daterangepicker', function(ev, picker) {
            currentFilters.travelStartDate = picker.startDate.format('YYYY-MM-DD');
            currentPage = 1;
            loadReservations();
        });
        
        // 취소 시 필터 초기화
        $(travelStartDateInput).on('cancel.daterangepicker', function() {
            currentFilters.travelStartDate = '';
            currentPage = 1;
            loadReservations();
        });
        
        // 일반 change 이벤트도 유지
        travelStartDateInput.addEventListener('change', function() {
            if (this.value) {
                currentFilters.travelStartDate = this.value;
                currentPage = 1;
                loadReservations();
            }
        });
    }
    
    // 상태 필터 변경 감지
    const statusSelect = document.querySelector('.search-form select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentFilters.status = this.value;
            currentPage = 1;
            loadReservations();
        });
    }
    
    // 검색 타입 필터 변경 감지
    const searchTypeSelect = document.querySelector('.search-form select[name="searchType"]');
    if (searchTypeSelect) {
        searchTypeSelect.addEventListener('change', function() {
            currentFilters.searchType = this.value;
            currentPage = 1;
            loadReservations();
        });
    }
    
    // 검색 입력 필드 실시간 감지 (debounce)
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = this.value;
                currentPage = 1;
                loadReservations();
            }, 500);
        });
    }
    
    // 초기 로드
    loadReservations();
}

async function loadReservations() {
    try {
        showLoading();
        
        const params = new URLSearchParams({
            action: 'getReservations',
            page: currentPage,
            limit: 20,
            ...currentFilters
        });
        
        const response = await fetch(`../backend/api/agent-api.php?${params.toString()}`);
        const result = await response.json();
        
        if (result.success) {
            renderReservations(result.data.reservations);
            renderPagination(result.data.pagination);
            updateResultCount(result.data.pagination.total);
        } else {
            console.error('Failed to load reservations:', result.message);
            showError('예약 목록을 불러오는데 실패했습니다.');
        }
    } catch (error) {
        console.error('Error loading reservations:', error);
        showError('예약 목록을 불러오는 중 오류가 발생했습니다.');
    } finally {
        hideLoading();
    }
}

function renderReservations(reservations) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (!tbody) return;
    
    if (reservations.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="is-center">예약 내역이 없습니다.</td></tr>';
        return;
    }
    
    tbody.innerHTML = reservations.map(item => `
        <tr onclick="goToReservationDetail('${escapeHtml(item.bookingId)}')">
            <td class="no is-center">${item.rowNum}</td>
            <td class="ellipsis">${escapeHtml(item.packageName)}</td>
            <td class="is-center">${item.departureDate}</td>
            <td class="is-center">${escapeHtml(item.reserverName)}</td>
            <td class="is-center">${item.numPeople}</td>
            <td class="is-center">
                <span class="badge ${item.statusClass}">${escapeHtml(item.statusLabel)}</span>
            </td>
        </tr>
    `).join('');
}

function renderPagination(pagination) {
    const pagebox = document.querySelector('.jw-pagebox');
    if (!pagebox) return;
    
    const pageContainer = pagebox.querySelector('.page');
    if (!pageContainer) return;
    
    const totalPages = pagination.totalPages;
    const current = pagination.page;
    
    // 페이지 번호 생성
    let pageNumbers = [];
    const maxPages = 5;
    let startPage = Math.max(1, current - Math.floor(maxPages / 2));
    let endPage = Math.min(totalPages, startPage + maxPages - 1);
    
    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        pageNumbers.push(i);
    }
    
    pageContainer.innerHTML = pageNumbers.map(page => `
        <button type="button" class="p ${page === current ? 'show' : ''}" 
                role="listitem" ${page === current ? 'aria-current="page"' : ''}
                onclick="goToPage(${page})">${page}</button>
    `).join('');
    
    // 첫 페이지/이전 페이지 버튼 상태
    const firstBtn = pagebox.querySelector('.first');
    const prevBtn = pagebox.querySelector('.prev');
    if (firstBtn && prevBtn) {
        const disabled = current === 1;
        firstBtn.disabled = disabled;
        prevBtn.disabled = disabled;
        firstBtn.setAttribute('aria-disabled', disabled);
        prevBtn.setAttribute('aria-disabled', disabled);
        if (!disabled) {
            firstBtn.onclick = () => goToPage(1);
            prevBtn.onclick = () => goToPage(current - 1);
        }
    }
    
    // 다음 페이지/마지막 페이지 버튼 상태
    const nextBtn = pagebox.querySelector('.next');
    const lastBtn = pagebox.querySelector('.last');
    if (nextBtn && lastBtn) {
        const disabled = current === totalPages;
        nextBtn.disabled = disabled;
        lastBtn.disabled = disabled;
        nextBtn.setAttribute('aria-disabled', disabled);
        lastBtn.setAttribute('aria-disabled', disabled);
        if (!disabled) {
            nextBtn.onclick = () => goToPage(current + 1);
            lastBtn.onclick = () => goToPage(totalPages);
        }
    }
}

function goToPage(page) {
    currentPage = page;
    loadReservations();
    // 페이지 상단으로 스크롤
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToReservationDetail(bookingId) {
    window.location.href = `reservation-detail.php?id=${bookingId}`;
}

function updateResultCount(total) {
    const resultCountNum = document.querySelector('.result-count__num');
    if (resultCountNum) {
        resultCountNum.textContent = total;
    }
}

function showLoading() {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="6" class="is-center">로딩 중...</td></tr>';
    }
}

function hideLoading() {
    // 로딩은 renderReservations에서 처리됨
}

function showError(message) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="is-center" style="color: red;">${escapeHtml(message)}</td></tr>`;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 다운로드 기능
function downloadReservations() {
    try {
        // 현재 필터 조건 가져오기
        const filters = {
            search: currentFilters.search || '',
            travelStartDate: currentFilters.travelStartDate || '',
            status: currentFilters.status || '',
            searchType: currentFilters.searchType || ''
        };
        
        // 쿼리 파라미터 구성
        const params = new URLSearchParams();
        params.append('action', 'downloadReservations');
        if (filters.search) params.append('search', filters.search);
        if (filters.travelStartDate) params.append('travelStartDate', filters.travelStartDate);
        if (filters.status) params.append('status', filters.status);
        if (filters.searchType) params.append('searchType', filters.searchType);
        
        // 다운로드 URL 생성
        const downloadUrl = `../backend/api/agent-api.php?${params.toString()}`;
        
        // 새 창에서 다운로드 실행
        window.location.href = downloadUrl;
    } catch (error) {
        console.error('Download error:', error);
        alert('다운로드 중 오류가 발생했습니다.');
    }
}
