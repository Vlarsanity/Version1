/**
 * Agent Admin - Customer List Page JavaScript
 */

let currentPage = 1;
let currentFilters = {
    search: ''
};

let batchModalState = {
    dialog: null,
    selectedFile: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeCustomerList();
});

document.addEventListener('modal:loaded', function(event) {
    const { dialog, action } = event.detail || {};
    if (!dialog || !action) return;
    if (action.includes('customer-batch-upload-modal.html')) {
        initializeBatchUploadModal(dialog);
    }
});

function initializeCustomerList() {
    // 검색 폼 이벤트 리스너
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            loadCustomers();
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
                loadCustomers();
            }, 500);
        });
    }
    
    // 초기 로드
    loadCustomers();
}

async function loadCustomers() {
    try {
        showLoading();
        
        const params = new URLSearchParams({
            action: 'getCustomers',
            page: currentPage,
            limit: 20,
            ...currentFilters
        });
        
        const response = await fetch(`../backend/api/agent-api.php?${params.toString()}`);
        
        // 응답 텍스트 가져오기
        const responseText = await response.text();
        
        // 응답 상태 확인
        if (!response.ok) {
            console.error('HTTP Error Response:', responseText);
            // JSON 에러 응답인 경우 파싱 시도
            try {
                const errorResult = JSON.parse(responseText);
                throw new Error(`HTTP ${response.status}: ${errorResult.message || 'Server error'}`);
            } catch (e) {
                // JSON이 아닌 경우 원본 텍스트 사용
                throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 500)}`);
            }
        }
        
        if (!responseText || responseText.trim() === '') {
            throw new Error('Empty response from server');
        }
        
        // JSON 파싱
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', responseText);
            // HTML 에러 페이지인 경우도 처리
            if (responseText.includes('<html') || responseText.includes('Fatal error') || responseText.includes('Parse error')) {
                throw new Error('PHP Error: ' + responseText.substring(0, 500));
            }
            throw new Error('Invalid JSON response from server: ' + responseText.substring(0, 200));
        }
        
        if (result.success) {
            renderCustomers(result.data.customers);
            renderPagination(result.data.pagination);
            updateResultCount(result.data.pagination.total);
        } else {
            console.error('Failed to load customers:', result.message);
            showError('고객 목록을 불러오는데 실패했습니다: ' + (result.message || '알 수 없는 오류'));
        }
    } catch (error) {
        console.error('Error loading customers:', error);
        showError('고객 목록을 불러오는 중 오류가 발생했습니다: ' + error.message);
    } finally {
        hideLoading();
    }
}

function renderCustomers(customers) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (!tbody) return;
    
    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="is-center">고객 내역이 없습니다.</td></tr>';
        return;
    }
    
    tbody.innerHTML = customers.map(item => `
        <tr onclick="goToCustomerDetail(${item.accountId})">
            <td class="no is-center">${item.rowNum}</td>
            <td class="is-center">${escapeHtml(item.customerName)}</td>
            <td class="is-center">${escapeHtml(item.email)}</td>
            <td class="is-center">${escapeHtml(item.phone)}</td>
            <td class="is-center">${formatDateTime(item.createdAt)}</td>
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
    loadCustomers();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToCustomerDetail(accountId) {
    window.location.href = `customer-detail.html?id=${accountId}`;
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
        tbody.innerHTML = '<tr><td colspan="5" class="is-center">로딩 중...</td></tr>';
    }
}

function hideLoading() {
    // 로딩은 renderCustomers에서 처리됨
}

function showError(message) {
    const tbody = document.querySelector('.jw-tableA tbody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="5" class="is-center" style="color: red;">${escapeHtml(message)}</td></tr>`;
    }
}

function formatDateTime(datetime) {
    if (!datetime) return '';
    const date = new Date(datetime);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function initializeBatchUploadModal(dialog) {
    batchModalState.dialog = dialog;
    batchModalState.selectedFile = null;
    dialog.addEventListener('close', resetBatchModalState, { once: true });

    const sampleBtn = dialog.querySelector('#sampleDownloadBtn');
    const fileInput = dialog.querySelector('#batchFileInput');
    const uploadBtn = dialog.querySelector('#batchFileUploadBtn');
    const fileRemoveBtn = dialog.querySelector('#fileRemoveBtn');
    const registerBtn = dialog.querySelector('#batchRegisterBtn');
    const fileInfo = dialog.querySelector('#fileInfo');

    if (fileInfo) {
        fileInfo.style.display = 'none';
    }
    if (registerBtn) {
        registerBtn.disabled = true;
    }

    if (sampleBtn) {
        sampleBtn.addEventListener('click', handleSampleDownload);
    }

    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', () => fileInput.click());
    }

    if (fileInput) {
        fileInput.addEventListener('change', handleBatchFileChange);
    }

    if (fileRemoveBtn) {
        fileRemoveBtn.addEventListener('click', () => clearBatchFile(dialog));
    }

    if (registerBtn) {
        registerBtn.addEventListener('click', handleBatchRegister);
    }

    const closeBtn = dialog.querySelector('#closeDialog');
    if (closeBtn) {
        closeBtn.addEventListener('click', resetBatchModalState, { once: true });
    }
}

function resetBatchModalState() {
    batchModalState = { dialog: null, selectedFile: null };
}

function handleSampleDownload() {
    window.location.href = '../backend/api/agent-api.php?action=downloadCustomerSample';
}

function handleBatchFileChange(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    if (!isValidBatchFile(file)) {
        alert('Excel 또는 CSV 파일만 업로드 가능합니다.');
        event.target.value = '';
        return;
    }

    batchModalState.selectedFile = file;
    displayBatchFileInfo(file);
}

function isValidBatchFile(file) {
    const validTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'text/csv'
    ];
    const validExtensions = ['.xlsx', '.xls', '.csv'];
    const fileExt = '.' + (file.name.split('.').pop() || '').toLowerCase();
    return validTypes.includes(file.type) || validExtensions.includes(fileExt);
}

