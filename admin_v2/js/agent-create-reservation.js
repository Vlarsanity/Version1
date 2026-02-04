/**
 * Agent Admin - Create Reservation Page JavaScript
 */

let selectedPackage = null;
let selectedCustomer = null;
let travelers = [];
let selectedRooms = [];
let selectedOptions = {};
let downPaymentProofFile = null; // 3단계 결제: 선금 증빙 파일
let currentTravelerIndex = 0;
let previousPackageId = null; // 이전 상품 ID 저장 (상품 변경 감지용)
let selectedDateInfo = null; // 선택된 날짜의 상세 정보
let availableDates = []; // 가용 가능한 날짜 목록
let calendarCurrentMonth = new Date().getMonth() + 1; // 현재 캘린더 월 (1-12)
let calendarCurrentYear = new Date().getFullYear(); // 현재 캘린더 연도
let selectedDateInCalendar = null; // 캘린더에서 선택한 날짜 (YYYY-MM-DD 형식)
let availableDatesByMonth = {}; // 월별 가용 가능한 날짜 (캐싱용)

// 모달 상태
let selectedProductInModal = null;
let selectedCustomerInModal = null;
let selectedRoomsInModal = [];

// 상품 버튼 스타일 초기화 (페이지 로드 시 즉시 적용)
(function initProductButtonStyles() {
    if (document.getElementById('product-button-styles')) return;

    const style = document.createElement('style');
    style.id = 'product-button-styles';
    style.textContent = `
        .product-flyer-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            color: #fff !important;
            border: none !important;
            padding: 8px 14px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .product-flyer-btn::before {
            content: '📄';
            font-size: 14px;
        }
        .product-flyer-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4) !important;
            transform: translateY(-1px);
        }
        .product-detail-btn {
            padding: 8px 14px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            white-space: nowrap !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .product-detail-btn::before {
            content: '📋';
            font-size: 14px;
        }
        .product-detail-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4) !important;
            transform: translateY(-1px);
        }
        .no-flyer-text, .no-detail-text {
            color: #9ca3af !important;
            font-size: 12px !important;
            font-style: italic !important;
            padding: 8px 0 !important;
        }
    `;
    document.head.appendChild(style);
})();

// 다국어 텍스트
const i18nTexts = {
    kor: {
        adult: '성인',
        child: '아동',
        infant: '유아',
        visaNotApplied: '미신청',
        visaApplied: '신청',
        male: '남성',
        female: '여성',
        other: '기타',
        firstName: '이름',
        lastName: '성',
        age: '숫자 입력',
        contact: '연락처',
        email: '이메일',
        nationality: '국적',
        passportNumber: '여권번호',
        remarks: '비고',
        searching: '검색 중...',
        noResults: '검색 결과가 없습니다.',
        errorOccurred: '검색 중 오류가 발생했습니다.',
        loading: '로딩 중...',
        noRoomOptions: '사용 가능한 룸 옵션이 없습니다.',
        cannotLoadRoomOptions: '룸 옵션을 불러올 수 없습니다.',
        errorLoadingRoomOptions: '룸 옵션을 불러오는 중 오류가 발생했습니다.',
        selectRoomOption: '룸 옵션 선택',
        selectRoomOptionCount: '룸 옵션 선택 ({count}개)',
        people: '명',
        capacity: '인원',
        price: '가격',
        pleaseSelectProduct: '상품을 선택해주세요.',
        pleaseSelectCustomer: '고객을 선택해주세요.',
        pleaseEnterProductName: '상품명을 입력해주세요.',
        requiredFields: '필수값을 입력해주세요.',
        pleaseSelectDate: '날짜를 선택해주세요.',
        selectTravelStartDate: '여행 시작일을 선택해주세요.',
        enterCustomerInfo: '예약 고객 정보를 모두 입력해주세요.',
        enterTravelerInfo: '최소 1명의 여행자 정보를 입력해주세요.',
        enterTravelerName: '{index}번째 여행자의 이름을 입력해주세요.',
        enterDepositInfo: '선금과 선금 입금 기한을 입력해주세요.',
        reservationCreated: '예약이 생성되었습니다.',
        reservationFailed: '예약 생성에 실패했습니다: {message}',
        reservationError: '예약 생성 중 오류가 발생했습니다.',
        failedToLoadProduct: '상품 정보를 불러오는데 실패했습니다.',
        errorLoadingProduct: '상품 정보를 불러오는 중 오류가 발생했습니다.',
        failedToLoadCustomer: '고객 정보를 불러오는데 실패했습니다.',
        errorLoadingCustomer: '고객 정보를 불러오는 중 오류가 발생했습니다.',
        resetRoomOptions: '룸 옵션 선택 후 인원 변경 시, 룸 옵션이 초기화됩니다. 계속하시겠습니까?',
        deleteTraveler: '테이블을 삭제하시겠습니까?',
        depositFileTooLarge: '파일 크기가 10MB를 초과했습니다.',
        depositFileSelected: '선택된 파일',
        fileUploadError: '파일 업로드 중 오류가 발생했습니다.'
    },
    eng: {
        adult: 'Adult',
        child: 'Child',
        infant: 'Infant',
        visaNotApplied: 'Not Applied',
        visaApplied: 'Applied',
        male: 'Male',
        female: 'Female',
        other: 'Other',
        firstName: 'First Name',
        lastName: 'Last Name',
        age: 'Enter number',
        contact: 'Contact',
        email: 'Email',
        nationality: 'Nationality',
        passportNumber: 'Passport Number',
        remarks: 'Remarks',
        searching: 'Searching...',
        noResults: 'No search results',
        errorOccurred: 'An error occurred while searching',
        loading: 'Loading...',
        noRoomOptions: 'No room options available',
        cannotLoadRoomOptions: 'Cannot load room options',
        errorLoadingRoomOptions: 'An error occurred while loading room options',
        selectRoomOption: 'Select Room Option',
        selectRoomOptionCount: 'Select Room Option ({count})',
        people: ' people',
        capacity: 'Capacity',
        price: 'Price',
        pleaseSelectProduct: 'Please select a product.',
        pleaseSelectCustomer: 'Please select a customer.',
        pleaseEnterProductName: 'Please enter product name.',
        requiredFields: 'Please enter required fields.',
        pleaseSelectDate: 'Please select a date.',
        selectTravelStartDate: 'Please select travel start date.',
        enterCustomerInfo: 'Please enter all customer information.',
        enterTravelerInfo: 'Please enter at least 1 traveler information.',
        enterTravelerName: 'Please enter the name of traveler {index}.',
        enterDepositInfo: 'Please enter deposit amount and due date.',
        reservationCreated: 'Reservation created successfully.',
        reservationFailed: 'Failed to create reservation: {message}',
        reservationError: 'An error occurred while creating reservation.',
        failedToLoadProduct: 'Failed to load product information.',
        errorLoadingProduct: 'An error occurred while loading product information.',
        failedToLoadCustomer: 'Failed to load customer information.',
        errorLoadingCustomer: 'An error occurred while loading customer information.',
        resetRoomOptions: 'Changing the number of people after selecting room options will reset the room options. Do you want to continue?',
        deleteTraveler: 'Do you want to delete the selected item?',
        depositFileTooLarge: 'File size must be less than 10MB.',
        depositFileSelected: 'Selected file',
        fileUploadError: 'An error occurred while processing the file.'
    }
};

// 현재 언어 가져오기
function getCurrentLang() {
    const langCookie = document.cookie.split('; ').find(row => row.startsWith('lang='));
    return langCookie ? langCookie.split('=')[1] : 'kor';
}

// 다국어 텍스트 가져오기
function getText(key, params = {}) {
    const lang = getCurrentLang();
    const langKey = lang === 'eng' ? 'eng' : 'kor';
    let text = i18nTexts[langKey][key] || i18nTexts['kor'][key] || key;
    
    // 파라미터 치환
    if (params) {
        Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
        });
    }
    
    return text;
}

document.addEventListener('DOMContentLoaded', function() {
    // HTML lang 속성 즉시 설정 (초기 로딩 시) - 가장 먼저 실행
    const htmlLang = document.getElementById('html-lang');
    if (htmlLang) {
        const currentLang = getCurrentLang();
        const langValue = currentLang === 'eng' ? 'en' : 'ko';
        const currentHtmlLang = htmlLang.getAttribute('lang');
        if (currentHtmlLang !== langValue) {
            htmlLang.setAttribute('lang', langValue);
        }
        // 날짜 입력 필드에도 직접 lang 속성 설정
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.setAttribute('lang', langValue);
        });
    }
    
    initializeCreateReservation();
    
    // 언어 변경 이벤트 리스너 (다른 스크립트에서 언어 변경 시 호출)
    // 약간의 지연을 두어 다른 스크립트의 초기화가 완료된 후 실행되도록 함
    setTimeout(() => {
        window.addEventListener('languageChanged', function(e) {
            // 이벤트가 이미 처리 중인지 확인
            if (isUpdatingLanguage) return;
            updateDynamicContentLanguage();
        });
    }, 100);
    
    // 초기 다국어 적용 (select 옵션 등)
    setTimeout(() => {
        if (typeof language_apply === 'function') {
            const currentLang = getCurrentLang();
            language_apply(currentLang);
        }
    }, 200);
});

// 동적 콘텐츠 언어 업데이트
let isUpdatingLanguage = false; // 무한 루프 방지 플래그

function updateDynamicContentLanguage() {
    // 무한 루프 방지
    if (isUpdatingLanguage) return;
    isUpdatingLanguage = true;
    
    try {
        const lang = getCurrentLang();
        
        // HTML lang 속성 업데이트 (날짜 입력 필드의 언어 설정)
        const htmlLang = document.getElementById('html-lang');
        if (htmlLang) {
            const newLang = lang === 'eng' ? 'en' : 'ko';
            const currentLang = htmlLang.getAttribute('lang');
            if (currentLang !== newLang) {
                htmlLang.setAttribute('lang', newLang);
                // 날짜 입력 필드에도 직접 lang 속성 설정
                document.querySelectorAll('input[type="date"]').forEach(input => {
                    input.setAttribute('lang', newLang);
                    // 값을 임시로 저장했다가 복원 (브라우저가 lang 변경을 인식하도록)
                    const value = input.value;
                    if (value) {
                        input.value = '';
                        setTimeout(() => {
                            input.value = value;
                        }, 10);
                    }
                });
            }
        }
        
        // 기존 여행자 행들의 select 옵션 업데이트
        document.querySelectorAll('.traveler-type option').forEach(option => {
            if (option.dataset.lanEng) {
                const key = option.value === 'adult' ? 'adult' : option.value === 'child' ? 'child' : 'infant';
                option.textContent = getText(key);
            }
        });
        
        document.querySelectorAll('.traveler-visa option').forEach(option => {
            if (option.dataset.lanEng) {
                const key = option.value === '0' ? 'visaNotApplied' : 'visaApplied';
                option.textContent = getText(key);
            }
        });
        
        document.querySelectorAll('.traveler-gender option').forEach(option => {
            if (option.dataset.lanEng) {
                const key = option.value === 'male' ? 'male' : option.value === 'female' ? 'female' : 'other';
                option.textContent = getText(key);
            }
        });
        
        // placeholder 업데이트
        document.querySelectorAll('.traveler-firstname').forEach(input => {
            if (input.dataset.lanEngPlaceholder) {
                input.placeholder = getText('firstName');
            }
        });
        
        document.querySelectorAll('.traveler-lastname').forEach(input => {
            if (input.dataset.lanEngPlaceholder) {
                input.placeholder = getText('lastName');
            }
        });
        
        document.querySelectorAll('.traveler-age').forEach(input => {
            if (input.dataset.lanEngPlaceholder) {
                input.placeholder = getText('age');
            }
        });
        
        document.querySelectorAll('.traveler-nationality').forEach(input => {
            if (input.dataset.lanEngPlaceholder) {
                input.placeholder = getText('nationality');
            }
        });
        
        document.querySelectorAll('.traveler-passport').forEach(input => {
            if (input.dataset.lanEngPlaceholder) {
                input.placeholder = getText('passportNumber');
            }
        });
        
        // 룸 옵션 버튼 텍스트 업데이트
        updateRoomOptionDisplay();
    } finally {
        isUpdatingLanguage = false;
    }
}

function initializeCreateReservation() {
    // HTML lang 속성 초기 설정 (날짜 입력 필드의 언어 설정)
    const htmlLang = document.getElementById('html-lang');
    if (htmlLang) {
        const currentLang = getCurrentLang();
        const langValue = currentLang === 'eng' ? 'en' : 'ko';
        htmlLang.setAttribute('lang', langValue);
        // 날짜 입력 필드에도 직접 lang 속성 설정
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.setAttribute('lang', langValue);
        });
    }
    
    // 상품 검색 버튼
    const productSearchBtn = document.getElementById('product_search_btn');
    if (productSearchBtn) {
        productSearchBtn.addEventListener('click', openProductSearchModal);
    }
    
    // 고객 검색 버튼
    const customerSearchBtn = document.getElementById('customer_search_btn');
    if (customerSearchBtn) {
        customerSearchBtn.addEventListener('click', openCustomerSearchModal);
    }
    
    // 고객 추가 버튼
    const addTravelerBtn = document.getElementById('add_traveler_btn');
    if (addTravelerBtn) {
        addTravelerBtn.addEventListener('click', addTraveler);
    }
    
    // 룸 옵션 선택 버튼
    const roomOptionBtn = document.getElementById('room_option_btn');
    if (roomOptionBtn) {
        roomOptionBtn.addEventListener('click', openRoomOptionModal);
    }
    
    // 저장 버튼
    const saveButton = document.getElementById('saveBtn');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }
    
    // 테스트 입력 버튼
    const testFillBtn = document.getElementById('test-fill-btn');
    if (testFillBtn) {
        testFillBtn.addEventListener('click', fillTestData);
    }
    
    initializeDownPaymentProofUpload();

    // 3단계 결제 시스템에서는 금액이 모두 고정 또는 자동 계산됨
    // 총액 변경 시 잔금만 자동 재계산
    
    // 상품 검색 모달 내 검색 버튼
    const productSearchSubmit = document.getElementById('product-search-submit');
    if (productSearchSubmit) {
        productSearchSubmit.addEventListener('click', searchProducts);
    }
    
    // 상품 검색 모달 내 입력 필드 엔터키 처리
    const productSearchInput = document.getElementById('product-search-input');
    if (productSearchInput) {
        productSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }
    
    // 고객 검색 모달 내 검색 버튼
    const customerSearchSubmit = document.getElementById('customer-search-submit');
    if (customerSearchSubmit) {
        customerSearchSubmit.addEventListener('click', searchCustomers);
    }
    
    // 고객 검색 모달 내 입력 필드 엔터키 처리
    const customerSearchInput = document.getElementById('customer-search-input');
    if (customerSearchInput) {
        customerSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchCustomers();
            }
        });
    }
    
    // 여행 고객 검색 모달 내 검색 버튼
    const travelCustomerSearchSubmit = document.getElementById('travel-customer-search-submit');
    if (travelCustomerSearchSubmit) {
        travelCustomerSearchSubmit.addEventListener('click', () => {
            searchTravelCustomers(1);
        });
    }
    
    // 여행 고객 검색 모달 내 입력 필드 엔터키 처리
    const travelCustomerSearchInput = document.getElementById('travel-customer-search-input');
    if (travelCustomerSearchInput) {
        travelCustomerSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchTravelCustomers(1);
            }
        });
    }
    
    // 여행 시작일 달력 버튼
    const departureDateBtn = document.getElementById('departure_date_btn');
    if (departureDateBtn) {
        departureDateBtn.addEventListener('click', openDatePickerModal);
    }
    
    // 날짜 선택 확인 버튼
    const confirmDateSelectionBtn = document.getElementById('confirm-date-selection');
    if (confirmDateSelectionBtn) {
        confirmDateSelectionBtn.addEventListener('click', confirmDateSelection);
    }
    
    // 캘린더 월 네비게이션
    const calendarPrevBtn = document.getElementById('calendar-prev-month');
    const calendarNextBtn = document.getElementById('calendar-next-month');
    if (calendarPrevBtn) {
        calendarPrevBtn.addEventListener('click', () => {
            calendarCurrentMonth--;
            if (calendarCurrentMonth < 1) {
                calendarCurrentMonth = 12;
                calendarCurrentYear--;
            }
            renderCalendar();
        });
    }
    if (calendarNextBtn) {
        calendarNextBtn.addEventListener('click', () => {
            calendarCurrentMonth++;
            if (calendarCurrentMonth > 12) {
                calendarCurrentMonth = 1;
                calendarCurrentYear++;
            }
            renderCalendar();
        });
    }

    // 선금 입금 기한: 예약 생성일 기준 +3일로 고정
    updateDepositDueFromCreatedDate();

    // 선금 입금 기한은 출발일에서 자동 계산되므로 직접 수정 못 하게 처리
    const depositDueInput = document.getElementById('deposit_due');
    if (depositDueInput) {
        depositDueInput.readOnly = true; // 키보드로 수정 불가
    }

    // 캘린더 버튼 비활성화
    if (depositDueInput && depositDueInput.parentElement) {
        const calendarBtn = depositDueInput.parentElement.querySelector('.btn-icon.calendar');
        if (calendarBtn) {
            calendarBtn.disabled = true;          // 버튼 자체 비활성
            calendarBtn.onclick = null;           // 기존 onclick 제거
            calendarBtn.style.pointerEvents = 'none'; // 혹시 몰라 클릭 완전 차단
            calendarBtn.style.opacity = '0.5';    // 비활성
        }
    }
    
    // 초기 여행자 1명 추가
    addTraveler();
}

