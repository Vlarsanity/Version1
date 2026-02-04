/**
 * Reservation Notices Page JavaScript
 */

let currentBookingId = null;
let currentNoticeId = null;
let currentPage = 1;
let totalPages = 1;

document.addEventListener('DOMContentLoaded', function() {
    // URL에서 bookingId와 noticeId 가져오기
    const urlParams = new URLSearchParams(window.location.search);
    currentBookingId = urlParams.get('bookingId') || urlParams.get('id');
    currentNoticeId = urlParams.get('noticeId') || urlParams.get('notice_id');
    
    // 공지사항 목록 로드
    loadNoticeList();
    
    // 특정 공지사항이 선택된 경우 상세 정보 로드
    if (currentNoticeId) {
        loadNoticeDetail(currentNoticeId);
    } else if (currentBookingId) {
        // bookingId가 있으면 해당 예약의 최신 공지사항 로드
        loadLatestNoticeForBooking();
    }
});

// 공지사항 목록 로드
async function loadNoticeList(page = 1) {
    try {
        currentPage = page;
        
        let url = `../backend/api/agent-api.php?action=getNotices&page=${page}&limit=10`;
        if (currentBookingId) {
            url += `&bookingId=${currentBookingId}`;
        }
        
        const response = await fetch(url);
        const result = await response.json();
        
        const tbody = document.getElementById('notice-list-tbody');
        if (!tbody) return;
        
        if (result.success && result.data && result.data.notices) {
            const notices = result.data.notices;
            totalPages = result.data.totalPages || 1;
            
            if (notices.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="is-center" data-lan-eng="No notices">공지사항이 없습니다.</td></tr>';
                renderPagination();
                return;
            }
            
            tbody.innerHTML = notices.map((notice, index) => {
                const rowNum = (page - 1) * 10 + index + 1;
                const status = notice.status === 'active' || notice.status === 'register' ? 
                              (getCurrentLang() === 'eng' ? 'Register' : '등록') : 
                              (getCurrentLang() === 'eng' ? 'Deleted' : '삭제');
                
                return `
                    <tr onclick="selectNotice(${notice.noticeId || notice.id})">
                        <td class="no is-center">${rowNum}</td>
                        <td class="is-center">${escapeHtml(notice.title || notice.noticeTitle || '')}</td>
                        <td class="is-center">${formatDateTime(notice.createdAt || notice.registrationDate || notice.createdDate)}</td>
                        <td class="is-center">${status}</td>
                    </tr>
                `;
            }).join('');
            
            // 페이지네이션 렌더링
            renderPagination();
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="is-center" data-lan-eng="No notices">공지사항이 없습니다.</td></tr>';
            renderPagination();
        }
    } catch (error) {
        console.error('Error loading notice list:', error);
        const tbody = document.getElementById('notice-list-tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="4" class="is-center" data-lan-eng="Error loading notices">공지사항을 불러오는 중 오류가 발생했습니다.</td></tr>';
        }
    }
}

// 공지사항 상세 정보 로드
async function loadNoticeDetail(noticeId) {
    try {
        const response = await fetch(`../backend/api/agent-api.php?action=getNoticeDetail&noticeId=${noticeId}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const notice = result.data.notice || result.data;
            
            // 등록 일시
            const registrationInput = document.getElementById('notice_registration_datetime');
            if (registrationInput && notice.createdAt) {
                registrationInput.value = formatDateTime(notice.createdAt);
            }
            
            // 제목
            const titleInput = document.getElementById('notice_title');
            if (titleInput) {
                titleInput.value = notice.title || notice.noticeTitle || '';
            }
            
            // 내용
            const contentTextarea = document.getElementById('notice_content');
            if (contentTextarea) {
                contentTextarea.value = notice.content || notice.noticeContent || notice.description || '';
            }
        }
    } catch (error) {
        console.error('Error loading notice detail:', error);
        // API가 없거나 실패한 경우 기본값 표시
        const titleInput = document.getElementById('notice_title');
        const contentTextarea = document.getElementById('notice_content');
        if (titleInput) titleInput.value = 'Title';
        if (contentTextarea) contentTextarea.value = 'This is the content';
    }
}

// 예약에 대한 최신 공지사항 로드
async function loadLatestNoticeForBooking() {
    try {
        if (!currentBookingId) return;
        
        // 예약 정보에서 공지사항 정보 가져오기
        const response = await fetch(`../backend/api/agent-api.php?action=getReservationDetail&bookingId=${currentBookingId}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const booking = result.data.booking;
            const selectedOptions = result.data.selectedOptions || {};
            
            // 공지사항 정보가 예약 데이터에 포함된 경우
            if (booking.noticeTitle || selectedOptions.noticeTitle) {
                const registrationInput = document.getElementById('notice_registration_datetime');
                if (registrationInput && booking.createdAt) {
                    registrationInput.value = formatDateTime(booking.createdAt);
                }
                
                const titleInput = document.getElementById('notice_title');
                if (titleInput) {
                    titleInput.value = booking.noticeTitle || selectedOptions.noticeTitle || '';
                }
                
                const contentTextarea = document.getElementById('notice_content');
                if (contentTextarea) {
                    contentTextarea.value = booking.noticeContent || selectedOptions.noticeContent || '';
                }
            }
        }
    } catch (error) {
        console.error('Error loading latest notice for booking:', error);
    }
}

// 공지사항 선택
function selectNotice(noticeId) {
    currentNoticeId = noticeId;
    loadNoticeDetail(noticeId);
    
    // URL 업데이트 (히스토리 추가)
    const url = new URL(window.location);
    url.searchParams.set('noticeId', noticeId);
    window.history.pushState({ noticeId }, '', url);
}

// 페이지네이션 렌더링
function renderPagination() {
    const paginationContainer = document.getElementById('notice-pagination');
    if (!paginationContainer || totalPages <= 1) {
        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }
        return;
    }
    
    let html = '<div class="contents">';
    
    // 첫 페이지 버튼
    html += `<button type="button" class="first" aria-label="첫 페이지" ${currentPage === 1 ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadNoticeList(1)">
        <img src="../image/first.svg" alt="">
    </button>`;
    
    // 이전 페이지 버튼
    html += `<button type="button" class="prev" aria-label="이전 페이지" ${currentPage === 1 ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadNoticeList(${currentPage - 1})">
        <img src="../image/prev.svg" alt="">
    </button>`;
    
    // 페이지 번호
    html += '<div class="page" role="list">';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<button type="button" class="p ${i === currentPage ? 'show' : ''}" role="listitem" ${i === currentPage ? 'aria-current="page"' : ''} onclick="loadNoticeList(${i})">${i}</button>`;
    }
    html += '</div>';
    
    // 다음 페이지 버튼
    html += `<button type="button" class="next" aria-label="다음 페이지" ${currentPage === totalPages ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadNoticeList(${currentPage + 1})">
        <img src="../image/next.svg" alt="">
    </button>`;
    
    // 마지막 페이지 버튼
    html += `<button type="button" class="last" aria-label="마지막 페이지" ${currentPage === totalPages ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadNoticeList(${totalPages})">
        <img src="../image/last.svg" alt="">
    </button>`;
    
    html += '</div>';
    paginationContainer.innerHTML = html;
}

// 유틸리티 함수
function formatDateTime(datetime) {
    if (!datetime) return '';
    const date = new Date(datetime);
    if (isNaN(date.getTime())) return datetime;
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getCurrentLang() {
    const htmlLang = document.documentElement.getAttribute('lang');
    return htmlLang === 'en' ? 'eng' : 'ko';
}