function displayBatchFileInfo(file) {
    if (!batchModalState.dialog) return;
    const fileInfo = batchModalState.dialog.querySelector('#fileInfo');
    const fileName = batchModalState.dialog.querySelector('#fileName');
    const registerBtn = batchModalState.dialog.querySelector('#batchRegisterBtn');

    if (!fileInfo || !fileName || !registerBtn) return;

    const sizeInKB = (file.size / 1024).toFixed(0);
    const ext = file.name.split('.').pop()?.toLowerCase() || '';
    fileName.textContent = `파일 [${ext}, ${sizeInKB}KB]`;
    fileInfo.style.display = 'block';
    registerBtn.disabled = false;
}

function clearBatchFile(dialog = batchModalState.dialog) {
    if (!dialog) return;
    const fileInput = dialog.querySelector('#batchFileInput');
    const fileInfo = dialog.querySelector('#fileInfo');
    const registerBtn = dialog.querySelector('#batchRegisterBtn');

    if (fileInput) fileInput.value = '';
    if (fileInfo) fileInfo.style.display = 'none';
    if (registerBtn) registerBtn.disabled = true;

    batchModalState.selectedFile = null;
}

async function handleBatchRegister() {
    const file = batchModalState.selectedFile;
    if (!file) {
        alert('파일을 선택해주세요.');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('action', 'batchUploadCustomers');

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || '알 수 없는 오류');
        }

        alert(`고객 등록이 완료되었습니다. (성공: ${result.data?.successCount ?? 0}, 오류: ${result.data?.errorCount ?? 0})`);
        resetBatchModalState();
        modal_close();
        loadCustomers();
    } catch (error) {
        console.error('Batch upload error:', error);
        alert('등록 중 오류가 발생했습니다: ' + error.message);
    }
}