// 여행 종료일 계산
function updateReturnDate() {
    const departureDateValueInput = document.getElementById('departure_date_value');
    const returnDateInput = document.getElementById('return_date');
    
    if (!departureDateValueInput || !departureDateValueInput.value || !selectedPackage || !selectedPackage.durationDays) {
        return;
    }
    
    const departureDate = new Date(departureDateValueInput.value);
    const durationDays = parseInt(selectedPackage.durationDays) || 0;
    const returnDate = new Date(departureDate);
    returnDate.setDate(returnDate.getDate() + durationDays - 1);
    
    if (returnDateInput) {
        const formattedDate = `${returnDate.getFullYear()}-${String(returnDate.getMonth() + 1).padStart(2, '0')}-${String(returnDate.getDate()).padStart(2, '0')}`;
        const displayDate = getCurrentLang() === 'eng' 
            ? returnDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
            : `${returnDate.getFullYear()}년 ${returnDate.getMonth() + 1}월 ${returnDate.getDate()}`;
        
        returnDateInput.value = displayDate;
        returnDateInput.disabled = false;
    }
}

// 모달 열기/닫기
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// 전역 함수로 등록 (HTML에서 onclick으로 호출)
window.closeModal = closeModal;
window.confirmProductSelection = confirmProductSelection;
window.confirmCustomerSelection = confirmCustomerSelection;
window.confirmRoomSelection = confirmRoomSelection;
window.openProductSearchModal = openProductSearchModal;
window.searchProducts = searchProducts;
window.openFlyerViewerModal = openFlyerViewerModal;
window.closeFlyerViewerModal = closeFlyerViewerModal;
window.openDetailViewerModal = openDetailViewerModal;
window.closeDetailViewerModal = closeDetailViewerModal;

