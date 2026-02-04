/**
 * Reservation Location Page JavaScript
 */

let currentBookingId = null;
let currentPage = 1;
let totalPages = 1;

document.addEventListener('DOMContentLoaded', function() {
    // URL에서 bookingId 가져오기
    const urlParams = new URLSearchParams(window.location.search);
    currentBookingId = urlParams.get('bookingId') || urlParams.get('id');
    
    if (currentBookingId) {
        loadLocationDetail();
        loadLocationHistory();
    } else {
        // bookingId가 없으면 기본 정보만 표시
        console.warn('Booking ID not found in URL');
    }
});

// 집합 위치 상세 정보 로드
async function loadLocationDetail() {
    try {
        if (!currentBookingId) return;
        
        const response = await fetch(`../backend/api/agent-api.php?action=getReservationDetail&bookingId=${currentBookingId}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const booking = result.data.booking;
            const selectedOptions = result.data.selectedOptions || {};
            
            // 등록 일시 (예약 생성일시)
            const registrationInput = document.getElementById('registration_datetime');
            if (registrationInput && booking.createdAt) {
                registrationInput.value = formatDateTime(booking.createdAt);
            }
            
            // 미팅 시간
            const meetingTimeInput = document.getElementById('meeting_time');
            if (meetingTimeInput) {
                const meetingTime = booking.meetingTime || booking.meetTime || selectedOptions.meetingTime || '';
                if (meetingTime) {
                    // 시간만 추출 (날짜가 포함된 경우)
                    const timeOnly = meetingTime.includes(' ') ? meetingTime.split(' ')[1] : meetingTime;
                    meetingTimeInput.value = timeOnly;
                }
            }
            
            // 장소명 및 주소
            const placeNameInput = document.getElementById('place_name');
            const addressInput = document.getElementById('address');
            
            // meeting_location 또는 meetPlace에서 정보 추출
            const meetingLocation = booking.meetingPlace || booking.meetPlace || selectedOptions.meetingLocation || '';
            
            if (meetingLocation) {
                // 주소 형식인 경우 파싱 (예: "장소명, 주소" 또는 "주소")
                if (meetingLocation.includes(',')) {
                    const parts = meetingLocation.split(',').map(s => s.trim());
                    if (placeNameInput && parts.length > 0) {
                        placeNameInput.value = parts[0];
                    }
                    if (addressInput && parts.length > 1) {
                        addressInput.value = parts.slice(1).join(', ');
                    } else if (addressInput) {
                        addressInput.value = meetingLocation;
                    }
                } else {
                    // 주소만 있는 경우
                    if (addressInput) {
                        addressInput.value = meetingLocation;
                    }
                }
            }
            
            // 내용
            const contentTextarea = document.getElementById('location_content');
            if (contentTextarea) {
                const content = selectedOptions.locationContent || selectedOptions.meetingLocationContent || 
                              booking.locationContent || booking.meetingLocationContent || '';
                contentTextarea.value = content;
            }
        }
    } catch (error) {
        console.error('Error loading location detail:', error);
    }
}

// 집합 위치 이력 로드
async function loadLocationHistory(page = 1) {
    try {
        currentPage = page;
        
        const response = await fetch(`../backend/api/agent-api.php?action=getLocationHistory&bookingId=${currentBookingId}&page=${page}&limit=10`);
        const result = await response.json();
        
        const tbody = document.getElementById('location-history-tbody');
        if (!tbody) return;
        
        if (result.success && result.data && result.data.locations) {
            const locations = result.data.locations;
            totalPages = result.data.totalPages || 1;
            
            if (locations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="is-center" data-lan-eng="No location history">집합 위치 이력이 없습니다.</td></tr>';
                return;
            }
            
            tbody.innerHTML = locations.map((location, index) => {
                const rowNum = (page - 1) * 10 + index + 1;
                const status = location.status === 'active' || location.status === 'register' ? 
                              (getCurrentLang() === 'eng' ? 'Register' : '등록') : 
                              (getCurrentLang() === 'eng' ? 'Deleted' : '삭제');
                
                return `
                    <tr onclick="selectLocation(${location.locationId || location.id})">
                        <td class="no is-center">${rowNum}</td>
                        <td class="is-center">${escapeHtml(location.placeName || location.name || '')}</td>
                        <td class="is-center">${formatDateTime(location.createdAt || location.registrationDate)}</td>
                        <td class="is-center">${escapeHtml(location.address || '')}</td>
                        <td class="is-center">${status}</td>
                    </tr>
                `;
            }).join('');
            
            // 페이지네이션 렌더링
            renderPagination();
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="is-center" data-lan-eng="No location history">집합 위치 이력이 없습니다.</td></tr>';
        }
    } catch (error) {
        console.error('Error loading location history:', error);
        const tbody = document.getElementById('location-history-tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="is-center" data-lan-eng="Error loading location history">이력을 불러오는 중 오류가 발생했습니다.</td></tr>';
        }
    }
}

// 위치 선택
function selectLocation(locationId) {
    // 선택한 위치의 상세 정보를 메인 영역에 표시
    // TODO: API를 통해 선택한 위치의 상세 정보를 가져와서 표시
    console.log('Selected location:', locationId);
}

// 페이지네이션 렌더링
function renderPagination() {
    const paginationContainer = document.getElementById('location-pagination');
    if (!paginationContainer || totalPages <= 1) {
        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }
        return;
    }
    
    let html = '<div class="contents">';
    
    // 첫 페이지 버튼
    html += `<button type="button" class="first" aria-label="첫 페이지" ${currentPage === 1 ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadLocationHistory(1)">
        <img src="../image/first.svg" alt="">
    </button>`;
    
    // 이전 페이지 버튼
    html += `<button type="button" class="prev" aria-label="이전 페이지" ${currentPage === 1 ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadLocationHistory(${currentPage - 1})">
        <img src="../image/prev.svg" alt="">
    </button>`;
    
    // 페이지 번호
    html += '<div class="page" role="list">';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<button type="button" class="p ${i === currentPage ? 'show' : ''}" role="listitem" ${i === currentPage ? 'aria-current="page"' : ''} onclick="loadLocationHistory(${i})">${i}</button>`;
    }
    html += '</div>';
    
    // 다음 페이지 버튼
    html += `<button type="button" class="next" aria-label="다음 페이지" ${currentPage === totalPages ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadLocationHistory(${currentPage + 1})">
        <img src="../image/next.svg" alt="">
    </button>`;
    
    // 마지막 페이지 버튼
    html += `<button type="button" class="last" aria-label="마지막 페이지" ${currentPage === totalPages ? 'aria-disabled="true" disabled' : 'aria-disabled="false"'} onclick="loadLocationHistory(${totalPages})">
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

