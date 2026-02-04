/**
 * Agent Admin - Inquiry List Page JavaScript
 */

let currentPage = 1;
let currentFilters = {
    status: '',
    sort: 'latest'
};

document.addEventListener('DOMContentLoaded', function() {
    initializeInquiryList();
});

function initializeInquiryList() {
    // 상태 필터 변경 감지
    const statusSelect = document.querySelector('.search-form select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentFilters.status = this.value;
            currentPage = 1;
            loadInquiries();
        });
    }
    
    // 정렬 변경 감지
    const sortSelect = document.querySelector('.search-form select[name="sort"]');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentFilters.sort = this.value;
            currentPage = 1;
            loadInquiries();
        });
    }
    
    // 초기 로드
    loadInquiries();
}

async function loadInquiries() {
    try {
        showLoading();
        
        const params = new URLSearchParams({
            action: 'getInquiries',
            page: currentPage,
            limit: 20,
            ...currentFilters
        });
        
        const response = await fetch(`../backend/api/agent-api.php?${params.toString()}`);
        const result = await response.json();
        
        if (result.success) {
            renderInquiries(result.data.inquiries);
            renderPagination(result.data.pagination);
            updateResultCount(result.data.pagination.total);
        } else {
            console.error('Failed to load inquiries:', result.message);
            showError('문의 목록을 불러오는데 실패했습니다.');
        }
    } catch (error) {
        console.error('Error loading inquiries:', error);
        showError('문의 목록을 불러오는 중 오류가 발생했습니다.');
    } finally {
        hideLoading();
    }
}

function renderInquiries(inquiries) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (!tbody) return;
    
    if (inquiries.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="is-center">문의 내역이 없습니다.</td></tr>';
        return;
    }
    
    tbody.innerHTML = inquiries.map(item => `
        <tr onclick="goToInquiryDetail(${item.inquiryId})">
            <td class="no is-center">${item.rowNum}</td>
            <td class="is-center">${escapeHtml(item.inquiryTitle)}</td>
            <td class="is-center">${formatDate(item.createdAt)}</td>
            <td class="is-center">${escapeHtml(item.statusLabel)}</td>
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
    loadInquiries();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToInquiryDetail(inquiryId) {
    window.location.href = `inquiry-detail.html?id=${inquiryId}`;
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
        tbody.innerHTML = '<tr><td colspan="4" class="is-center">로딩 중...</td></tr>';
    }
}

function hideLoading() {
    // 로딩은 renderInquiries에서 처리됨
}

function showError(message) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="4" class="is-center" style="color: red;">${escapeHtml(message)}</td></tr>`;
    }
}

function formatDate(datetime) {
    if (!datetime) return '';
    const date = new Date(datetime);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