// Flyer 뷰어 모달 열기 (A4 규격에 최적화)
function openFlyerViewerModal(flyerUrl, productName) {
    // 모달이 없으면 동적 생성
    let modal = document.getElementById('flyer-viewer-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'flyer-viewer-modal';
        modal.className = 'modal flyer-viewer-modal';
        modal.innerHTML = `
            <div class="modal-content flyer-viewer-content">
                <div class="modal-header">
                    <h3 id="flyer-viewer-title">Flyer</h3>
                    <button type="button" class="modal-close" onclick="closeFlyerViewerModal()">
                        <img src="../image/button-close2.svg" alt="Close">
                    </button>
                </div>
                <div class="modal-body flyer-viewer-body">
                    <div class="flyer-scroll-container" id="flyer-scroll-container">
                        <img id="flyer-viewer-image" src="" alt="Flyer" class="flyer-image">
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="flyer-download-link" href="" download class="jw-button typeA">Download</a>
                    <button type="button" class="jw-button typeD" onclick="closeFlyerViewerModal()">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // 모달 스타일 추가 (한 번만)
        if (!document.getElementById('flyer-viewer-styles')) {
            const style = document.createElement('style');
            style.id = 'flyer-viewer-styles';
            style.textContent = `
                .flyer-viewer-modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    z-index: 10000;
                    justify-content: center;
                    align-items: center;
                }
                .flyer-viewer-modal.show {
                    display: flex;
                }
                .flyer-viewer-content {
                    width: 90%;
                    max-width: 650px;
                    height: 90vh;
                    max-height: 90vh;
                    display: flex;
                    flex-direction: column;
                    background: #fff;
                    border-radius: 12px;
                    overflow: hidden;
                }
                .flyer-viewer-content .modal-header {
                    flex-shrink: 0;
                    padding: 16px 20px;
                    border-bottom: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .flyer-viewer-content .modal-header h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    max-width: calc(100% - 40px);
                }
                .flyer-viewer-body {
                    flex: 1;
                    overflow: hidden;
                    padding: 0;
                }
                .flyer-scroll-container {
                    width: 100%;
                    height: 100%;
                    overflow-y: auto;
                    overflow-x: hidden;
                    -webkit-overflow-scrolling: touch;
                    display: flex;
                    justify-content: center;
                    align-items: flex-start;
                    background: #f3f4f6;
                    padding: 16px;
                }
                .flyer-image {
                    max-width: 100%;
                    max-height: none;
                    width: auto;
                    height: auto;
                    display: block;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }
                .flyer-viewer-content .modal-footer {
                    flex-shrink: 0;
                    padding: 12px 20px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: flex-end;
                    gap: 8px;
                }
                .product-flyer-btn {
                    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                    color: #fff;
                    border: none;
                    padding: 8px 14px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 12px;
                    font-weight: 600;
                    transition: all 0.2s ease;
                    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .product-flyer-btn::before {
                    content: '📄';
                    font-size: 14px;
                }
                .product-flyer-btn:hover {
                    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
                    transform: translateY(-1px);
                }
                .no-flyer-text {
                    color: #9ca3af;
                    font-size: 12px;
                    font-style: italic;
                    padding: 8px 0;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // 모달 내용 설정
    document.getElementById('flyer-viewer-title').textContent = productName || 'Flyer';
    document.getElementById('flyer-viewer-image').src = flyerUrl;
    document.getElementById('flyer-download-link').href = flyerUrl;

    // 스크롤 위치 초기화
    const scrollContainer = document.getElementById('flyer-scroll-container');
    if (scrollContainer) {
        scrollContainer.scrollTop = 0;
    }

    // 모달 표시
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Flyer 뷰어 모달 닫기
function closeFlyerViewerModal() {
    const modal = document.getElementById('flyer-viewer-modal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Detail 뷰어 모달 열기 (360x10400 세로 긴 이미지에 최적화)
function openDetailViewerModal(detailUrl, productName) {
    // 모달이 없으면 동적 생성
    let modal = document.getElementById('detail-viewer-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'detail-viewer-modal';
        modal.className = 'modal detail-viewer-modal';
        modal.innerHTML = `
            <div class="modal-content detail-viewer-content">
                <div class="modal-header">
                    <h3 id="detail-viewer-title">Detail</h3>
                    <button type="button" class="modal-close" onclick="closeDetailViewerModal()">
                        <img src="../image/button-close2.svg" alt="Close">
                    </button>
                </div>
                <div class="modal-body detail-viewer-body">
                    <div class="detail-scroll-container" id="detail-scroll-container">
                        <img id="detail-viewer-image" src="" alt="Detail" class="detail-image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="jw-button typeD" onclick="closeDetailViewerModal()">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // 모달 스타일 추가 (한 번만)
        if (!document.getElementById('detail-viewer-styles')) {
            const style = document.createElement('style');
            style.id = 'detail-viewer-styles';
            style.textContent = `
                .detail-viewer-modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    z-index: 10000;
                    justify-content: center;
                    align-items: center;
                }
                .detail-viewer-modal.show {
                    display: flex;
                }
                .detail-viewer-content {
                    width: 90%;
                    max-width: 420px;
                    height: 95vh;
                    max-height: 95vh;
                    display: flex;
                    flex-direction: column;
                    background: #fff;
                    border-radius: 12px;
                    overflow: hidden;
                }
                .detail-viewer-content .modal-header {
                    flex-shrink: 0;
                    padding: 16px 20px;
                    border-bottom: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .detail-viewer-content .modal-header h3 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 600;
                    color: #111827;
                }
                .detail-viewer-body {
                    flex: 1;
                    overflow: hidden;
                    padding: 0;
                }
                .detail-scroll-container {
                    width: 100%;
                    height: 100%;
                    overflow-y: auto;
                    overflow-x: hidden;
                    -webkit-overflow-scrolling: touch;
                }
                .detail-image {
                    width: 100%;
                    height: auto;
                    display: block;
                }
                .detail-viewer-content .modal-footer {
                    flex-shrink: 0;
                    padding: 16px 20px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: flex-end;
                    gap: 12px;
                }
                .product-detail-btn {
                    padding: 8px 14px;
                    font-size: 12px;
                    font-weight: 600;
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    white-space: nowrap;
                    transition: all 0.2s ease;
                    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .product-detail-btn::before {
                    content: '📋';
                    font-size: 14px;
                }
                .product-detail-btn:hover {
                    background: linear-gradient(135deg, #059669 0%, #047857 100%);
                    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
                    transform: translateY(-1px);
                }
                .no-detail-text {
                    font-size: 12px;
                    color: #9ca3af;
                    font-style: italic;
                    padding: 8px 0;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // 모달 내용 업데이트
    document.getElementById('detail-viewer-title').textContent = `Detail - ${productName}`;
    document.getElementById('detail-viewer-image').src = detailUrl;

    // 스크롤 위치 초기화
    const scrollContainer = document.getElementById('detail-scroll-container');
    if (scrollContainer) {
        scrollContainer.scrollTop = 0;
    }

    // 모달 표시
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Detail 뷰어 모달 닫기
function closeDetailViewerModal() {
    const modal = document.getElementById('detail-viewer-modal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// 상품 검색 모달 열기
function openProductSearchModal() {
    selectedProductInModal = null;
    document.getElementById('product-search-input').value = '';
    document.getElementById('product-search-results').innerHTML = '';
    openModal('product-search-modal');
    // 모달이 열릴 때 전체 상품 목록 자동 로드
    loadProductList();
}

// 상품 검색
async function searchProducts() {
    const searchInput = document.getElementById('product-search-input');
    const searchTerm = searchInput.value.trim();
    // 검색어가 없으면 전체 목록 로드
    loadProductList(searchTerm);
}

// 상품 목록 로드 (검색어 옵션)
async function loadProductList(searchTerm = '') {
    const resultsContainer = document.getElementById('product-search-results');

    try {
        resultsContainer.innerHTML = `<div class="is-center">${getText('loading')}</div>`;

        let apiUrl = `../../backend/api/packages.php?limit=50`;
        if (searchTerm) {
            apiUrl += `&search=${encodeURIComponent(searchTerm)}`;
        }

        const response = await fetch(apiUrl);
        const responseText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
        }
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}`);
        }

        if (result.success && result.data && result.data.length > 0) {
            let html = '<div class="product-list">';
            result.data.forEach(pkg => {
                const hasItinerary = pkg.itineraryFile && pkg.itineraryFile.path;
                const itineraryUrl = hasItinerary ? `../${pkg.itineraryFile.path}` : '';
                const hasFlyer = pkg.flyerFile && pkg.flyerFile.path;
                const flyerUrl = hasFlyer ? `../${pkg.flyerFile.path}` : '';
                const hasDetail = pkg.detailFile && pkg.detailFile.path;
                const detailUrl = hasDetail ? `../${pkg.detailFile.path}` : '';

                html += `
                    <div class="product-item" data-package-id="${pkg.packageId}" onclick="selectProductInModal(${pkg.packageId})">
                        <div class="product-name">${escapeHtml(pkg.packageName || '')}</div>
                        <div class="product-price">₱${formatCurrency(pkg.packagePrice || 0)}</div>
                        <div class="product-actions">
                            ${hasFlyer ? `<button type="button" class="product-flyer-btn" onclick="event.stopPropagation(); openFlyerViewerModal('${flyerUrl}', '${escapeHtml(pkg.packageName || '')}');">View Flyer</button>` : '<span class="no-flyer-text">No Flyer</span>'}
                            ${hasDetail ? `<button type="button" class="product-detail-btn" onclick="event.stopPropagation(); openDetailViewerModal('${detailUrl}', '${escapeHtml(pkg.packageName || '')}');">View Detail</button>` : '<span class="no-detail-text">No Detail</span>'}
                            ${hasItinerary ? `<a href="${itineraryUrl}" target="_blank" class="product-download-btn" onclick="event.stopPropagation();" download>Download Itinerary</a>` : ''}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            resultsContainer.innerHTML = html;
        } else {
            resultsContainer.innerHTML = `<div class="is-center">${getText('noResults')}</div>`;
        }
    } catch (error) {
        console.error('Error loading products:', error);
        resultsContainer.innerHTML = `<div class="is-center">${getText('errorOccurred')}</div>`;
    }
}

// 모달에서 상품 선택
window.selectProductInModal = function(packageId) {
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
};

// 상품 선택 확인
function confirmProductSelection() {
    if (!selectedProductInModal) {
        alert(getText('pleaseSelectProduct'));
        return;
    }
    
    // 상품 변경 감지 및 여행 시작일 초기화
    if (previousPackageId !== null && previousPackageId !== selectedProductInModal) {
        const departureDateInput = document.getElementById('departure_date');
        const departureDateValueInput = document.getElementById('departure_date_value');
        const departureDateBtn = document.getElementById('departure_date_btn');
        const returnDateInput = document.getElementById('return_date');
        if (departureDateInput) {
            departureDateInput.value = '';
            departureDateInput.setAttribute('readonly', 'readonly');
            departureDateInput.disabled = true;
        }
        if (departureDateValueInput) {
            departureDateValueInput.value = '';
        }
        if (departureDateBtn) {
            departureDateBtn.disabled = true;
        }
        if (returnDateInput) {
            returnDateInput.value = '';
            returnDateInput.disabled = true;
        }
        selectedDateInfo = null;
        selectedDateInCalendar = null;
        availableDates = [];
        availableDatesByMonth = {};
        
        // 항공편 정보 섹션 제거
        removeFlightInfoSection();
    }
    
    previousPackageId = selectedProductInModal;
    
    // 상품 정보 로드
    loadProductDetail(selectedProductInModal);
    closeModal('product-search-modal');
}

// 상품 상세 정보 로드
async function loadProductDetail(packageId) {
    try {
        const apiUrl = `../../backend/api/packages.php?id=${encodeURIComponent(packageId)}`;
        const response = await fetch(apiUrl);
        const responseText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
        }
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}`);
        }
        
        if (result.success && result.data) {
            const pkg = result.data;
            selectedPackage = pkg;
            
            // 상품명 표시
            document.getElementById('product_name').value = pkg.packageName || '';
            document.getElementById('package_id').value = pkg.packageId || '';
            
            // 여행 시작일 입력 활성화
            const departureDateInput = document.getElementById('departure_date');
            const departureDateBtn = document.getElementById('departure_date_btn');
            departureDateInput.disabled = false;
            departureDateInput.removeAttribute('readonly');
            if (departureDateBtn) {
                departureDateBtn.disabled = false;
            }
            
            // 날짜별 가용성 확인 및 불러오기
            await loadAvailableDates(packageId);
            
            // 총 금액 계산
            calculateTotalAmount();
        } else {
            alert(getText('failedToLoadProduct'));
        }
    } catch (error) {
        console.error('Error loading product detail:', error);
        alert(getText('errorLoadingProduct'));
    }
}
// 🔍 앞으로 N개월 중 "예약 가능 날짜가 있는 첫 번째 월" 찾기
async function findFirstAvailableMonth(packageId, startYear, startMonth, monthsToSearch = 12) {
    for (let i = 0; i < monthsToSearch; i++) {
        const year  = startYear + Math.floor((startMonth - 1 + i) / 12);
        const month = ((startMonth - 1 + i) % 12) + 1;

        const url = `../../backend/api/product_availability.php`
            + `?id=${encodeURIComponent(packageId)}&year=${year}&month=${month}`;

        try {
            const res   = await fetch(url);
            const json  = await res.json();

            if (!json.success || !json.data || !Array.isArray(json.data.availability)) {
                continue;
            }

            const hasOpen = json.data.availability.some(item =>
                item &&
                item.status === 'available' &&
                Number(item.remainingSeats) > 0
            );

            if (hasOpen) {
                // ✅ 이 달로 시작하면 됨
                return { year, month };
            }
        } catch (e) {
            console.error('findFirstAvailableMonth error:', e);
            // 에러 난 달은 그냥 건너뛰고 다음 달로
        }
    }

    // 12개월 안에 하나도 없으면 null 리턴 → 그냥 오늘 기준 월 사용
    return null;
}

// 날짜별 가용성 불러오기 (여러 월 지원)
async function loadAvailableDates(packageId, year = null, month = null) {
    try {
        const today = new Date();
        const targetYear = year || today.getFullYear();
        const targetMonth = month || today.getMonth() + 1;
        const cacheKey = `${targetYear}-${targetMonth}`;

        // ✅ 여기서 월/년도 텍스트 업데이트
        const monthLabelEl = document.querySelector('.availability-header .month-label');
        if (monthLabelEl) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            monthLabelEl.textContent = `${monthNames[targetMonth - 1]} ${targetYear}`;
        }

        
        // 이미 로드된 월이면 캐시에서 반환
        if (availableDatesByMonth[cacheKey]) {
            return availableDatesByMonth[cacheKey];
        }
        
        // product_availability.php API 호출
        const availabilityUrl = `../../backend/api/product_availability.php?id=${encodeURIComponent(packageId)}&year=${targetYear}&month=${targetMonth}`;
        const response = await fetch(availabilityUrl);
        const responseText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
        }
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}`);
        }
        
        if (result.success && result.data && result.data.availability) {
            const dates = result.data.availability.filter(date => 
                date.status === 'available' && date.remainingSeats > 0
            );
            
            // 캐시에 저장
            availableDatesByMonth[cacheKey] = dates;
            
            // 현재 월이면 전역 변수에도 저장
            if (targetYear === calendarCurrentYear && targetMonth === calendarCurrentMonth) {
                availableDates = dates;
            }
            
            console.log(`Available dates loaded for ${targetYear}-${targetMonth}:`, dates);
            return dates;
        } else {
            console.warn('Failed to load available dates:', result);
            availableDatesByMonth[cacheKey] = [];
            return [];
        }
    } catch (error) {
        console.error('Error loading available dates:', error);
        const cacheKey = `${year || calendarCurrentYear}-${month || calendarCurrentMonth}`;
        availableDatesByMonth[cacheKey] = [];
        return [];
    }
}

// 날짜 선택 모달 열기
async function openDatePickerModal() {
    if (!selectedPackage || !selectedPackage.packageId) {
        alert(getText('pleaseSelectProduct') || '상품을 먼저 선택해주세요.');
        return;
    }

    // 기준은 오늘
    const today = new Date();
    let year  = today.getFullYear();
    let month = today.getMonth() + 1;

    try {
        // 🔍 앞으로 12개월 중 "예약 가능한 첫 번째 달" 찾기
        const found = await findFirstAvailableMonth(
            selectedPackage.packageId,
            year,
            month,
            12 // 찾을 개월 수
        );

        if (found) {
            year  = found.year;
            month = found.month;
        }
    } catch (e) {
        console.error('openDatePickerModal init error:', e);
        // 에러 나면 그냥 오늘 기준 월로 둠
    }

    // 전역 캘린더 상태를 "시작 달"로 세팅
    calendarCurrentYear  = year;
    calendarCurrentMonth = month;

    // 그 달 기준으로 캘린더 렌더링
    await renderCalendar();

    // 모달 열기
    openModal('date-picker-modal');
}


// 캘린더 렌더링
async function renderCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    const monthDisplay = document.getElementById('calendar-month-display');
    
    if (!calendarBody || !selectedPackage) return;
    
    // 월 표시 업데이트
    const monthNames = getCurrentLang() === 'eng' 
        ? ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'];
    
    if (monthDisplay) {
        monthDisplay.textContent = `${monthNames[calendarCurrentMonth - 1]} ${calendarCurrentYear}`;
    }
    
    // 해당 월의 가용 가능한 날짜 로드
    await loadAvailableDates(selectedPackage.packageId, calendarCurrentYear, calendarCurrentMonth);
    const monthDates = availableDatesByMonth[`${calendarCurrentYear}-${calendarCurrentMonth}`] || [];
    
    // 가용 가능한 날짜 맵 생성
    const availabilityMap = {};
    monthDates.forEach(date => {
        const dateObj = new Date(date.availableDate);
        const day = dateObj.getDate();
        availabilityMap[day] = date;
    });
    
    // 캘린더 생성
    const firstDay = new Date(calendarCurrentYear, calendarCurrentMonth - 1, 1).getDay();
    const daysInMonth = new Date(calendarCurrentYear, calendarCurrentMonth, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    let calendarHtml = '';
    let date = 1;
    
    for (let week = 0; week < 6; week++) {
        calendarHtml += '<tr>';
        
        for (let day = 0; day < 7; day++) {
            if (week === 0 && day < firstDay) {
                calendarHtml += '<td class="inactive"></td>';
            } else if (date > daysInMonth) {
                calendarHtml += '<td class="inactive"></td>';
            } else {
                const currentDate = new Date(calendarCurrentYear, calendarCurrentMonth - 1, date);
                currentDate.setHours(0, 0, 0, 0);
                //const dateStr = currentDate.toISOString().split('T')[0];
                // SMT 수정 시작
                const dateStr = `${calendarCurrentYear}-${String(calendarCurrentMonth).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                // SMT 수정 종료
                const isPast = currentDate < today;
                const availabilityInfo = availabilityMap[date];
                const isSelected = selectedDateInCalendar === dateStr;
                
                let cellClass = '';
                let cellContent = date;
                let clickEvent = '';
                
                if (isPast) {
                    cellClass = 'inactive';
                } else if (availabilityInfo && availabilityInfo.remainingSeats > 0) {
                    cellClass = 'available';
                    const price = Math.floor(availabilityInfo.price / 1000);
                    cellContent = `
                        ${date}
                        <p class="text fz12 fw400 lh16">₱${price}K</p>
                    `;
                    clickEvent = `onclick="selectDateInCalendar('${dateStr}', ${availabilityInfo.availabilityId})"`;
                } else {
                    cellClass = 'inactive';
                }
                
                if (isSelected) {
                    cellClass += ' selected';
                }
                
                if (currentDate.getTime() === today.getTime()) {
                    cellClass += ' today';
                }
                
                calendarHtml += `<td class="${cellClass.trim()}" ${clickEvent} role="gridcell" tabindex="0">${cellContent}</td>`;
                date++;
            }
        }
        
        calendarHtml += '</tr>';
        
        if (date > daysInMonth) break;
    }
    
    calendarBody.innerHTML = calendarHtml;
    
    // 다국어 적용
    if (typeof language_apply === 'function') {
        const currentLang = getCurrentLang();
        language_apply(currentLang);
    }
}

// 캘린더에서 날짜 선택
window.selectDateInCalendar = function(dateStr, availabilityId) {
    selectedDateInCalendar = dateStr;
    
    // 선택된 날짜 하이라이트
    document.querySelectorAll('#calendar-body td').forEach(td => {
        td.classList.remove('selected');
    });
    
    const selectedCell = Array.from(document.querySelectorAll('#calendar-body td')).find(td => {
        return td.getAttribute('onclick') && td.getAttribute('onclick').includes(dateStr);
    });
    
    if (selectedCell) {
        selectedCell.classList.add('selected');
    }
    
    // 선택된 날짜 정보 저장
    const monthDates = availableDatesByMonth[`${calendarCurrentYear}-${calendarCurrentMonth}`] || [];
    selectedDateInfo = monthDates.find(date => date.availableDate === dateStr);
    
    // 날짜 정보 표시
    updateCalendarInfo();
};

// 캘린더 정보 업데이트
function updateCalendarInfo() {
    const calendarInfo = document.getElementById('calendar-info');
    if (!calendarInfo || !selectedDateInfo) {
        if (calendarInfo) calendarInfo.innerHTML = '';
        return;
    }
    
    const date = new Date(selectedDateInfo.availableDate);
    const formattedDate = `${date.getFullYear()}. ${date.getMonth() + 1}. ${date.getDate()}`;
    const price = formatCurrency(selectedDateInfo.price);
    const remainingSeats = selectedDateInfo.remainingSeats;
    
    calendarInfo.innerHTML = `
        <div class="calendar-info-item">
            <strong>Date:</strong> ${formattedDate}
        </div>
        <div class="calendar-info-item">
            <strong>Price:</strong> ₱${price}
        </div>
        <div class="calendar-info-item">
            <strong>RemainingSeats:</strong> ${remainingSeats}
        </div>
    `;
}

// 날짜 선택 확인
async function confirmDateSelection() {
    if (!selectedDateInCalendar) {
        alert(getText('pleaseSelectDate') || '날짜를 선택해주세요.');
        return;
    }
    
    // selectedDateInfo가 없으면 가용 날짜 목록에서 찾기
    if (!selectedDateInfo) {
        const monthDates = availableDatesByMonth[`${calendarCurrentYear}-${calendarCurrentMonth}`] || [];
        selectedDateInfo = monthDates.find(date => date.availableDate === selectedDateInCalendar);
        
        // 그래도 없으면 다른 월의 가용 날짜에서 찾기
        if (!selectedDateInfo) {
            for (const [key, dates] of Object.entries(availableDatesByMonth)) {
                const found = dates.find(date => date.availableDate === selectedDateInCalendar);
                if (found) {
                    selectedDateInfo = found;
                    break;
                }
            }
        }
    }
    
    // 날짜 입력 필드 업데이트
    const departureDateInput = document.getElementById('departure_date');
    const departureDateValueInput = document.getElementById('departure_date_value');
    const date = new Date(selectedDateInCalendar);
    
    const formattedDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const displayDate = getCurrentLang() === 'eng' 
        ? date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : `${date.getFullYear()}년 ${date.getMonth() + 1}월 ${date.getDate()}일`;
    
    if (departureDateInput) {
        departureDateInput.value = displayDate;
    }
    if (departureDateValueInput) {
        departureDateValueInput.value = formattedDate;
    }    
    
    // 여행 종료일 계산
    updateReturnDate();
    
    // 선택한 날짜의 상세 정보 불러오기
    await loadDateDetailInfo(selectedPackage.packageId, formattedDate);
    
    // 모달 닫기
    closeModal('date-picker-modal');
    
    // 총 금액 계산
    calculateTotalAmount();
}

// 예약 생성일(오늘) 기준으로 선금 입금 기한(+3일) 자동 설정
function updateDepositDueFromCreatedDate() {
    const depositDueInput = document.getElementById('deposit_due');
    if (!depositDueInput) return;

    const created = new Date(); // 현재 시점 = 예약 생성일

    // 예약일 기준 +3일
    const due = new Date(created);
    due.setDate(due.getDate() + 3);

    const yyyy = due.getFullYear();
    const mm = String(due.getMonth() + 1).padStart(2, '0');
    const dd = String(due.getDate()).padStart(2, '0');

    depositDueInput.value = `${yyyy}-${mm}-${dd}`;
}

// 선택한 날짜의 상세 정보 불러오기 (여행 기간, 미팅 시간, 미팅 장소)
async function loadDateDetailInfo(packageId, date) {
    try {
        // 패키지 상세 정보에서 미팅 정보 가져오기
        const detailUrl = `../../backend/api/packages.php?id=${encodeURIComponent(packageId)}`;
        const response = await fetch(detailUrl);
        const responseText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
        }
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}`);
        }
        
        if (result.success && result.data) {
            const pkg = result.data;
            
            // 미팅 시간 및 장소 정보 표시 (필요시 UI에 추가)
            if (pkg.meeting_time || pkg.meeting_location) {
                console.log('Meeting info:', {
                    time: pkg.meeting_time,
                    location: pkg.meeting_location
                });
                // TODO: UI에 미팅 정보 표시 (필요시 섹션 추가)
            }
            
            // 항공편 정보 확인 및 표시
            if (selectedDateInfo && selectedDateInfo.flightId) {
                await loadFlightInfo(selectedDateInfo.flightId);
            }
        }
    } catch (error) {
        console.error('Error loading date detail info:', error);
    }
}

// 항공편 정보 불러오기
async function loadFlightInfo(flightId) {
    try {
        // agent-api.php의 getFlightInfo 사용
        await fetchFlightDetails(flightId);
    } catch (error) {
        console.error('Error loading flight info:', error);
    }
}

// 항공편 상세 정보 조회
async function fetchFlightDetails(flightId) {
    try {
        // agent-api.php에 항공편 조회 API 추가 필요
        // 임시로 product_availability.php의 응답에서 flight 정보 확인
        const response = await fetch(`../backend/api/agent-api.php?action=getFlightInfo&flightId=${flightId}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            renderFlightInfoSection(result.data);
        } else {
            console.warn('Flight info not available from API, using date info');
            // API가 없으면 날짜 정보에서 추출 가능한 정보만 사용
            if (selectedDateInfo) {
                renderFlightInfoSectionFromDateInfo(selectedDateInfo);
            }
        }
    } catch (error) {
        console.error('Error fetching flight details:', error);
        // API 호출 실패 시 날짜 정보에서 추출 가능한 정보만 사용
        if (selectedDateInfo) {
            renderFlightInfoSectionFromDateInfo(selectedDateInfo);
        }
    }
}

// 날짜 정보에서 항공편 정보 섹션 렌더링 (임시)
function renderFlightInfoSectionFromDateInfo(dateInfo) {
    // 날짜 정보에는 제한적인 항공편 정보만 포함되므로,
    // 기본 정보만 표시하거나 API가 구현될 때까지 대기
    console.log('Flight info from date info:', dateInfo);
}

// 항공편 정보 섹션 렌더링
function renderFlightInfoSection(flight) {
    // 기존 항공편 정보 섹션 제거
    removeFlightInfoSection();
    
    // 항공편 정보 섹션 추가
    const productInfoSection = document.querySelector('.card-panel.jw-mgt16');
    if (!productInfoSection) return;
    
    const flightSection = document.createElement('div');
    flightSection.id = 'flight-info-section';
    flightSection.className = 'card-panel jw-mgt16';
    flightSection.innerHTML = `
        <h2 class="section-title" data-lan-eng="Flight Information">항공편 정보</h2>
        <div class="grid-wrap">
            <div class="grid-item">
                <label class="label-name" data-lan-eng="Departure">출발</label>
                <div>
                    <div>${escapeHtml(flight.origin || '')}</div>
                    <div>${escapeHtml(flight.flightName || '')} ${escapeHtml(flight.flightCode || '')}</div>
                    <div>${formatDate(flight.flightDepartureDate)} ${flight.flightDepartureTime || ''}</div>
                </div>
            </div>
            <div class="grid-item">
                <label class="label-name" data-lan-eng="Arrival">도착</label>
                <div>
                    <div>${escapeHtml(flight.destination || '')}</div>
                    <div>${escapeHtml(flight.returnFlightName || '')} ${escapeHtml(flight.returnFlightCode || '')}</div>
                    <div>${formatDate(flight.flightArrivalDate)} ${flight.flightArrivalTime || ''}</div>
                </div>
            </div>
            <div class="grid-item">
                <label class="label-name" data-lan-eng="Return Departure">귀국 출발</label>
                <div>
                    <div>${escapeHtml(flight.returnOrigin || '')}</div>
                    <div>${formatDate(flight.returnDepartureDate)} ${flight.returnDepartureTime || ''}</div>
                </div>
            </div>
            <div class="grid-item">
                <label class="label-name" data-lan-eng="Return Arrival">귀국 도착</label>
                <div>
                    <div>${escapeHtml(flight.returnDestination || '')}</div>
                    <div>${formatDate(flight.returnArrivalDate)} ${flight.returnArrivalTime || ''}</div>
                </div>
            </div>
        </div>
    `;
    
    // 상품 정보 섹션 다음에 추가
    productInfoSection.parentNode.insertBefore(flightSection, productInfoSection.nextSibling);
    
    // 다국어 적용
    if (typeof language_apply === 'function') {
        const currentLang = getCurrentLang();
        language_apply(currentLang);
    }
}

// 항공편 정보 섹션 제거
function removeFlightInfoSection() {
    const flightSection = document.getElementById('flight-info-section');
    if (flightSection) {
        flightSection.remove();
    }
}

// 고객 검색 모달 열기
function openCustomerSearchModal() {
    selectedCustomerInModal = null;
    document.getElementById('customer-search-input').value = '';
    searchCustomers(); // 초기 로드
    openModal('customer-search-modal');
}

// 고객 검색
let currentCustomerPage = 1;
const customerLimit = 20;

async function searchCustomers(page = 1) {
    const searchInput = document.getElementById('customer-search-input');
    const searchTerm = searchInput.value.trim();
    const resultsContainer = document.getElementById('customer-search-results');
    
    currentCustomerPage = page;
    
    try {
        resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('searching')}</td></tr>`;
        
        const params = new URLSearchParams({
            action: 'getCustomers',
            page: page,
            limit: customerLimit
        });
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }
        
        const response = await fetch(`../backend/api/agent-api.php?${params.toString()}`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.customers && result.data.customers.length > 0) {
            let html = '';
            result.data.customers.forEach(customer => {
                const fullName = `${customer.fName || ''} ${customer.lName || ''}`.trim();
                html += `
                    <tr onclick="selectCustomerInModal(${customer.accountId})">
                        <td class="is-center">
                            <input type="radio" name="customer_select" value="${customer.accountId}">
                        </td>
                        <td>${escapeHtml(fullName)}</td>
                        <td class="is-center">${escapeHtml(customer.gender === 'male' ? getText('male') : customer.gender === 'female' ? getText('female') : '')}</td>
                        <td class="is-center">${customer.dateOfBirth ? formatDate(customer.dateOfBirth) : '-'}</td>
                        <td>${escapeHtml(customer.contactNo || '-')}</td>
                        <td>${escapeHtml(customer.emailAddress || '-')}</td>
                        <td class="is-center">${escapeHtml(customer.nationality || '-')}</td>
                        <td>${escapeHtml(customer.passportNumber || '-')}</td>
                        <td class="is-center">${customer.passportExpiry ? formatDate(customer.passportExpiry) : '-'}</td>
                    </tr>
                `;
            });
            resultsContainer.innerHTML = html;
            
            // 페이지네이션 렌더링
            renderCustomerPagination(result.data.pagination);
        } else {
            resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('noResults')}</td></tr>`;
            document.getElementById('customer-pagination').innerHTML = '';
        }
    } catch (error) {
        console.error('Error searching customers:', error);
        resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('errorOccurred')}</td></tr>`;
    }
}

// 페이지네이션 렌더링
function renderCustomerPagination(pagination) {
    const container = document.getElementById('customer-pagination');
    if (!pagination || pagination.totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<div class="contents">';
    
    // 첫 페이지
    html += `<button type="button" class="first" ${currentCustomerPage === 1 ? 'aria-disabled="true"' : ''} onclick="searchCustomers(1)"><img src="../image/first.svg" alt=""></button>`;
    
    // 이전 페이지
    html += `<button type="button" class="prev" ${currentCustomerPage === 1 ? 'aria-disabled="true"' : ''} onclick="searchCustomers(${currentCustomerPage - 1})"><img src="../image/prev.svg" alt=""></button>`;
    
    // 페이지 번호
    html += '<div class="page" role="list">';
    const startPage = Math.max(1, currentCustomerPage - 2);
    const endPage = Math.min(pagination.totalPages, currentCustomerPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<button type="button" class="p ${i === currentCustomerPage ? 'show' : ''}" role="listitem" onclick="searchCustomers(${i})">${i}</button>`;
    }
    html += '</div>';
    
    // 다음 페이지
    html += `<button type="button" class="next" ${currentCustomerPage === pagination.totalPages ? 'aria-disabled="true"' : ''} onclick="searchCustomers(${currentCustomerPage + 1})"><img src="../image/next.svg" alt=""></button>`;
    
    // 마지막 페이지
    html += `<button type="button" class="last" ${currentCustomerPage === pagination.totalPages ? 'aria-disabled="true"' : ''} onclick="searchCustomers(${pagination.totalPages})"><img src="../image/last.svg" alt=""></button>`;
    
    html += '</div>';
    container.innerHTML = html;
}

// 모달에서 고객 선택
window.selectCustomerInModal = function(accountId) {
    // 라디오 버튼 업데이트
    document.querySelectorAll('input[name="customer_select"]').forEach(radio => {
        radio.checked = (radio.value == accountId);
    });
    
    selectedCustomerInModal = accountId;
};

// 고객 선택 확인
async function confirmCustomerSelection() {
    const selectedRadio = document.querySelector('input[name="customer_select"]:checked');
    if (!selectedRadio) {
        alert(getText('pleaseSelectCustomer'));
        return;
    }

    const accountId = selectedRadio.value;

    try {
        const response = await fetch(`../backend/api/agent-api.php?action=getCustomerDetail&accountId=${accountId}`);
        const result = await response.json();

        if (result.success && result.data && result.data.customer) {
            const customer = result.data.customer;
            selectedCustomer = customer;

            // 고객 정보 표시
            document.getElementById('user_name').value = `${customer.fName || ''} ${customer.lName || ''}`.trim();
            document.getElementById('user_email').value = customer.accountEmail || customer.emailAddress || '';
            document.getElementById('user_phone').value = customer.contactNo || '';
            document.getElementById('country_code').value = customer.countryCode || '+63';
            document.getElementById('customer_account_id').value = customer.accountId || '';

            // 이미 같은 고객이 travelers에 있는지 확인
            const existingIndex = travelers.findIndex(t =>
                (t.accountId && customer.accountId && t.accountId == customer.accountId) ||
                (t.firstName === customer.fName && t.lastName === customer.lName && customer.fName && customer.lName)
            );

            if (existingIndex >= 0) {
                // 이미 있으면 해당 traveler를 대표 여행자로 설정
                travelers.forEach((t, i) => {
                    t.isMainTraveler = (i === existingIndex);
                });
                // 정보 업데이트
                const existingTraveler = travelers[existingIndex];
                existingTraveler.firstName = customer.fName || '';
                existingTraveler.lastName = customer.lName || '';
                existingTraveler.gender = customer.gender || 'male';
                existingTraveler.age = customer.dateOfBirth ? calculateAge(customer.dateOfBirth) : '';
                existingTraveler.birthDate = customer.dateOfBirth || '';
                existingTraveler.email = customer.accountEmail || customer.emailAddress || '';
                existingTraveler.contact = customer.contactNo || '';
                existingTraveler.nationality = customer.nationality || '';
                existingTraveler.passportNumber = customer.passportNumber || '';
                existingTraveler.passportIssueDate = customer.passportIssueDate || '';
                existingTraveler.passportExpiry = customer.passportExpiry || '';
                existingTraveler.passportImage = customer.profileImage || '';
                existingTraveler.accountId = customer.accountId || null;
                updateTravelerRow(existingIndex);
            } else {
                // 여행자 정보에 대표 여행자로 추가
                if (travelers.length === 0) {
                    addTraveler();
                }

                // 첫 번째 여행자 정보 채우기
                if (travelers.length > 0) {
                    const firstTraveler = travelers[0];
                    firstTraveler.firstName = customer.fName || '';
                    firstTraveler.lastName = customer.lName || '';
                    firstTraveler.gender = customer.gender || 'male';
                    firstTraveler.age = customer.dateOfBirth ? calculateAge(customer.dateOfBirth) : '';
                    firstTraveler.birthDate = customer.dateOfBirth || '';
                    firstTraveler.email = customer.accountEmail || customer.emailAddress || '';
                    firstTraveler.contact = customer.contactNo || '';
                    firstTraveler.nationality = customer.nationality || '';
                    firstTraveler.passportNumber = customer.passportNumber || '';
                    firstTraveler.passportIssueDate = customer.passportIssueDate || '';
                    firstTraveler.passportExpiry = customer.passportExpiry || '';
                    firstTraveler.passportImage = customer.profileImage || '';
                    firstTraveler.accountId = customer.accountId || null;
                    firstTraveler.isMainTraveler = true;

                    updateTravelerRow(0);
                }
            }

            closeModal('customer-search-modal');
        } else {
            alert(getText('failedToLoadCustomer'));
        }
    } catch (error) {
        console.error('Error loading customer detail:', error);
        alert(getText('errorLoadingCustomer'));
    }
}

// 여행 고객 검색 모달 열기
function openTravelCustomerSearchModal() {
    document.getElementById('travel-customer-search-input').value = '';
    searchTravelCustomers(1); // 초기 로드
    openModal('travel-customer-search-modal');
}

// 여행 고객 검색
let currentTravelCustomerPage = 1;
const travelCustomerLimit = 20;

async function searchTravelCustomers(page = 1) {
    const searchInput = document.getElementById('travel-customer-search-input');
    const searchTerm = searchInput.value.trim();
    const resultsContainer = document.getElementById('travel-customer-search-results');
    
    currentTravelCustomerPage = page;
    
    try {
        resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('searching')}</td></tr>`;
        
        const params = new URLSearchParams({
            action: 'getCustomers',
            page: page,
            limit: travelCustomerLimit
        });
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }
        
        const response = await fetch(`../backend/api/agent-api.php?${params.toString()}`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.customers && result.data.customers.length > 0) {
            let html = '';
            result.data.customers.forEach(customer => {
                const fullName = `${customer.fName || ''} ${customer.lName || ''}`.trim();
                html += `
                    <tr>
                        <td class="is-center">
                            <input type="checkbox" name="travel_customer_select" value="${customer.accountId}" class="travel-customer-checkbox">
                        </td>
                        <td>${escapeHtml(fullName)}</td>
                        <td class="is-center">${escapeHtml(customer.gender === 'male' ? getText('male') : customer.gender === 'female' ? getText('female') : '')}</td>
                        <td class="is-center">${customer.dateOfBirth ? formatDate(customer.dateOfBirth) : '-'}</td>
                        <td>${escapeHtml(customer.contactNo || '-')}</td>
                        <td>${escapeHtml(customer.emailAddress || '-')}</td>
                        <td class="is-center">${escapeHtml(customer.nationality || '-')}</td>
                        <td>${escapeHtml(customer.passportNumber || '-')}</td>
                        <td class="is-center">${customer.passportExpiry ? formatDate(customer.passportExpiry) : '-'}</td>
                    </tr>
                `;
            });
            resultsContainer.innerHTML = html;
            
            // 페이지네이션 렌더링
            renderTravelCustomerPagination(result.data.pagination);
        } else {
            resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('noResults')}</td></tr>`;
            document.getElementById('travel-customer-pagination').innerHTML = '';
        }
    } catch (error) {
        console.error('Error searching travel customers:', error);
        resultsContainer.innerHTML = `<tr><td colspan="9" class="is-center">${getText('errorLoadingCustomer')}</td></tr>`;
        document.getElementById('travel-customer-pagination').innerHTML = '';
    }
}

// 여행 고객 페이지네이션 렌더링
function renderTravelCustomerPagination(pagination) {
    const paginationContainer = document.getElementById('travel-customer-pagination');
    if (!pagination || !paginationContainer) return;
    
    let html = '<div class="contents">';
    
    // 첫 페이지
    html += `<button type="button" class="first" ${pagination.currentPage === 1 ? 'aria-disabled="true"' : ''} onclick="searchTravelCustomers(1)"><img src="../image/first.svg" alt=""></button>`;
    
    // 이전 페이지
    html += `<button type="button" class="prev" ${pagination.currentPage === 1 ? 'aria-disabled="true"' : ''} onclick="searchTravelCustomers(${pagination.currentPage - 1})"><img src="../image/prev.svg" alt=""></button>`;
    
    // 페이지 번호
    html += '<div class="page" role="list">';
    for (let i = pagination.startPage; i <= pagination.endPage; i++) {
        html += `<button type="button" class="p ${i === pagination.currentPage ? 'show' : ''}" role="listitem" ${i === pagination.currentPage ? 'aria-current="page"' : ''} onclick="searchTravelCustomers(${i})">${i}</button>`;
    }
    html += '</div>';
    
    // 다음 페이지
    html += `<button type="button" class="next" ${pagination.currentPage === pagination.totalPages ? 'aria-disabled="true"' : ''} onclick="searchTravelCustomers(${pagination.currentPage + 1})"><img src="../image/next.svg" alt=""></button>`;
    
    // 마지막 페이지
    html += `<button type="button" class="last" ${pagination.currentPage === pagination.totalPages ? 'aria-disabled="true"' : ''} onclick="searchTravelCustomers(${pagination.totalPages})"><img src="../image/last.svg" alt=""></button>`;
    
    html += '</div>';
    paginationContainer.innerHTML = html;
}

// 여행 고객 복수 선택 확인
async function confirmTravelCustomerSelection() {
    const selectedCheckboxes = document.querySelectorAll('input[name="travel_customer_select"]:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert(getText('pleaseSelectCustomer'));
        return;
    }
    
    const selectedAccountIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    try {
        // 선택한 모든 고객 정보 가져오기
        const customerPromises = selectedAccountIds.map(accountId => 
            fetch(`../backend/api/agent-api.php?action=getCustomerDetail&accountId=${accountId}`)
                .then(res => res.json())
        );
        
        const results = await Promise.all(customerPromises);

        // 각 고객을 여행자로 추가
        let skippedCount = 0;
        for (const result of results) {
            if (result.success && result.data && result.data.customer) {
                const customer = result.data.customer;

                // 중복 체크: accountId 또는 이름(firstName + lastName)으로
                const isDuplicate = travelers.some(t =>
                    (t.accountId && customer.accountId && t.accountId == customer.accountId) ||
                    (t.firstName && t.lastName && customer.fName && customer.lName &&
                     t.firstName.toLowerCase() === customer.fName.toLowerCase() &&
                     t.lastName.toLowerCase() === customer.lName.toLowerCase())
                );

                if (isDuplicate) {
                    skippedCount++;
                    continue; // 중복이면 건너뛰기
                }

                // 여행자 추가
                const newTraveler = {
                    index: travelers.length,
                    isMainTraveler: travelers.length === 0, // 첫 번째만 대표 여행자
                    type: 'adult',
                    visaRequired: false,
                    title: customer.gender === 'female' ? 'MRS' : 'MR',
                    firstName: customer.fName || '',
                    lastName: customer.lName || '',
                    gender: customer.gender || 'male',
                    age: customer.dateOfBirth ? calculateAge(customer.dateOfBirth) : '',
                    birthDate: customer.dateOfBirth || '',
                    contact: customer.contactNo || '',
                    email: customer.accountEmail || customer.emailAddress || '',
                    nationality: customer.nationality || '',
                    passportNumber: customer.passportNumber || '',
                    passportIssueDate: customer.passportIssueDate || '',
                    passportExpiry: customer.passportExpiry || '',
                    passportImage: customer.profileImage || '', // Load passport photo from customer profileImage
                    accountId: customer.accountId || null, // Store accountId for saving passport photo back
                    remarks: ''
                };

                travelers.push(newTraveler);
                renderTravelerRow(newTraveler);
            }
        }

        // 중복으로 건너뛴 고객이 있으면 알림
        if (skippedCount > 0) {
            alert(`${skippedCount} customer(s) were skipped as they are already in the traveler list.`);
        }

        // 모달 닫기
        closeModal('travel-customer-search-modal');
        
    } catch (error) {
        console.error('Error loading travel customer details:', error);
        alert(getText('errorLoadingCustomer'));
    }
}

// 여행자 추가
function addTraveler() {
    const tbody = document.getElementById('travelers-tbody');
    if (!tbody) return;

    const newTraveler = {
        index: travelers.length,
        isMainTraveler: travelers.length === 0,
        type: 'adult',
        visaRequired: false,
        title: 'MR',
        firstName: '',
        lastName: '',
        gender: 'male',
        age: '',
        birthDate: '',
        contact: '',
        email: '',
        nationality: '',
        passportNumber: '',
        passportIssueDate: '',
        passportExpiry: '',
        passportImage: '',
        passportImageFile: null,
        accountId: null, // For saving passport photo back to client table
        remarks: ''
    };

    travelers.push(newTraveler);
    renderTravelerRow(newTraveler);
}

// 여행자 행 렌더링
function renderTravelerRow(traveler) {
    const tbody = document.getElementById('travelers-tbody');
    if (!tbody) return;
    
    const row = document.createElement('tr');
    row.id = `traveler-row-${traveler.index}`;
    row.innerHTML = `
        <td class="is-center">${traveler.index + 1}</td>
        <td class="is-center">
            <input type="radio" name="lead_traveler" value="${traveler.index}" ${traveler.isMainTraveler ? 'checked' : ''}>
        </td>
        <td class="show">
            <div class="cell">
                <select class="select traveler-type" disabled>
                    <option value="adult" ${traveler.type === 'adult' ? 'selected' : ''}>${getText('adult')}</option>
                    <option value="child" ${traveler.type === 'child' ? 'selected' : ''}>${getText('child')}</option>
                    <option value="infant" ${traveler.type === 'infant' ? 'selected' : ''}>${getText('infant')}</option>
                </select>
            </div>
        </td>
        <td class="show">
            <div class="cell">
                <select class="select traveler-visa">
                    <option value="0" ${!traveler.visaRequired ? 'selected' : ''}>${getText('visaNotApplied')}</option>
                    <option value="1" ${traveler.visaRequired ? 'selected' : ''}>${getText('visaApplied')}</option>
                </select>
            </div>
        </td>
        <td class="show">
            <div class="cell">
                <select class="select w-auto traveler-title">
                    <option value="MR" ${traveler.title === 'MR' ? 'selected' : ''}>MR</option>
                    <option value="MS" ${traveler.title === 'MS' ? 'selected' : ''}>MS</option>
                    <option value="MRS" ${traveler.title === 'MRS' ? 'selected' : ''}>MRS</option>
                    <option value="MISS" ${traveler.title === 'MISS' ? 'selected' : ''}>MISS</option>
                </select>
            </div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="text" class="form-control traveler-firstname" placeholder="${getText('firstName')}" data-lan-eng-placeholder="${getText('firstName')}" value="${escapeHtml(traveler.firstName || '')}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="text" class="form-control traveler-lastname" placeholder="${getText('lastName')}" data-lan-eng-placeholder="${getText('lastName')}" value="${escapeHtml(traveler.lastName || '')}"></div>
        </td>
        <td class="show">
            <div class="cell">
                <select class="select w-auto traveler-gender">
                    <option value="male" ${traveler.gender === 'male' ? 'selected' : ''}>${getText('male')}</option>
                    <option value="female" ${traveler.gender === 'female' ? 'selected' : ''}>${getText('female')}</option>
                    <option value="other" ${traveler.gender === 'other' ? 'selected' : ''}>${getText('other')}</option>
                </select>
            </div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="number" class="form-control traveler-age" placeholder="${getText('age')}" data-lan-eng-placeholder="${getText('age')}" value="${traveler.age || ''}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="date" class="form-control traveler-birthdate" lang="${getCurrentLang() === 'eng' ? 'en' : 'ko'}" value="${traveler.birthDate ? formatDateForInput(traveler.birthDate) : ''}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="text" class="form-control traveler-nationality" placeholder="${getText('nationality')}" data-lan-eng-placeholder="${getText('nationality')}" value="${escapeHtml(traveler.nationality || '')}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="text" class="form-control traveler-passport" placeholder="${getText('passportNumber')}" data-lan-eng-placeholder="${getText('passportNumber')}" value="${escapeHtml(traveler.passportNumber || '')}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="date" class="form-control traveler-passport-issue" lang="${getCurrentLang() === 'eng' ? 'en' : 'ko'}" value="${traveler.passportIssueDate ? formatDateForInput(traveler.passportIssueDate) : ''}"></div>
        </td>
        <td class="is-center">
            <div class="cell"><input type="date" class="form-control traveler-passport-expiry" lang="${getCurrentLang() === 'eng' ? 'en' : 'ko'}" value="${traveler.passportExpiry ? formatDateForInput(traveler.passportExpiry) : ''}"></div>
        </td>
        <td class="is-center">
            <div class="passport-photo-container" data-index="${traveler.index}">
                ${traveler.passportImage ? `
                    <div class="passport-photo-preview">
                        <img src="${traveler.passportImage}" alt="Passport" style="max-width: 80px; max-height: 80px; border-radius: 4px; object-fit: cover;">
                        <div style="display: flex; gap: 4px; margin-top: 8px; flex-wrap: wrap; justify-content: center;">
                            <button type="button" class="jw-button typeA passport-photo-view" style="font-size: 10px; padding: 3px 6px;">
                                View
                            </button>
                            <button type="button" class="jw-button typeA passport-photo-download" style="font-size: 10px; padding: 3px 6px;">
                                Download
                            </button>
                            <button type="button" class="jw-button typeA passport-photo-delete" style="font-size: 10px; padding: 3px 6px; background: #ef4444; border-color: #ef4444;">
                                Delete
                            </button>
                        </div>
                    </div>
                ` : `
                    <label class="inputFile">
                        <input type="file" class="traveler-passport-photo" accept="image/*" style="display: none;">
                        <button type="button" class="btn-upload passport-photo-upload"><img src="../image/upload3.svg" alt=""> <span data-lan-eng="Image upload">Image upload</span></button>
                    </label>
                `}
            </div>
        </td>
        <td class="is-center">
            <div class="jw-center"><button type="button" class="jw-button traveler-delete" aria-label="row delete" onclick="deleteTraveler(${traveler.index})"><img src="../image/trash.svg" alt=""></button></div>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // 이벤트 리스너 추가
    attachTravelerEventListeners(row, traveler.index);
    
    // 다국어 적용 (select 옵션과 placeholder 업데이트)
    if (typeof language_apply === 'function') {
        const currentLang = getCurrentLang();
        language_apply(currentLang);
    }
    
    // jw_select 재적용
    if (typeof jw_select === 'function') {
        setTimeout(() => {
            jw_select();
        }, 100);
    }
}

function addTravelerWithData(data = {}) {
    addTraveler();
    const idx = travelers.length - 1;
    if (idx < 0) return;
    
    travelers[idx] = {
        ...travelers[idx],
        ...data,
        index: idx
    };
    
    updateTravelerRow(idx);
}

// 여행자 행 업데이트
function updateTravelerRow(index) {
    const traveler = travelers[index];
    if (!traveler) return;
    const row = document.getElementById(`traveler-row-${index}`);
    if (!row) {
        renderTravelerRow(traveler);
        return;
    }
    const firstNameInput = row.querySelector('.traveler-firstname');
    const lastNameInput = row.querySelector('.traveler-lastname');
    const genderSelect = row.querySelector('.traveler-gender');
    const ageInput = row.querySelector('.traveler-age');
    const birthDateInput = row.querySelector('.traveler-birthdate');
    const nationalityInput = row.querySelector('.traveler-nationality');
    const passportInput = row.querySelector('.traveler-passport');
    const passportIssueInput = row.querySelector('.traveler-passport-issue');
    const passportExpiryInput = row.querySelector('.traveler-passport-expiry');
    const titleSelect = row.querySelector('.traveler-title');
    const typeSelect = row.querySelector('.traveler-type');
    const visaSelect = row.querySelector('.traveler-visa');
    const mainTravelerRadio = row.querySelector('input[name="lead_traveler"]');

    if (firstNameInput) {
        firstNameInput.value = traveler.firstName || '';
        firstNameInput.placeholder = getText('firstName');
        firstNameInput.setAttribute('data-lan-eng-placeholder', getText('firstName'));
    }

    if (lastNameInput) {
        lastNameInput.value = traveler.lastName || '';
        lastNameInput.placeholder = getText('lastName');
        lastNameInput.setAttribute('data-lan-eng-placeholder', getText('lastName'));
    }

    if (genderSelect) {
        genderSelect.value = traveler.gender || 'male';
        Array.from(genderSelect.options).forEach(option => {
            if (option.dataset.lanEng) {
                const key = option.value === 'male' ? 'male' : option.value === 'female' ? 'female' : 'other';
                option.textContent = getText(key);
            }
        });
    }

    if (ageInput) {
        ageInput.value = traveler.age || '';
        ageInput.placeholder = getText('age');
        ageInput.setAttribute('data-lan-eng-placeholder', getText('age'));
    }

    if (birthDateInput) {
        birthDateInput.value = traveler.birthDate ? formatDateForInput(traveler.birthDate) : '';
        birthDateInput.setAttribute('lang', getCurrentLang() === 'eng' ? 'en' : 'ko');
    }

    if (nationalityInput) {
        nationalityInput.value = traveler.nationality || '';
        nationalityInput.placeholder = getText('nationality');
        nationalityInput.setAttribute('data-lan-eng-placeholder', getText('nationality'));
    }

    if (passportInput) {
        passportInput.value = traveler.passportNumber || '';
        passportInput.placeholder = getText('passportNumber');
        passportInput.setAttribute('data-lan-eng-placeholder', getText('passportNumber'));
    }

    if (passportIssueInput) {
        passportIssueInput.value = traveler.passportIssueDate ? formatDateForInput(traveler.passportIssueDate) : '';
        passportIssueInput.setAttribute('lang', getCurrentLang() === 'eng' ? 'en' : 'ko');
    }

    if (passportExpiryInput) {
        passportExpiryInput.value = traveler.passportExpiry ? formatDateForInput(traveler.passportExpiry) : '';
        passportExpiryInput.setAttribute('lang', getCurrentLang() === 'eng' ? 'en' : 'ko');
    }

    if (titleSelect) {
        titleSelect.value = traveler.title || 'MR';
    }

    if (typeSelect) {
        typeSelect.value = traveler.type || 'adult';
    }

    if (visaSelect) {
        visaSelect.value = traveler.visaRequired ? '1' : '0';
    }

    if (mainTravelerRadio) {
        mainTravelerRadio.checked = traveler.isMainTraveler || traveler.index === 0;
    }

    // Update passport photo display
    updatePassportPhotoDisplay(index);
}


// 여행자 이벤트 리스너 추가
function attachTravelerEventListeners(row, index) {
    const typeSelect = row.querySelector('.traveler-type');
    const visaSelect = row.querySelector('.traveler-visa');
    const titleSelect = row.querySelector('.traveler-title');
    const firstNameInput = row.querySelector('.traveler-firstname');
    const lastNameInput = row.querySelector('.traveler-lastname');
    const genderSelect = row.querySelector('.traveler-gender');
    const ageInput = row.querySelector('.traveler-age');
    const birthDateInput = row.querySelector('.traveler-birthdate');
    const nationalityInput = row.querySelector('.traveler-nationality');
    const passportInput = row.querySelector('.traveler-passport');
    const passportIssueInput = row.querySelector('.traveler-passport-issue');
    const passportExpiryInput = row.querySelector('.traveler-passport-expiry');
    const mainTravelerRadio = row.querySelector('input[name="lead_traveler"]');
    
    typeSelect?.addEventListener('change', function(event) {
        travelers[index].type = typeSelect.value;

        // Skip room options warning if this change was triggered programmatically (from birthdate change)
        if (event.isTrusted === false) {
            // Auto-update from birthdate - check and warn about room options
            if (selectedRooms.length > 0) {
                if (confirm(getText('resetRoomOptions'))) {
                    selectedRooms = [];
                    updateRoomOptionDisplay();
                }
            }
        }
    });
    
    visaSelect?.addEventListener('change', () => {
        travelers[index].visaRequired = visaSelect.value === '1';
    });
    
    titleSelect?.addEventListener('change', () => {
        travelers[index].title = titleSelect.value;
    });
    
    firstNameInput?.addEventListener('input', () => {
        travelers[index].firstName = firstNameInput.value;
    });
    
    lastNameInput?.addEventListener('input', () => {
        travelers[index].lastName = lastNameInput.value;
    });
    
    genderSelect?.addEventListener('change', () => {
        travelers[index].gender = genderSelect.value;
    });
    
    ageInput?.addEventListener('input', () => {
        travelers[index].age = parseInt(ageInput.value) || null;
    });
    
    birthDateInput?.addEventListener('change', () => {
        travelers[index].birthDate = birthDateInput.value;
        if (birthDateInput.value) {
            const age = calculateAge(birthDateInput.value);
            const type = determineTypeByAge(age);

            travelers[index].age = age;
            travelers[index].type = type;

            ageInput.value = age;
            typeSelect.value = type;

            // Trigger change event on type select to update room options if needed
            typeSelect.dispatchEvent(new Event('change'));

            // Recalculate total amount
            calculateTotalAmount();
        }
    });
    
    nationalityInput?.addEventListener('input', () => {
        travelers[index].nationality = nationalityInput.value;
    });
    
    passportInput?.addEventListener('input', () => {
        travelers[index].passportNumber = passportInput.value;
    });
    
    passportIssueInput?.addEventListener('change', () => {
        travelers[index].passportIssueDate = passportIssueInput.value;
    });
    
    passportExpiryInput?.addEventListener('change', () => {
        travelers[index].passportExpiry = passportExpiryInput.value;
    });
    
    mainTravelerRadio?.addEventListener('change', () => {
        if (mainTravelerRadio.checked) {
            travelers.forEach((t, i) => {
                t.isMainTraveler = (i === index);
            });
            // 모든 라디오 버튼 업데이트
            document.querySelectorAll('input[name="lead_traveler"]').forEach((radio, i) => {
                radio.checked = (i === index);
            });
        }
    });

    // Passport photo upload event listeners
    const passportPhotoInput = row.querySelector('.traveler-passport-photo');
    const passportPhotoUploadBtn = row.querySelector('.passport-photo-upload');
    const passportPhotoViewBtn = row.querySelector('.passport-photo-view');
    const passportPhotoDownloadBtn = row.querySelector('.passport-photo-download');
    const passportPhotoDeleteBtn = row.querySelector('.passport-photo-delete');

    // Upload button click
    passportPhotoUploadBtn?.addEventListener('click', () => {
        passportPhotoInput?.click();
    });

    // View button click
    passportPhotoViewBtn?.addEventListener('click', () => {
        if (travelers[index].passportImage) {
            viewPassportPhoto(index);
        }
    });

    // Download button click
    passportPhotoDownloadBtn?.addEventListener('click', () => {
        downloadPassportPhoto(index);
    });

    // Delete button click
    passportPhotoDeleteBtn?.addEventListener('click', () => {
        deletePassportPhoto(index);
    });

    // File input change
    passportPhotoInput?.addEventListener('change', (e) => handlePassportPhotoChange(e, index));
}

// Handle passport photo file change
function handlePassportPhotoChange(event, index) {
    const file = event.target.files?.[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert(getText('invalidFileType') || 'Please select an image file.');
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert(getText('fileTooLarge') || 'File size must be less than 5MB.');
        return;
    }

    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
        travelers[index].passportImage = e.target.result;
        travelers[index].passportImageFile = file;

        // Update only the passport photo container, not the entire row
        updatePassportPhotoDisplay(index);
    };
    reader.readAsDataURL(file);
}

// Update passport photo display without re-rendering entire row
function updatePassportPhotoDisplay(index) {
    const row = document.getElementById(`traveler-row-${index}`);
    if (!row) return;

    const container = row.querySelector('.passport-photo-container');
    if (!container) return;

    const traveler = travelers[index];

    // Generate new HTML for passport photo container
    const newHTML = traveler.passportImage ? `
        <div class="passport-photo-preview">
            <img src="${traveler.passportImage}" alt="Passport" style="max-width: 80px; max-height: 80px; border-radius: 4px; object-fit: cover;">
            <div style="display: flex; gap: 4px; margin-top: 8px; flex-wrap: wrap; justify-content: center;">
                <button type="button" class="jw-button typeA passport-photo-view" style="font-size: 10px; padding: 3px 6px;">
                    View
                </button>
                <button type="button" class="jw-button typeA passport-photo-download" style="font-size: 10px; padding: 3px 6px;">
                    Download
                </button>
                <button type="button" class="jw-button typeA passport-photo-delete" style="font-size: 10px; padding: 3px 6px; background: #ef4444; border-color: #ef4444;">
                    Delete
                </button>
            </div>
        </div>
    ` : `
        <label class="inputFile">
            <input type="file" class="traveler-passport-photo" accept="image/*" style="display: none;">
            <button type="button" class="btn-upload passport-photo-upload"><img src="../image/upload3.svg" alt=""> <span data-lan-eng="Image upload">Image upload</span></button>
        </label>
    `;

    container.innerHTML = newHTML;

    // Re-attach event listeners for the new buttons
    const passportPhotoInput = container.querySelector('.traveler-passport-photo');
    const passportPhotoUploadBtn = container.querySelector('.passport-photo-upload');
    const passportPhotoViewBtn = container.querySelector('.passport-photo-view');
    const passportPhotoDownloadBtn = container.querySelector('.passport-photo-download');
    const passportPhotoDeleteBtn = container.querySelector('.passport-photo-delete');

    // Upload button click
    passportPhotoUploadBtn?.addEventListener('click', () => {
        passportPhotoInput?.click();
    });

    // View button click
    passportPhotoViewBtn?.addEventListener('click', () => {
        if (travelers[index].passportImage) {
            viewPassportPhoto(index);
        }
    });

    // Download button click
    passportPhotoDownloadBtn?.addEventListener('click', () => {
        downloadPassportPhoto(index);
    });

    // Delete button click
    passportPhotoDeleteBtn?.addEventListener('click', () => {
        deletePassportPhoto(index);
    });

    // File input change
    passportPhotoInput?.addEventListener('change', (e) => handlePassportPhotoChange(e, index));

    // Apply language if available
    if (typeof language_apply === 'function') {
        const currentLang = getCurrentLang();
        language_apply(currentLang);
    }
}

// View passport photo in new window
function viewPassportPhoto(index) {
    const traveler = travelers[index];
    if (!traveler || !traveler.passportImage) {
        alert(getText('noImageToView') || 'No image to view.');
        return;
    }

    // Open new window
    const newWindow = window.open('', '_blank');
    if (!newWindow) {
        alert(getText('popupBlocked') || 'Popup blocked. Please allow popups for this site.');
        return;
    }

    // Generate traveler name for title
    const firstName = traveler.firstName || 'Traveler';
    const lastName = traveler.lastName || '';
    const fullName = `${firstName} ${lastName}`.trim();

    // Write HTML content to new window
    newWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Passport Photo - ${fullName}</title>
            <style>
                body {
                    margin: 0;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background-color: #f5f5f5;
                    font-family: Arial, sans-serif;
                }
                .container {
                    text-align: center;
                }
                h2 {
                    color: #333;
                    margin-bottom: 20px;
                }
                img {
                    max-width: 90vw;
                    max-height: 80vh;
                    border: 2px solid #ddd;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Passport Photo - ${fullName}</h2>
                <img src="${traveler.passportImage}" alt="Passport Photo">
            </div>
        </body>
        </html>
    `);
    newWindow.document.close();
}

// Download passport photo
function downloadPassportPhoto(index) {
    const traveler = travelers[index];
    if (!traveler || !traveler.passportImage) {
        alert(getText('noImageToDownload') || 'No image to download.');
        return;
    }

    // Create a temporary link element
    const link = document.createElement('a');
    link.href = traveler.passportImage;

    // Generate filename
    const firstName = traveler.firstName || 'traveler';
    const lastName = traveler.lastName || '';
    const filename = `passport_${firstName}_${lastName}_${Date.now()}.jpg`.replace(/\s+/g, '_');

    link.download = filename;

    // Trigger download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Delete passport photo
function deletePassportPhoto(index) {
    const traveler = travelers[index];
    if (!traveler) return;

    if (!confirm('Are you sure you want to delete this passport photo?')) {
        return;
    }

    // Clear the passport image data
    traveler.passportImage = '';
    traveler.passportImageFile = null;

    // Update the display
    updatePassportPhotoDisplay(index);
}

// 여행자 삭제
window.deleteTraveler = function(index) {
    if (!confirm(getText('deleteTraveler'))) {
        return;
    }
    
    const row = document.getElementById(`traveler-row-${index}`);
    if (row) {
        row.remove();
    }
    
    travelers.splice(index, 1);
    
    // 인덱스 재정렬
    travelers.forEach((traveler, i) => {
        traveler.index = i;
        const row = document.getElementById(`traveler-row-${i}`);
        if (row) {
            row.querySelector('td:first-child').textContent = i + 1;
            row.id = `traveler-row-${i}`;
        }
    });
    
    // 총 금액 재계산
    calculateTotalAmount();
};

// 룸 옵션 선택 모달 열기
function openRoomOptionModal() {
    if (!selectedPackage || !selectedPackage.packageId) {
        alert(getText('pleaseSelectProduct'));
        return;
    }
    
    selectedRoomsInModal = [...selectedRooms];
    openModal('room-option-modal');
    loadRoomOptions();
}

// 기본 룸 옵션 데이터 (singlePrice는 날짜별 가격 우선, 없으면 기본 10000페소)
function getDefaultRoomOptions() {
    // 날짜별 singlePrice 우선, 없으면 기본 10000페소
    const singlePrice = selectedDateInfo?.singlePrice ?? 10000;
    return [
        { roomId: 'double', roomType: 'Double room', capacity: 2, roomPrice: 0 },
        { roomId: 'twin', roomType: 'Twin room', capacity: 2, roomPrice: 0 },
        { roomId: 'triple', roomType: 'Triple room', capacity: 3, roomPrice: 0 },
        { roomId: 'single_paid', roomType: 'Single Room + Single Supplement Surcharge', capacity: 1, roomPrice: singlePrice }
    ];
}

// 현재 로드된 룸 옵션 데이터 (API에서 가져온 데이터 저장)
let currentRoomOptions = [];

// 룸 옵션 로드
async function loadRoomOptions() {
    // 디버깅: 함수 호출 확인
    console.log('=== loadRoomOptions called ===');
    console.log('selectedDateInfo:', selectedDateInfo);
    console.log('selectedDateInfo?.price:', selectedDateInfo?.price);

    const container = document.getElementById('room-option-list');
    if (!container) return;

    try {
        container.innerHTML = `<div class="is-center">${getText('loading')}</div>`;
        
        let roomOptions = [];
        
        // 룸 옵션 API 호출 시도
        try {
            const response = await fetch(`../backend/api/package-options.php?packageId=${selectedPackage.packageId}`);
            const result = await response.json();
            
            if (result.success && result.data && result.data.roomOptions && result.data.roomOptions.length > 0) {
                roomOptions = result.data.roomOptions;
                // API에서 가져온 룸 옵션을 전역 변수에 저장
                currentRoomOptions = roomOptions;
            } else {
                // API에서 데이터가 없으면 기본 데이터 사용
                const defaultOptions = getDefaultRoomOptions();
                roomOptions = defaultOptions;
                currentRoomOptions = defaultOptions;
            }
        } catch (error) {
            console.error('Error loading room options from API:', error);
            // API 에러 시 기본 데이터 사용
            const defaultOptions = getDefaultRoomOptions();
            roomOptions = defaultOptions;
            currentRoomOptions = defaultOptions;
        }
        
        // 룸 옵션 목록 렌더링
        let html = '';
        roomOptions.forEach(room => {
            const existingRoom = selectedRoomsInModal.find(r => r.roomId === room.roomId);
            const count = existingRoom ? existingRoom.count : 0;
            
            html += `
                <div class="room-option-item">
                    <div class="room-option-name">${escapeHtml(room.roomType || '')}</div>
                    <div class="room-option-capacity">${room.capacity || 1} <span data-lan-eng="people">people</span></div>
                    ${room.roomPrice > 0 ? `<div class="room-option-price">₱${formatCurrency(room.roomPrice)}</div>` : ''}
                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn minus" onclick="changeRoomQuantity('${room.roomId}', -1)" ${count <= 0 ? 'disabled' : ''}>-</button>
                        <span class="quantity-value">${count}</span>
                        <button type="button" class="quantity-btn plus" onclick="changeRoomQuantity('${room.roomId}', 1)">+</button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        
        // 주문 요약 업데이트
        updateOrderSummary();
        updateRoomCombinationBanner();
        
    } catch (error) {
        console.error('Error loading room options:', error);
        // 에러 시 기본 데이터 사용
        const defaultOptions = getDefaultRoomOptions();
        currentRoomOptions = defaultOptions;
        let html = '';
        defaultOptions.forEach(room => {
            const existingRoom = selectedRoomsInModal.find(r => r.roomId === room.roomId);
            const count = existingRoom ? existingRoom.count : 0;
            
            html += `
                <div class="room-option-item">
                    <div class="room-option-name">${escapeHtml(room.roomType || '')}</div>
                    <div class="room-option-capacity">${room.capacity || 1} <span data-lan-eng="people">people</span></div>
                    ${room.roomPrice > 0 ? `<div class="room-option-price">₱${formatCurrency(room.roomPrice)}</div>` : ''}
                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn minus" onclick="changeRoomQuantity('${room.roomId}', -1)" ${count <= 0 ? 'disabled' : ''}>-</button>
                        <span class="quantity-value">${count}</span>
                        <button type="button" class="quantity-btn plus" onclick="changeRoomQuantity('${room.roomId}', 1)">+</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        updateOrderSummary();
        updateRoomCombinationBanner();
    }
}

// 룸 수량 변경
window.changeRoomQuantity = function(roomId, change) {
    // API에서 가져온 룸 옵션에서 먼저 찾기, 없으면 기본 데이터에서 찾기
    const room = currentRoomOptions.find(r => r.roomId === roomId) ||
                getDefaultRoomOptions().find(r => r.roomId === roomId) ||
                selectedRoomsInModal.find(r => r.roomId === roomId);
    if (!room) {
        console.warn(`Room not found: ${roomId}`);
        return;
    }
    
    const existingIndex = selectedRoomsInModal.findIndex(r => r.roomId === roomId);
    const currentCount = existingIndex >= 0 ? selectedRoomsInModal[existingIndex].count : 0;
    const newCount = Math.max(0, currentCount + change);
    
    if (newCount === 0) {
        // 수량이 0이면 제거
        selectedRoomsInModal = selectedRoomsInModal.filter(r => r.roomId !== roomId);
    } else {
        if (existingIndex >= 0) {
            // 기존 항목 업데이트 (가격 정보는 유지)
            selectedRoomsInModal[existingIndex].count = newCount;
        } else {
            // 새 항목 추가 (API에서 가져온 가격 정보 사용)
            selectedRoomsInModal.push({
                roomId: room.roomId,
                roomType: room.roomType,
                roomPrice: room.roomPrice || 0, // API에서 가져온 가격 사용
                capacity: room.capacity || 1,
                count: newCount
            });
        }
    }
    
    // UI 업데이트
    loadRoomOptions();
    // 주문 요약 업데이트 (룸 수량 변경 시 즉시 반영)
    updateOrderSummary();
};

// 주문 요약 업데이트 (calculateTotalAmount와 동일한 계산 로직 사용)
function updateOrderSummary() {
    const summaryContainer = document.getElementById('order-summary-list');
    const amountContainer = document.getElementById('order-amount-value');
    if (!summaryContainer || !amountContainer) return;

    // 디버깅: selectedDateInfo 상태 확인
    console.log('updateOrderSummary - selectedDateInfo:', selectedDateInfo);
    console.log('updateOrderSummary - selectedPackage.packagePrice:', selectedPackage?.packagePrice);

    // calculateTotalAmount와 동일한 계산 로직 사용
    let totalAmount = 0;
    let summaryHtml = '';

    // 상품 가격: 날짜 선택 시 해당 날짜 가격 사용, 없으면 기본 가격 사용
    if (selectedPackage && (selectedDateInfo?.price || selectedPackage.packagePrice)) {
        const adults = travelers.filter(t => t.type === 'adult').length;
        const children = travelers.filter(t => t.type === 'child').length;
        const infants = travelers.filter(t => t.type === 'infant').length;

        // 날짜별 가격 우선, 없으면 상품 기본 가격 사용
        const adultPrice = selectedDateInfo?.price ?? selectedPackage.packagePrice ?? 0;
        console.log('updateOrderSummary - adultPrice used:', adultPrice);

        // packageType에 따라 가격 계산
        // Full 패키지: adult 100%, children 날짜별 가격 우선 (없으면 80%), infant DB에서 가져온 가격 (없으면 6500페소)
        // Land 패키지: adult 100%, children 날짜별 가격 우선 (없으면 70%), infant 무료
        const packageType = selectedPackage.packageType || 'full';
        const defaultChildPrice = packageType === 'land' ? adultPrice * 0.7 : adultPrice * 0.8;
        const childPrice = selectedDateInfo?.childPrice ?? defaultChildPrice;
        const infantPrice = packageType === 'land' ? 0 : (selectedPackage.infantPrice ?? 6500);
        console.log('packageType:', packageType, '| childPrice:', childPrice, '| infantPrice:', infantPrice);
        
        // Adult
        if (adults > 0) {
            const adultTotal = adultPrice * adults;
            totalAmount += adultTotal;
            summaryHtml += `
                <div class="order-summary-item">
                    <span data-lan-eng="Adult">Adult</span> x${adults}: <span class="order-price">${formatCurrency(adultTotal)} (P)</span>
                </div>
            `;
        }

        // Children
        if (children > 0) {
            const childTotal = childPrice * children;
            totalAmount += childTotal;
            summaryHtml += `
                <div class="order-summary-item">
                    <span data-lan-eng="Children">Children</span> x${children}: <span class="order-price">${formatCurrency(childTotal)} (P)</span>
                </div>
            `;
        }

        // Infants (Infants have a price but are not included in room occupancy)
        if (infants > 0) {
            const infantTotal = infantPrice * infants;
            totalAmount += infantTotal;
            summaryHtml += `
                <div class="order-summary-item">
                    <span data-lan-eng="Infant">Infant</span> x${infants}: <span class="order-price">${formatCurrency(infantTotal)} (P)</span>
                </div>
            `;
        }
    }


    // Room option price (same logic as calculateTotalAmount)
    selectedRoomsInModal.forEach(room => {
        if (room.count > 0) {
            const roomTotal = (room.roomPrice || 0) * (room.count || 1);
            totalAmount += roomTotal;
            summaryHtml += `
                <div class="order-summary-item">
                    ${escapeHtml(room.roomType || '')} x${room.count}: <span class="order-price">${formatCurrency(roomTotal)} (P)</span>
                </div>
            `;
        }
    });

    if (summaryHtml === '') {
        summaryHtml = '<div class="order-summary-item" data-lan-eng="No items selected">No items selected</div>';
    }
    
    summaryContainer.innerHTML = summaryHtml;
    amountContainer.textContent = `${formatCurrency(totalAmount)} (P)`;
}

// 룸 조합 배너 업데이트 및 인원 검증
function updateRoomCombinationBanner() {
    const banner = document.getElementById('room-combination-count');
    if (!banner) return;

    // 총 예약 인원 수 계산 (유아는 제외, 성인 + 아동만)
    const adults = travelers.filter(t => t.type === 'adult').length;
    const children = travelers.filter(t => t.type === 'child').length;
    const totalBookingGuests = adults + children; // 유아는 제외 (select-room.js와 동일)

    // 각 룸타입 수량 × 수용 인원 합 계산
    let totalCapacity = 0;
    selectedRoomsInModal.forEach(room => {
        const roomCapacity = (room.capacity || 0) * (room.count || 0);
        totalCapacity += roomCapacity;
    });

    banner.textContent = `(${totalCapacity}/${totalBookingGuests} ${getText('People') || 'People'})`;

    // 인원 검증 및 버튼 활성화/비활성화 (select-room.js의 validateRoomSelection과 동일한 로직)
    validateRoomCapacity(totalCapacity, totalBookingGuests);
}

// 인원 검증 함수 (select-room.js의 validateRoomSelection과 동일한 로직)
function validateRoomCapacity(totalCapacity, totalBookingGuests) {
    const confirmBtn = document.getElementById('confirm-room-selection-btn');
    if (!confirmBtn) return;
    
    // 초기 상태 (객실 미선택)
    if (totalCapacity === 0) {
        confirmBtn.disabled = true;
        return;
    }
    
    // 요구 인원이 0이면 버튼 비활성화
    if (totalBookingGuests === 0) {
        confirmBtn.disabled = true;
        return;
    }
    
    // 총 예약 인원 수 = 각 룸타입 수량 × 수용 인원 합 검증
    // 수용 인원이 부족한 경우
    if (totalCapacity < totalBookingGuests) {
        confirmBtn.disabled = true;
        return;
    }
    
    // 수용 인원이 예약 인원보다 많은 경우
    if (totalCapacity > totalBookingGuests) {
        confirmBtn.disabled = true;
        return;
    }
    
    // 수용 인원이 예약 인원과 정확히 일치하는 경우만 버튼 활성화
    if (totalCapacity === totalBookingGuests) {
        confirmBtn.disabled = false;
        return;
    }
}

// 룸 옵션 선택 확인 (select-room.js와 동일한 검증 로직)
function confirmRoomSelection() {
    // 총 예약 인원 수 계산 (유아는 제외, 성인 + 아동만) - select-room.js와 동일
    const adults = travelers.filter(t => t.type === 'adult').length;
    const children = travelers.filter(t => t.type === 'child').length;
    const totalBookingGuests = adults + children; // 유아는 제외

    // 각 룸타입 수량 × 수용 인원 합 계산
    let totalCapacity = 0;
    let hasAnyRoom = false;
    selectedRoomsInModal.forEach(room => {
        if (room.count > 0) hasAnyRoom = true;
        const roomCapacity = (room.capacity || 0) * (room.count || 0);
        totalCapacity += roomCapacity;
    });

    // Initial state (no room selected)
    if (!hasAnyRoom) {
        alert('Please select rooms.');
        return;
    }

    // Insufficient room capacity
    if (totalCapacity < totalBookingGuests) {
        alert('Insufficient room capacity.');
        return;
    }

    // Room capacity exceeds required
    if (totalCapacity > totalBookingGuests) {
        alert(`The number of people does not match the room capacity. Selected capacity: ${totalCapacity}, Required: ${totalBookingGuests}.`);
        return;
    }

    // Proceed only when room capacity exactly matches booking guests
    if (totalCapacity === totalBookingGuests) {
        selectedRooms = [...selectedRoomsInModal];
        updateRoomOptionDisplay();
        // Update Order Amount after room option selection (same calculation as updateOrderSummary)
        calculateTotalAmount();
        closeModal('room-option-modal');
        return;
    }
}

// 룸 옵션 표시 업데이트
function updateRoomOptionDisplay() {
    const roomOptionBtn = document.getElementById('room_option_btn');
    if (roomOptionBtn && selectedRooms.length > 0) {
        const totalRooms = selectedRooms.reduce((sum, room) => sum + room.count, 0);
        const lang = getCurrentLang();
        if (lang === 'eng') {
            roomOptionBtn.textContent = getText('selectRoomOptionCount', { count: totalRooms });
        } else {
            roomOptionBtn.textContent = getText('selectRoomOptionCount', { count: totalRooms });
        }
    } else if (roomOptionBtn) {
        roomOptionBtn.textContent = getText('selectRoomOption');
    }
}

// 총 금액 계산
// 선금 고정 금액
const FIXED_ADVANCE_PAYMENT = 5000;

// 선금 자동 계산 함수 (항상 5,000으로 고정)
function calculateAdvancePayment(orderAmount) {
    return FIXED_ADVANCE_PAYMENT;
}

// 잔금 계산 함수
function calculateBalanceAmount(orderAmount, advancePayment) {
    const balance = orderAmount - FIXED_ADVANCE_PAYMENT;
    return Math.max(0, balance); // 음수 방지
}

function calculateTotalAmount() {
    let total = 0;

    // 상품 가격: 날짜 선택 시 해당 날짜 가격 사용, 없으면 기본 가격 사용
    if (selectedPackage && (selectedDateInfo?.price || selectedPackage.packagePrice)) {
        const adults = travelers.filter(t => t.type === 'adult').length;
        const children = travelers.filter(t => t.type === 'child').length;
        const infants = travelers.filter(t => t.type === 'infant').length;

        // 날짜별 가격 우선, 없으면 상품 기본 가격 사용
        const adultPrice = selectedDateInfo?.price ?? selectedPackage.packagePrice ?? 0;

        // packageType에 따라 가격 계산
        // Full 패키지: adult 100%, children 날짜별 가격 우선 (없으면 80%), infant DB에서 가져온 가격 (없으면 6500페소)
        // Land 패키지: adult 100%, children 날짜별 가격 우선 (없으면 70%), infant 무료
        const packageType = selectedPackage.packageType || 'full';
        const defaultChildPrice = packageType === 'land' ? adultPrice * 0.7 : adultPrice * 0.8;
        const childPrice = selectedDateInfo?.childPrice ?? defaultChildPrice;
        const infantPrice = packageType === 'land' ? 0 : (selectedPackage.infantPrice ?? 6500);

        total += (adultPrice * adults) + (childPrice * children) + (infantPrice * infants);
    }
    
    // 룸 옵션 가격
    selectedRooms.forEach(room => {
        total += (room.roomPrice || 0) * (room.count || 1);
    });
    
    // 추가 옵션 가격 (기내수하물, 조식, 와이파이 등)
    // TODO: 추가 옵션 가격 계산
    
    const totalInput = document.getElementById('pay_total');
    if (totalInput) {
        totalInput.value = formatCurrency(total);
    }

    // 3단계 결제 시스템: 선금 5000, 중도금 10000 고정
    // 잔금만 자동 계산
    updatePaymentAmounts(total);
}

// 3단계 결제 금액 계산
// Full 패키지: Down Payment 5,000₱ × 인원수, Advance Payment 10,000₱ × 인원수
// Land 패키지: Down Payment 3,000₱ × 인원수, Advance Payment 5,000₱ × 인원수
// 유아는 Down Payment, Advance Payment 인원수에서 제외 (유아 금액은 balance에 포함)
function updatePaymentAmounts(total = null) {
    const totalInput = document.getElementById('pay_total');
    const balanceInput = document.getElementById('pay_balance');
    const downPaymentInput = document.getElementById('pay_down_payment');
    const advancePaymentInput = document.getElementById('pay_advance_payment');

    if (!total) {
        total = parseFloat(totalInput?.value.replace(/[^\d.]/g, '')) || 0;
    }

    // 인원수 계산 (유아 제외)
    const adults = travelers.filter(t => t.type === 'adult').length;
    const children = travelers.filter(t => t.type === 'child').length;
    const headcount = adults + children; // 유아 제외

    // packageType에 따라 1인당 선금과 중도금 결정
    // Full 패키지: downPayment 5000/인, advancePayment 10000/인
    // Land 패키지: downPayment 3000/인, advancePayment 5000/인
    const packageType = selectedPackage?.packageType || 'full';
    const downPaymentPerPerson = packageType === 'land' ? 3000 : 5000;
    const advancePaymentPerPerson = packageType === 'land' ? 5000 : 10000;

    const downPayment = downPaymentPerPerson * headcount;
    const advancePayment = advancePaymentPerPerson * headcount;
    const balance = Math.max(0, total - downPayment - advancePayment);

    // Down Payment 입력란 업데이트
    if (downPaymentInput) {
        downPaymentInput.value = formatCurrency(downPayment);
    }

    // Advance Payment 입력란 업데이트
    if (advancePaymentInput) {
        advancePaymentInput.value = formatCurrency(advancePayment);
    }

    if (balanceInput) {
        balanceInput.value = formatCurrency(balance);
    }
}

function initializeDownPaymentProofUpload() {
    const uploadBtn = document.getElementById('down_payment_file_upload_btn');
    const fileInput = document.getElementById('down_payment_file_input');
    const removeBtn = document.getElementById('down_payment_file_remove');
    const fileInfo = document.getElementById('down_payment_file_info');
    const fileNameEl = document.getElementById('down_payment_file_name');
    
    if (!uploadBtn || !fileInput) return;
    
    uploadBtn.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (!file) return;

        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert(getText('depositFileTooLarge') || '파일 크기는 10MB 이하여야 합니다.');
            event.target.value = '';
            return;
        }

        downPaymentProofFile = file;
        if (fileNameEl) {
            fileNameEl.textContent = `파일 선택됨: ${file.name} (${formatFileSize(file.size)})`;
        }
        if (fileInfo) {
            fileInfo.style.display = 'block';
        }
    });

    removeBtn?.addEventListener('click', () => {
        clearDownPaymentProofFile();
    });
}

function clearDownPaymentProofFile() {
    downPaymentProofFile = null;
    const fileInput = document.getElementById('down_payment_file_input');
    const fileInfo = document.getElementById('down_payment_file_info');
    const fileNameEl = document.getElementById('down_payment_file_name');
    if (fileInput) {
        fileInput.value = '';
    }
    if (fileInfo) {
        fileInfo.style.display = 'none';
    }
    if (fileNameEl) {
        fileNameEl.textContent = '';
    }
}

// 저장 처리
async function handleSave() {
    try {
        // 필수 필드 검증
        if (!selectedPackage || !selectedPackage.packageId) {
            alert(getText('requiredFields') + '\n' + getText('pleaseSelectProduct'));
            return;
        }
        
        const departureDateInput = document.getElementById('departure_date');
        const departureDateValueInput = document.getElementById('departure_date_value');
        if (!departureDateInput || !departureDateInput.value || !departureDateValueInput || !departureDateValueInput.value) {
            alert(getText('requiredFields') + '\n' + getText('selectTravelStartDate'));
            return;
        }
        
        const userNameInput = document.getElementById('user_name');
        const userEmailInput = document.getElementById('user_email');
        const userPhoneInput = document.getElementById('user_phone');
        
        if (!userNameInput?.value || !userEmailInput?.value || !userPhoneInput?.value) {
            alert(getText('requiredFields') + '\n' + getText('enterCustomerInfo'));
            return;
        }
        
        if (travelers.length === 0) {
            alert(getText('requiredFields') + '\n' + getText('enterTravelerInfo'));
            return;
        }
        
        // 여행자 정보 검증
        for (let i = 0; i < travelers.length; i++) {
            const traveler = travelers[i];
            if (!traveler.firstName || !traveler.lastName) {
                alert(getText('requiredFields') + '\n' + getText('enterTravelerName', { index: i + 1 }));
                return;
            }
        }
        
        // 3단계 결제 시스템: 선금(5000), 중도금(10000), 잔금은 백엔드에서 자동 계산
        // 선금 증빙 파일은 선택사항
        
        // 고객 정보
        const nameParts = userNameInput.value.trim().split(' ');
        const customerInfo = {
            accountId: document.getElementById('customer_account_id').value || null,
            firstName: nameParts[0] || '',
            lastName: nameParts.slice(1).join(' ') || '',
            email: userEmailInput.value,
            phone: userPhoneInput.value,
            countryCode: document.getElementById('country_code').value || '+63'
        };
        
        // 중복 제거: accountId 또는 이름(firstName + lastName)으로
        const uniqueTravelers = [];
        const seen = new Set();

        for (const t of travelers) {
            // accountId가 있으면 accountId로, 없으면 이름으로 중복 체크
            const key = t.accountId
                ? `id:${t.accountId}`
                : `name:${(t.firstName || '').toLowerCase()}|${(t.lastName || '').toLowerCase()}`;

            if (key !== 'name:|' && !seen.has(key)) {
                seen.add(key);
                uniqueTravelers.push(t);
            } else if (key === 'name:|') {
                // 이름이 비어있는 경우 (빈 traveler)는 그냥 추가
                uniqueTravelers.push(t);
            }
        }

        console.log('Original travelers:', travelers.length, 'Unique travelers:', uniqueTravelers.length);

        // 인원 수 계산 (중복 제거된 배열 기준)
        const adults = uniqueTravelers.filter(t => t.type === 'adult').length;
        const children = uniqueTravelers.filter(t => t.type === 'child').length;
        const infants = uniqueTravelers.filter(t => t.type === 'infant').length;

        const seatRequestValue = getEditorPlainText('seat_req_editor');
        const otherRequestValue = getEditorPlainText('etc_req_editor');
        const memoValue = getEditorPlainText('memo_editor');

        // 예약 생성 데이터
        const reservationData = {
            action: 'createReservation',
            packageId: selectedPackage.packageId,
            departureDate: departureDateValueInput.value,
            departureTime: '12:20:00',
            customerInfo: customerInfo,
            travelers: uniqueTravelers.map(t => ({
                type: t.type,
                title: t.title,
                firstName: t.firstName,
                lastName: t.lastName,
                gender: t.gender,
                age: t.age,
                birthDate: t.birthDate,
                contact: t.contact,
                email: t.email,
                nationality: t.nationality,
                passportNumber: t.passportNumber,
                passportIssueDate: t.passportIssueDate,
                passportExpiry: t.passportExpiry,
                passportImage: t.passportImage || '',
                accountId: t.accountId || null,
                visaRequired: t.visaRequired,
                isMainTraveler: t.isMainTraveler,
                remarks: t.remarks
            })),
            adults: adults,
            children: children,
            infants: infants,
            selectedRooms: selectedRooms,
            selectedOptions: selectedOptions,
            seatRequest: seatRequestValue,
            otherRequest: otherRequestValue,
            memo: memoValue
            // 3단계 결제: depositAmount, depositDueDate 제거 (백엔드에서 자동 계산)
        };
        
        const formData = new FormData();
        formData.append('action', 'createReservation');
        formData.append('data', JSON.stringify(reservationData));
        if (downPaymentProofFile) {
            formData.append('downPaymentProof', downPaymentProofFile);
        }
        
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });
        
        const responseText = await response.text();
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response:', responseText);
            throw new Error(getText('reservationError'));
        }
        
        if (result.success) {
            downPaymentProofFile = null;
            clearDownPaymentProofFile();
            alert(getText('reservationCreated'));
            const bookingId = result.data && result.data.bookingId;
            if (bookingId) {
                window.location.href = `reservation-detail.php?id=${bookingId}`;
            } else {
                window.location.href = 'reservation-list.html';
            }
        } else {
            alert(getText('reservationFailed', { message: result.message }));
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert(getText('reservationError'));
    }
}

// 유틸리티 함수들
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US').format(Math.round(amount || 0));
}

function getEditorPlainText(editorId) {
    const editor = document.getElementById(editorId);
    if (!editor) return '';
    return editor.innerText.replace(/\u00a0/g, ' ').trim();
}

function setEditorPlainText(editorId, value) {
    const editor = document.getElementById(editorId);
    if (!editor) return;
    editor.innerHTML = value ? value.replace(/\n/g, '<br>') : '';
}

function formatFileSize(bytes) {
    if (!bytes) return '0B';
    if (bytes < 1024) return `${bytes}B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)}KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)}MB`;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

function formatDateForInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

function calculateAge(dateOfBirth) {
    if (!dateOfBirth) return null;
    const birth = new Date(dateOfBirth);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

// Determine traveler type based on age (US age calculation)
function determineTypeByAge(age) {
    if (age === null || age === undefined) return 'adult';
    if (age < 2) return 'infant';
    if (age >= 2 && age <= 7) return 'child';
    return 'adult';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 테스트 데이터 채우기
async function fillTestData() {
    try {
        console.log('테스트 데이터 채우기 시작...');
        clearDepositProofFile();
        
        // 1. DB에서 상품 정보 가져오기
        const packagesUrl = `../backend/api/packages.php?limit=10`;
        const packagesResponse = await fetch(packagesUrl);
        const packagesText = await packagesResponse.text();
        if (!packagesResponse.ok) {
            throw new Error(`HTTP ${packagesResponse.status}: ${packagesText.substring(0, 200)}`);
        }
        let packagesResult;
        try {
            packagesResult = JSON.parse(packagesText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${packagesText.substring(0, 200)}`);
        }
        
        let testPackage = null;
        if (packagesResult.success && packagesResult.data && packagesResult.data.length > 0) {
            // 첫 번째 상품 사용
            testPackage = packagesResult.data[0];
            console.log('선택된 상품:', testPackage);
            
            // 상품 정보 설정
            selectedPackage = testPackage;
            selectedProductInModal = testPackage.packageId;
            previousPackageId = testPackage.packageId;
            document.getElementById('product_name').value = testPackage.packageName || '';
            document.getElementById('package_id').value = testPackage.packageId || '';
            
            // 여행 시작일 입력 활성화
            const departureDateInput = document.getElementById('departure_date');
            const departureDateBtn = document.getElementById('departure_date_btn');
            departureDateInput.disabled = false;
            departureDateInput.removeAttribute('readonly');
            if (departureDateBtn) {
                departureDateBtn.disabled = false;
            }
            
            // 가용 날짜 로드
            await loadAvailableDates(testPackage.packageId);
            
            // 가용 날짜 중 첫 번째 날짜 선택 (30일 후)
            const today = new Date();
            const futureDate = new Date(today);
            futureDate.setDate(futureDate.getDate() + 30);
            const dateStr = futureDate.toISOString().split('T')[0];
            
            // 날짜 선택 (가용 날짜가 있으면 첫 번째, 없으면 임의 날짜)
            if (availableDates.length > 0) {
                selectedDateInCalendar = availableDates[0];
            } else {
                selectedDateInCalendar = dateStr;
            }
            
            // 날짜 적용
            const selectedDate = new Date(selectedDateInCalendar);
            document.getElementById('departure_date').value = selectedDate.toLocaleDateString('ko-KR');
            document.getElementById('departure_date_value').value = selectedDateInCalendar;
            
            // 종료일 계산 (duration_days 또는 durationDays 사용)
            // return_date 필드는 제거되었으므로 주석 처리
            // const duration = testPackage.durationDays || testPackage.duration_days || 5;
            // const returnDate = new Date(selectedDate);
            // returnDate.setDate(returnDate.getDate() + duration - 1);
            // const returnDateInput = document.getElementById('return_date');
            // if (returnDateInput) {
            //     returnDateInput.value = returnDate.toLocaleDateString('ko-KR');
            //     returnDateInput.disabled = false;
            // }
        } else {
            alert('상품 정보를 불러올 수 없습니다. DB에 상품이 있는지 확인해주세요.');
            return;
        }
        
        // 2. DB에서 고객 정보 가져오기
        const customersUrl = `./admin_v2/backend/api/agent-api.php?action=getCustomers&limit=10`;
        const customersResponse = await fetch(customersUrl);
        const customersText = await customersResponse.text();
        if (!customersResponse.ok) {
            throw new Error(`HTTP ${customersResponse.status}: ${customersText.substring(0, 200)}`);
        }
        let customersResult;
        try {
            customersResult = JSON.parse(customersText);
        } catch (parseError) {
            throw new Error(`Invalid JSON response: ${customersText.substring(0, 200)}`);
        }
        
        let testCustomer = null;
        if (customersResult.success && customersResult.data && customersResult.data.customers && customersResult.data.customers.length > 0) {
            // 첫 번째 고객 사용
            testCustomer = customersResult.data.customers[0];
            console.log('선택된 고객:', testCustomer);
            
            // 고객 상세 정보 가져오기
            const detailUrl = `../admin_v2/backend/api/agent-api.php?action=getCustomerDetail&accountId=${encodeURIComponent(testCustomer.accountId)}`;
            const customerDetailResponse = await fetch(detailUrl);
            const detailText = await customerDetailResponse.text();
            if (!customerDetailResponse.ok) {
                throw new Error(`HTTP ${customerDetailResponse.status}: ${detailText.substring(0, 200)}`);
            }
            let customerDetailResult;
            try {
                customerDetailResult = JSON.parse(detailText);
            } catch (parseError) {
                throw new Error(`Invalid JSON response: ${detailText.substring(0, 200)}`);
            }
            
            if (customerDetailResult.success && customerDetailResult.data && customerDetailResult.data.customer) {
                const customerDetail = customerDetailResult.data.customer;
                selectedCustomer = customerDetail;
                
                // 예약 고객 정보 채우기
                document.getElementById('user_name').value = `${customerDetail.fName || ''} ${customerDetail.lName || ''}`.trim();
                document.getElementById('user_email').value = customerDetail.accountEmail || customerDetail.emailAddress || testCustomer.emailAddress || '';
                document.getElementById('user_phone').value = customerDetail.contactNo || testCustomer.contactNo || '';
                document.getElementById('country_code').value = customerDetail.countryCode || '+63';
                document.getElementById('customer_account_id').value = customerDetail.accountId || testCustomer.accountId || '';
            } else {
                // 상세 정보가 없으면 기본 정보만 사용
                document.getElementById('user_name').value = `${testCustomer.fName || ''} ${testCustomer.lName || ''}`.trim();
                document.getElementById('user_email').value = testCustomer.emailAddress || '';
                document.getElementById('user_phone').value = testCustomer.contactNo || '';
                document.getElementById('country_code').value = '+63';
                document.getElementById('customer_account_id').value = testCustomer.accountId || '';
            }
        } else {
            // 고객이 없으면 임의 값 사용
            document.getElementById('user_name').value = 'Test User';
            document.getElementById('user_email').value = 'test@example.com';
            document.getElementById('user_phone').value = '1234567890';
            document.getElementById('country_code').value = '+63';
        }
        
        // 3. 여행자 정보 추가 (3명)
        travelers = []; // 초기화
        const tbody = document.getElementById('travelers-tbody');
        if (tbody) {
            tbody.innerHTML = '';
        }
        
        // 첫 번째 여행자 (대표 여행자) - 고객 정보 사용
        const baseMainTraveler = {
            isMainTraveler: true,
            type: 'adult',
            visaRequired: false,
            title: 'MR',
            firstName: 'John',
            lastName: 'Doe',
            gender: 'male',
            age: 30,
            birthDate: '1994-01-15',
            contact: '1234567890',
            email: 'test1@example.com',
            nationality: 'Philippines',
            passportNumber: 'P12345678',
            passportExpiry: '2028-12-31',
            remarks: 'Main traveler'
        };
        
        const firstTravelerData = testCustomer ? {
            ...baseMainTraveler,
            firstName: testCustomer.fName || baseMainTraveler.firstName,
            lastName: testCustomer.lName || baseMainTraveler.lastName,
            gender: testCustomer.gender || baseMainTraveler.gender,
            age: testCustomer.dateOfBirth ? calculateAge(testCustomer.dateOfBirth) : baseMainTraveler.age,
            birthDate: testCustomer.dateOfBirth || baseMainTraveler.birthDate,
            contact: testCustomer.contactNo || baseMainTraveler.contact,
            email: testCustomer.emailAddress || baseMainTraveler.email,
            nationality: testCustomer.nationality || baseMainTraveler.nationality,
            passportNumber: testCustomer.passportNumber || baseMainTraveler.passportNumber,
            passportExpiry: testCustomer.passportExpiry || baseMainTraveler.passportExpiry
        } : baseMainTraveler;
        
        addTravelerWithData(firstTravelerData);
        
        // 두 번째 여행자
        addTravelerWithData({
            isMainTraveler: false,
            type: 'adult',
            visaRequired: true,
            title: 'MS',
            firstName: 'Maria',
            lastName: 'Santos',
            gender: 'female',
            age: 28,
            birthDate: '1996-03-20',
            contact: '9876543210',
            email: 'maria@example.com',
            nationality: 'Philippines',
            passportNumber: 'P87654321',
            passportExpiry: '2029-06-30',
            remarks: 'Second traveler'
        });
        
        // 세 번째 여행자 (아동)
        addTravelerWithData({
            isMainTraveler: false,
            type: 'child',
            visaRequired: false,
            title: 'MR',
            firstName: 'Juan',
            lastName: 'Santos',
            gender: 'male',
            age: 8,
            birthDate: '2016-07-10',
            contact: '9876543210',
            email: 'maria@example.com',
            nationality: 'Philippines',
            passportNumber: 'P11111111',
            passportExpiry: '2027-05-15',
            remarks: 'Child traveler'
        });
        
        // 4. 예약 정보 채우기
        // 기내 수화물 추가 (opt_breakfast는 빈 옵션이 있으므로 skip)
        
        // 조식 신청
        const breakfastSelect = document.getElementById('opt_breakfast2');
        if (breakfastSelect) {
            const breakfastOption = Array.from(breakfastSelect.options).find(opt => opt.textContent.includes('신청') || opt.getAttribute('data-lan-eng') === 'Applied');
            if (breakfastOption) {
                breakfastSelect.value = breakfastOption.value || breakfastOption.textContent;
            }
        }
        
        // 와이파이 대여
        const wifiSelect = document.getElementById('opt_wifi');
        if (wifiSelect) {
            const wifiOption = Array.from(wifiSelect.options).find(opt => opt.textContent.includes('신청') || opt.getAttribute('data-lan-eng') === 'Applied');
            if (wifiOption) {
                wifiSelect.value = wifiOption.value || wifiOption.textContent;
            }
        }
        
        // 기내 수화물 추가
        const baggageSelect = document.getElementById('opt_baggage');
        if (baggageSelect) {
            const baggageOption = Array.from(baggageSelect.options).find(opt => opt.textContent.includes('20kg') || opt.getAttribute('data-lan-eng') === 'Add 20kg');
            if (baggageOption) {
                baggageSelect.value = baggageOption.value || baggageOption.textContent;
            }
        }
        
        // 항공 좌석 요청사항
        setEditorPlainText('seat_req_editor', '창가 자리 부탁드립니다.\n조용한 구역 선호합니다.');
        
        // 기타 요청사항
        setEditorPlainText('etc_req_editor', '특별 식사 요청: 할랄 식사\n공항 픽업 서비스 요청');
        
        // 메모
        setEditorPlainText('memo_editor', '테스트 예약입니다.\n고객 연락처 확인 완료.\n특별 요청사항 확인 필요.');
        
        // 5. 결제 정보 채우기
        // 총 금액 계산 (나중에 자동 계산될 예정이지만 임시로 설정)
        const basePrice = testPackage.packagePrice || 50000;
        const totalAmount = basePrice * travelers.length;
        document.getElementById('pay_total').value = formatCurrency(totalAmount);
        
        // 선금 입금 기한 (7일 후)
        const depositDueDate = new Date();
        depositDueDate.setDate(depositDueDate.getDate() + 7);
        document.getElementById('deposit_due').value = depositDueDate.toISOString().split('T')[0];
        
        // 총 금액 재계산 (선금과 잔금도 자동 계산됨)
        calculateTotalAmount();
        
        console.log('테스트 데이터 채우기 완료!');
        alert('테스트 데이터가 채워졌습니다!');
        
    } catch (error) {
        console.error('테스트 데이터 채우기 중 오류:', error);
        alert('테스트 데이터 채우기 중 오류가 발생했습니다: ' + error.message);
    }
}
