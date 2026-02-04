/**
 * Agent Admin - Customer Detail Page JavaScript
 */

let currentAccountId = null;
let currentLang = 'ko';

// 다국어 텍스트
const texts = {
    ko: {
        loading: '로딩 중...',
        noData: '데이터가 없습니다.',
        noBookings: '예약 내역이 없습니다.',
        noInquiries: '문의 내역이 없습니다.',
        saved: '저장되었습니다.',
        saveFailed: '저장에 실패했습니다.',
        error: '오류가 발생했습니다.',
        confirmPasswordReset: '비밀번호를 초기화하시겠습니까?',
        passwordResetSuccess: '비밀번호가 초기화되었습니다.',
        passwordResetFailed: '비밀번호 초기화에 실패했습니다.'
    },
    en: {
        loading: 'Loading...',
        noData: 'No data available.',
        noBookings: 'No reservation history.',
        noInquiries: 'No inquiry history.',
        saved: 'Saved successfully.',
        saveFailed: 'Failed to save.',
        error: 'An error occurred.',
        confirmPasswordReset: 'Do you want to reset the password?',
        passwordResetSuccess: 'Password has been reset.',
        passwordResetFailed: 'Failed to reset password.'
    }
};

function getCurrentLang() {
    const langCookie = document.cookie.split(';').find(c => c.trim().startsWith('language='));
    return langCookie ? langCookie.split('=')[1] : 'ko';
}

function getText(key) {
    return texts[currentLang]?.[key] || texts['ko'][key] || key;
}

document.addEventListener('DOMContentLoaded', function() {
    currentLang = getCurrentLang();
    
    // URL에서 accountId 가져오기
    const urlParams = new URLSearchParams(window.location.search);
    currentAccountId = urlParams.get('id') || urlParams.get('accountId');
    
    if (currentAccountId) {
        loadCustomerDetail();
    } else {
        showError('고객 ID가 없습니다.');
    }
    
    // 저장 버튼 이벤트
    const saveButton = document.querySelector('.page-toolbar-actions .jw-button.typeB');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }
    
    // 비밀번호 초기화 버튼
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');
    if (resetPasswordBtn) {
        resetPasswordBtn.addEventListener('click', handlePasswordReset);
    }
    
    // 여권 사진 다운로드/삭제 버튼
    const downloadPassportBtn = document.getElementById('downloadPassportBtn');
    const deletePassportBtn = document.getElementById('deletePassportBtn');
    if (downloadPassportBtn) {
        downloadPassportBtn.addEventListener('click', handleDownloadPassport);
    }
    if (deletePassportBtn) {
        deletePassportBtn.addEventListener('click', handleDeletePassport);
    }
    
    // 여권 사진 파일 선택
    const passportFileInput = document.getElementById('file-passport');
    if (passportFileInput) {
        passportFileInput.addEventListener('change', handlePassportFileSelect);
    }
});

