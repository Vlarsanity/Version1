/**
 * Employee Admin - Overview Page JavaScript
 */

// 다국어 텍스트 정의
const overviewTexts = {
    eng: {
        case: ''
    },
    kor: {
        case: '건'
    }
};

// 현재 언어 가져오기
function getCurrentLang() {
    const lang = getCookie('lang') || 'eng';
    return lang === 'eng' ? 'eng' : 'kor';
}

// 다국어 텍스트 가져오기
function getText(key) {
    const lang = getCurrentLang();
    return overviewTexts[lang]?.[key] || overviewTexts['eng'][key] || key;
}

document.addEventListener('DOMContentLoaded', function() {
    updateCurrentDate();
    loadOverviewData();
});

function updateCurrentDate() {
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
        const now = new Date();
        const months = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
        const dateString = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
        dateElement.textContent = dateString;
        dateElement.setAttribute('datetime', now.toISOString().split('T')[0]);
    }
}

async function loadOverviewData() {
    try {
        const response = await fetch('../backend/api/employee-api.php?action=getOverview');
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // 예약 현황 업데이트
            updateBookingStatus(data.bookingStatus);

            // 문의 현황 업데이트
            updateInquiryStatus(data.inquiryStatus);
        } else {
            console.error('Failed to load overview:', result.message);
        }
    } catch (error) {
        console.error('Error loading overview:', error);
    }
}

function updateBookingStatus(status) {
    const pendingDepositElement = document.querySelector('.status-item:has(.dot-blue) + .status-item:has(.dot-blue)');
    const pendingBalanceElement = document.querySelector('.status-item:has(.dot-green)');

    // 선금 확인 전
    const pendingDepositCount = document.querySelector('.overview-card-grid .card:first-child .status-item:first-child .count');
    if (pendingDepositCount) {
        const caseText = getText('case');
        // 영어일 때는 아무것도 표시하지 않음
        const lang = getCurrentLang();
        if (lang === 'eng') {
            pendingDepositCount.textContent = status.pendingDeposit;
        } else {
            pendingDepositCount.innerHTML = status.pendingDeposit + (caseText ? '<span>' + caseText + '</span>' : '');
        }
    }

    // 잔금 확인 전
    const pendingBalanceCount = document.querySelector('.overview-card-grid .card:first-child .status-item:last-child .count');
    if (pendingBalanceCount) {
        const caseText = getText('case');
        const lang = getCurrentLang();
        if (lang === 'eng') {
            pendingBalanceCount.textContent = status.pendingBalance;
        } else {
            pendingBalanceCount.innerHTML = status.pendingBalance + (caseText ? '<span>' + caseText + '</span>' : '');
        }
    }
}

function updateInquiryStatus(status) {
    // 미답변
    const unansweredCount = document.querySelector('.overview-card-grid .card:last-child .status-item:first-child .count');
    if (unansweredCount) {
        const caseText = getText('case');
        const lang = getCurrentLang();
        if (lang === 'eng') {
            unansweredCount.textContent = status.unanswered;
        } else {
            unansweredCount.innerHTML = status.unanswered + (caseText ? '<span>' + caseText + '</span>' : '');
        }
    }

    // 처리중
    const processingCount = document.querySelector('.overview-card-grid .card:last-child .status-item:last-child .count');
    if (processingCount) {
        const caseText = getText('case');
        const lang = getCurrentLang();
        if (lang === 'eng') {
            processingCount.textContent = status.processing;
        } else {
            processingCount.innerHTML = status.processing + (caseText ? '<span>' + caseText + '</span>' : '');
        }
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