async function loadCustomerDetail() {
    try {
        showLoading();
        
        const response = await fetch(`../backend/api/agent-api.php?action=getCustomerDetail&accountId=${currentAccountId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            renderCustomerDetail(result.data);
        } else {
            // API 에러가 발생해도 빈 데이터로 렌더링 (데이터가 없을 수 있음)
            console.warn('API returned error, but rendering with empty data:', result.message);
            renderCustomerDetail({
                customer: {},
                bookings: [],
                inquiries: []
            });
        }
    } catch (error) {
        console.error('Error loading customer detail:', error);
        // 네트워크 오류 등 실제 오류인 경우에만 에러 표시
        // 데이터가 없는 경우는 빈 데이터로 렌더링
        renderCustomerDetail({
            customer: {},
            bookings: [],
            inquiries: []
        });
    } finally {
        hideLoading();
    }
}

function renderCustomerDetail(data) {
    const customer = data.customer || {};
    const bookings = data.bookings || [];
    const inquiries = data.inquiries || [];
    
    console.log('renderCustomerDetail - Full customer data:', customer);
    console.log('renderCustomerDetail - profileImage:', customer.profileImage);
    
    // 기본 정보 - 데이터가 없어도 빈 값으로 설정
    const customerNameInput = document.getElementById('cust_name');
    if (customerNameInput) {
        if (customer.fName && customer.lName) {
            customerNameInput.value = `${customer.fName} ${customer.lName}`;
        } else {
            customerNameInput.value = '';
        }
    }
    
    // 이메일
    const emailInput = document.getElementById('cust_email');
    if (emailInput) {
        emailInput.value = customer.accountEmail || customer.emailAddress || '';
    }
    
    // 연락처
    const phoneInput = document.getElementById('cust_phone');
    if (phoneInput) {
        phoneInput.value = customer.contactNo || '';
    }
    
    // 국가 코드
    const countryCodeSelect = document.getElementById('country_code');
    if (countryCodeSelect) {
        if (customer.countryCode) {
            countryCodeSelect.value = customer.countryCode;
        } else {
            countryCodeSelect.value = '+63'; // 기본값
        }
    }
    
    // 고객 번호
    const custNoInput = document.getElementById('cust_no');
    if (custNoInput) {
        custNoInput.value = customer.clientId || '';
    }
    
    // Agent Name (fName + lName)
    const branchInput = document.getElementById('cust_branch');
    if (branchInput) {
        // Use agent's full name (fName + lName) instead of branch name
        if (customer.agentFName || customer.agentLName) {
            branchInput.value = `${customer.agentFName || ''} ${customer.agentLName || ''}`.trim();
        } else {
            branchInput.value = '';
        }
    }
    
    // 등록일시
    const createdAtInput = document.getElementById('created_at');
    if (createdAtInput) {
        if (customer.accountCreatedAt || customer.createdAt) {
            const date = new Date(customer.accountCreatedAt || customer.createdAt);
            createdAtInput.value = formatDateTime(date);
        } else {
            createdAtInput.value = '';
        }
    }
    
    // Note (에디터) - Quill 에디터에 내용 설정 (데이터가 없어도 빈 값으로 설정)
    const memo = customer.memo || customer.note || '';
    setTimeout(() => {
        const editorArea = document.querySelector('.jweditor');
        if (editorArea) {
            const editorRoot = editorArea.closest('.jw-editor');
            if (editorRoot) {
                // Quill 인스턴스 찾기
                const quillEditor = editorArea.querySelector('.ql-editor');
                if (quillEditor && quillEditor.__quill) {
                    // Quill이 초기화된 경우
                    quillEditor.__quill.root.innerHTML = memo;
                } else {
                    // Quill이 아직 초기화되지 않은 경우
                    editorArea.innerHTML = memo;
                    // Quill 초기화 시도
                    if (typeof window.board === 'function') {
                        window.board();
                        setTimeout(() => {
                            const qlEditor = editorArea.querySelector('.ql-editor');
                            if (qlEditor && qlEditor.__quill) {
                                qlEditor.__quill.root.innerHTML = memo;
                            }
                        }, 100);
                    }
                }
            }
        }
    }, 500); // Quill 초기화 대기 시간 증가
    
    // 여행자 정보
    const firstNameInput = document.getElementById('first_name');
    if (firstNameInput) {
        firstNameInput.value = customer.fName || '';
    }
    
    const lastNameInput = document.getElementById('last_name');
    if (lastNameInput) {
        lastNameInput.value = customer.lName || '';
    }
    
    // 호칭 (title 필드가 있으면)
    const titleSelect = document.getElementById('title');
    if (titleSelect) {
        if (customer.title) {
            // MR, MS, MRS, MISS 중 하나로 매핑
            const titleValue = customer.title.toUpperCase();
            if (['MR', 'MS', 'MRS', 'MISS'].includes(titleValue)) {
                titleSelect.value = titleValue;
            } else if (titleValue === 'M') {
                titleSelect.value = 'MR';
            } else {
                titleSelect.value = 'MR'; // 기본값
            }
        } else {
            titleSelect.value = 'MR'; // 기본값
        }
        // select 업데이트를 위해 change 이벤트 발생
        titleSelect.dispatchEvent(new Event('change'));
    }
    
    // 성별
    const genderSelect = document.getElementById('gender');
    if (genderSelect) {
        if (customer.gender) {
            const genderValue = customer.gender.toLowerCase();
            // Male, Female, Other로 매핑
            if (genderValue === 'male' || genderValue === '남성' || genderValue === 'm' || genderValue === 'male') {
                genderSelect.value = 'Male';
            } else if (genderValue === 'female' || genderValue === '여성' || genderValue === 'f' || genderValue === 'female') {
                genderSelect.value = 'Female';
            } else {
                genderSelect.value = 'Other';
            }
        } else {
            genderSelect.value = 'Male'; // 기본값
        }
        // select 업데이트를 위해 change 이벤트 발생
        genderSelect.dispatchEvent(new Event('change'));
    }
    
    // 나이 (dateOfBirth에서 계산)
    const ageInput = document.getElementById('age');
    if (ageInput) {
        if (customer.dateOfBirth) {
            const age = calculateAge(customer.dateOfBirth);
            if (age >= 0) {
                ageInput.value = age;
            } else {
                ageInput.value = '';
            }
        } else {
            ageInput.value = '';
        }
    }
    
    // 생년월일
    const birthInput = document.getElementById('birth');
    if (birthInput) {
        birthInput.value = customer.dateOfBirth ? formatDateYYYYMMDD(customer.dateOfBirth) : '';
    }
    
    // 출신국가
    const nationalityInput = document.getElementById('nationality');
    if (nationalityInput) {
        nationalityInput.value = customer.nationality || '';
    }
    
    // 여권번호
    const passportNoInput = document.getElementById('passport_no');
    if (passportNoInput) {
        passportNoInput.value = customer.passportNumber || '';
    }
    
    // 여권 발행일 (여러 가능한 필드명 확인)
    const passportIssueInput = document.getElementById('passport_issue');
    if (passportIssueInput) {
        const passportIssueDate = customer.passportIssueDate || customer.passportIssuedDate || customer.passportIssue || '';
        passportIssueInput.value = passportIssueDate ? formatDateYYYYMMDD(passportIssueDate) : '';
    }
    
    // 여권 만료일 (여러 가능한 필드명 확인)
    const passportExpireInput = document.getElementById('passport_expire');
    if (passportExpireInput) {
        const passportExpiry = customer.passportExpiry || customer.passportExpiryDate || customer.passportExp || '';
        passportExpireInput.value = passportExpiry ? formatDateYYYYMMDD(passportExpiry) : '';
    }
    
    // 여권 사진 (데이터가 없어도 처리)
    if (customer.profileImage) {
        // 상대 경로인 경우 전체 URL로 변환
        let imageUrl = customer.profileImage;
        console.log('Original profileImage:', imageUrl);
        
        if (imageUrl && !imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('data:')) {
            // smart-travel2 제거 및 경로 정규화
            imageUrl = imageUrl.replace('/smart-travel2/', '/');
            imageUrl = imageUrl.replace('smart-travel2/', '');
            
            // uploads/uploads 중복 제거
            imageUrl = imageUrl.replace(/\/uploads\/uploads\//g, '/uploads/');
            
            // 상대 경로를 전체 URL로 변환
            if (imageUrl.startsWith('/')) {
                // /로 시작하는 경우 (예: /uploads/passports/...)
                imageUrl = window.location.origin + imageUrl;
            } else if (imageUrl.startsWith('../')) {
                // ../www/uploads/passports/... 형식 처리
                imageUrl = window.location.origin + '/' + imageUrl.replace('../www/', '');
            } else {
                // uploads/passports/... 형식 처리
                imageUrl = window.location.origin + '/uploads/' + imageUrl;
            }
        }
        
        console.log('Converted imageUrl:', imageUrl);
        displayPassportImage(imageUrl);
    } else {
        console.log('No profileImage in customer data');
        // 여권 사진이 없으면 빈 상태로 표시
        const thumb = document.querySelector('.upload-box .thumb');
        const uploadMeta = document.querySelector('.upload-box .upload-meta');
        if (thumb) {
            thumb.style.backgroundImage = '';
            thumb.style.display = 'block'; // 기본 상태 유지 (회색 배경)
        }
        if (uploadMeta) {
            uploadMeta.style.display = 'none';
        }
    }
    
    // 예약 내역 렌더링
    renderBookings(bookings);
    
    // 문의 내역 렌더링
    renderInquiries(inquiries);
}

function renderBookings(bookings) {
    const tbody = document.getElementById('bookings-tbody');
    if (!tbody) return;
    
    if (bookings.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="is-center">${getText('noBookings')}</td></tr>`;
        return;
    }
    
    let html = '';
    bookings.forEach((booking, index) => {
        const bookingDate = booking.bookingDate ? formatDate(booking.bookingDate) : '-';
        const departureDate = booking.departureDate ? formatDate(booking.departureDate) : '-';
        const status = getBookingStatusText(booking.bookingStatus);
        const amount = booking.totalAmount ? formatCurrency(booking.totalAmount) : '0';
        
        html += `
            <tr style="cursor: pointer;" onclick="goToReservationDetail('${booking.bookingId}')">
                <td class="no is-center">${bookings.length - index}</td>
                <td class="ellipsis">${escapeHtml(booking.packageName || '-')}</td>
                <td class="is-center">${bookingDate}</td>
                <td class="is-center">${departureDate}</td>
                <td class="is-center">${status}</td>
                <td class="is-center">${booking.numPeople || 0}</td>
                <td class="is-center">${amount}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function renderInquiries(inquiries) {
    const tbody = document.getElementById('inquiries-tbody');
    if (!tbody) return;
    
    if (inquiries.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="is-center">${getText('noInquiries')}</td></tr>`;
        return;
    }
    
    let html = '';
    inquiries.forEach((inquiry, index) => {
        const inquiryDate = inquiry.createdAt ? formatDate(inquiry.createdAt) : '-';
        const inquiryType = getInquiryTypeText(inquiry.inquiryType);
        const status = getInquiryStatusText(inquiry.status);
        const replyStatus = inquiry.replyStatus || '미답변';
        
        html += `
            <tr style="cursor: pointer;" onclick="goToInquiryDetail('${inquiry.inquiryId}')">
                <td class="no is-center">${inquiries.length - index}</td>
                <td class="is-center">${inquiryType}</td>
                <td class="ellipsis">${escapeHtml(inquiry.inquiryTitle || '-')}</td>
                <td class="is-center">${inquiryDate}</td>
                <td class="is-center">${replyStatus}</td>
                <td class="is-center">${status}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function goToReservationDetail(bookingId) {
    window.location.href = `reservation-detail.php?id=${bookingId}`;
}

function goToInquiryDetail(inquiryId) {
    window.location.href = `inquiry-detail.html?id=${inquiryId}`;
}

async function handleSave() {
    try {
        // 고객명 (기본 정보)
        const customerName = document.getElementById('cust_name')?.value.trim() || '';
        const firstName = document.getElementById('first_name')?.value.trim() || '';
        const lastName = document.getElementById('last_name')?.value.trim() || '';
        const email = document.getElementById('cust_email')?.value.trim() || '';
        const phone = document.getElementById('cust_phone')?.value.trim() || '';
        const countryCode = document.getElementById('country_code')?.value || '+63';
        
        // 필수 필드 검증
        const errors = [];
        if (!customerName && (!firstName || !lastName)) {
            errors.push('고객명 또는 이름/성을 입력해주세요.');
        }
        if (!email) {
            errors.push('이메일을 입력해주세요.');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('올바른 이메일 형식을 입력해주세요.');
        }
        if (!phone) {
            errors.push('연락처를 입력해주세요.');
        }
        
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return;
        }
        
        // Note 에디터에서 내용 가져오기
        const editorArea = document.querySelector('.jweditor');
        let memo = '';
        if (editorArea) {
            // Quill 에디터의 경우 .ql-editor 내부의 내용을 가져옴
            const quillEditor = editorArea.querySelector('.ql-editor');
            if (quillEditor) {
                memo = quillEditor.innerHTML.trim();
            } else {
                // Quill이 아직 초기화되지 않은 경우
                memo = editorArea.innerHTML.trim();
            }
        }
        
        // 여행자 정보
        const title = document.getElementById('title')?.value || '';
        const gender = document.getElementById('gender')?.value || '';
        const age = document.getElementById('age')?.value || '';
        const birth = document.getElementById('birth')?.value.trim() || '';
        const nationality = document.getElementById('nationality')?.value.trim() || '';
        const passportNo = document.getElementById('passport_no')?.value.trim() || '';
        const passportIssue = document.getElementById('passport_issue')?.value.trim() || '';
        const passportExpire = document.getElementById('passport_expire')?.value.trim() || '';
        
        // 이름과 성이 없으면 고객명에서 분리 시도
        let finalFirstName = firstName;
        let finalLastName = lastName;
        if (!firstName && !lastName && customerName) {
            const nameParts = customerName.trim().split(/\s+/);
            if (nameParts.length >= 2) {
                finalFirstName = nameParts[0];
                finalLastName = nameParts.slice(1).join(' ');
            } else if (nameParts.length === 1) {
                finalFirstName = nameParts[0];
                finalLastName = '';
            }
        }
        
        const formData = new FormData();
        formData.append('action', 'updateCustomer');
        formData.append('accountId', currentAccountId);
        formData.append('firstName', finalFirstName);
        formData.append('lastName', finalLastName);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('countryCode', countryCode);
        formData.append('memo', memo);
        formData.append('title', title);
        formData.append('travelerGender', gender);
        formData.append('travelerAge', age);
        formData.append('travelerBirth', birth);
        formData.append('travelerNationality', nationality);
        formData.append('travelerPassportNo', passportNo);
        formData.append('travelerPassportIssue', passportIssue);
        formData.append('travelerPassportExpire', passportExpire);
        
        // 여권 사진 파일
        const passportFile = document.getElementById('file-passport')?.files[0];
        if (passportFile) {
            formData.append('passportPhoto', passportFile);
        }
        
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(getText('saved'));
            loadCustomerDetail(); // 재로드
        } else {
            alert(getText('saveFailed') + ': ' + result.message);
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert(getText('error'));
    }
}

async function handlePasswordReset() {
    if (!confirm(getText('confirmPasswordReset'))) {
        return;
    }
    
    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'resetPassword',
                accountId: currentAccountId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(getText('passwordResetSuccess'));
            document.getElementById('cust_pw').value = '';
        } else {
            alert(getText('passwordResetFailed') + ': ' + result.message);
        }
    } catch (error) {
        console.error('Error resetting password:', error);
        alert(getText('error'));
    }
}

function handleDownloadPassport() {
    // TODO: 여권 사진 다운로드 구현
    const imageSrc = document.querySelector('.upload-box .thumb')?.style.backgroundImage;
    if (imageSrc) {
        const url = imageSrc.replace('url("', '').replace('")', '');
        window.open(url, '_blank');
    }
}

function handleDeletePassport() {
    if (!confirm('여권 사진을 삭제하시겠습니까?')) {
        return;
    }
    
    // TODO: 서버에서 여권 사진 삭제 API 호출
    const thumb = document.querySelector('.upload-box .thumb');
    const uploadMeta = document.querySelector('.upload-box .upload-meta');
    if (thumb) {
        thumb.style.backgroundImage = '';
        thumb.style.display = 'none';
    }
    if (uploadMeta) {
        uploadMeta.style.display = 'none';
    }
    
    document.getElementById('file-passport').value = '';
}

function handlePassportFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        alert('이미지 파일만 업로드 가능합니다.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        displayPassportImage(e.target.result);
    };
    reader.readAsDataURL(file);
}

function displayPassportImage(imageSrc) {
    const thumb = document.querySelector('.upload-box .thumb');
    const uploadMeta = document.querySelector('.upload-box .upload-meta');
    
    console.log('displayPassportImage called with:', imageSrc);
    
    if (!thumb || !uploadMeta) {
        console.error('upload-box elements not found');
        return;
    }
    
    if (imageSrc && imageSrc !== 'null' && imageSrc !== 'undefined' && imageSrc.trim() !== '') {
        // 이미지 로드 테스트
        const img = new Image();
        img.onload = function() {
            console.log('Image loaded successfully:', imageSrc);
            if (thumb) {
                thumb.style.backgroundImage = `url("${imageSrc}")`;
                thumb.style.display = 'block';
                thumb.style.backgroundSize = 'cover';
                thumb.style.backgroundPosition = 'center';
                thumb.style.width = '110px';
                thumb.style.height = '110px';
                thumb.style.minWidth = '110px';
                thumb.style.minHeight = '110px';
                thumb.style.borderRadius = '10px';
                thumb.style.overflow = 'hidden';
            }
            
            if (uploadMeta) {
                uploadMeta.style.display = 'flex';
                // 파일 정보 업데이트 (있는 경우)
                const fileInfo = uploadMeta.querySelector('.file-info');
                if (fileInfo) {
                    // URL에서 파일명 추출
                    try {
                        const url = new URL(imageSrc);
                        const fileName = url.pathname.split('/').pop() || '이미지';
                        const extension = fileName.split('.').pop()?.toLowerCase() || 'jpg';
                        // 파일 크기 추정 (실제로는 서버에서 가져와야 함)
                        fileInfo.textContent = `${extension}, 이미지`;
                    } catch (e) {
                        // URL 파싱 실패 시 기본값 (상대 경로인 경우)
                        const fileName = imageSrc.split('/').pop() || '이미지';
                        const extension = fileName.split('.').pop()?.toLowerCase() || 'jpg';
                        fileInfo.textContent = `${extension}, 이미지`;
                    }
                }
            }
        };
        img.onerror = function() {
            console.error('Failed to load image:', imageSrc);
            // 이미지 로드 실패 시에도 기본 스타일 유지
            if (thumb) {
                thumb.style.backgroundImage = '';
                thumb.style.display = 'block';
            }
            if (uploadMeta) {
                uploadMeta.style.display = 'none';
            }
        };
        img.src = imageSrc;
    } else {
        console.log('No image source provided or invalid:', imageSrc);
        if (thumb) {
            thumb.style.backgroundImage = '';
            thumb.style.display = 'block';
        }
        if (uploadMeta) {
            uploadMeta.style.display = 'none';
        }
    }
}

// 유틸리티 함수들
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

function formatDateYYYYMMDD(dateString) {
    if (!dateString) return '';
    
    // 이미 YYYYMMDD 형식인 경우
    if (typeof dateString === 'string' && /^\d{8}$/.test(dateString)) {
        return dateString;
    }
    
    // YYYY-MM-DD 형식인 경우
    if (typeof dateString === 'string' && /^\d{4}-\d{2}-\d{2}/.test(dateString)) {
        return dateString.replace(/-/g, '').substring(0, 8);
    }
    
    // Date 객체나 다른 형식인 경우
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            console.warn('Invalid date:', dateString);
            return '';
        }
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}${month}${day}`;
    } catch (e) {
        console.error('Error formatting date:', dateString, e);
        return '';
    }
}

function formatDateTime(date) {
    if (!date) return '';
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US').format(amount);
}

function calculateAge(dateOfBirth) {
    if (!dateOfBirth) return 0;
    const birth = new Date(dateOfBirth);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

function getBookingStatusText(status) {
    const statusMap = {
        'confirmed': '예약 확정',
        'pending': '대기중',
        'cancelled': '취소됨',
        'completed': '완료됨'
    };
    return statusMap[status] || status;
}

function getInquiryTypeText(type) {
    const typeMap = {
        'product': '상품 문의',
        'booking': '예약 문의',
        'payment': '결제 문의',
        'general': '일반 문의',
        'complaint': '불만 접수'
    };
    return typeMap[type] || type;
}

function getInquiryStatusText(status) {
    const statusMap = {
        'pending': '접수됨',
        'processing': '처리중',
        'resolved': '해결됨',
        'closed': '종료됨'
    };
    return statusMap[status] || status;
}

function showLoading() {
    // 로딩 상태 표시 (필요시 구현)
}

function hideLoading() {
    // 로딩 종료 (필요시 구현)
}

function showError(message) {
    alert(message);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
