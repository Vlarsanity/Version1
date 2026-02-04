/**
 * Agent Admin - Customer Register Page JavaScript
 */

let passportPhotoFile = null;

// 다국어 텍스트
const texts = {
    ko: {
        imageOnly: '이미지 파일만 업로드 가능합니다.',
        requiredFields: '고객명, 이메일, 연락처는 필수 항목입니다.',
        enterCustomerName: '고객명을 입력해주세요.',
        invalidEmail: '올바른 이메일 형식을 입력해주세요.',
        invalidBirthDate: '생년월일은 YYYYMMDD 형식으로 입력해주세요.',
        invalidPassportIssue: '여권 발행일은 YYYYMMDD 형식으로 입력해주세요.',
        invalidPassportExpire: '여권 만료일은 YYYYMMDD 형식으로 입력해주세요.',
        saved: '고객이 등록되었습니다.',
        saveFailed: '고객 등록에 실패했습니다: ',
        error: '고객 등록 중 오류가 발생했습니다: ',
        testData: '테스트 데이터 입력',
        fillTestData: 'Fill Test Data',
        testDataFilled: ' - 모든 필드가 채워졌습니다.'
    },
    en: {
        imageOnly: 'Only image files can be uploaded.',
        requiredFields: 'Customer name, email, and contact are required fields.',
        enterCustomerName: 'Please enter customer name.',
        invalidEmail: 'Please enter a valid email format.',
        invalidBirthDate: 'Date of birth must be in YYYYMMDD format.',
        invalidPassportIssue: 'Passport issue date must be in YYYYMMDD format.',
        invalidPassportExpire: 'Passport expiry date must be in YYYYMMDD format.',
        saved: 'Customer has been registered.',
        saveFailed: 'Customer registration failed: ',
        error: 'An error occurred while registering customer: ',
        parseError: 'Unable to parse server response.',
        unknownError: 'Unknown error',
        testData: 'Fill Test Data',
        testDataFilled: ' - All fields have been filled.'
    }
};

function getCurrentLang() {
    return getCookie('lang') || 'eng';
}

function getText(key) {
    const lang = getCurrentLang();
    const langTexts = lang === 'eng' ? texts.en : texts.ko;
    return langTexts[key] || key;
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    // 저장 버튼 이벤트
    const saveButton = document.getElementById('saveBtn') || document.querySelector('.page-toolbar-actions .jw-button.typeB');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }
    
    // 테스트 데이터 버튼 이벤트 (있는 경우)
    const testDataBtn = document.getElementById('testDataBtn');
    if (testDataBtn) {
        testDataBtn.addEventListener('click', fillTestData);
    }
    
    // 비밀번호 자동 입력 버튼
    const autoPasswordBtn = document.querySelector('.input-box .jw-button.typeD');
    if (autoPasswordBtn) {
        autoPasswordBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('cust_pw');
            if (passwordInput) {
                passwordInput.value = generateRandomPassword();
            }
        });
    }
    
    // 여권 사진 업로드
    const passportPhotoInput = document.getElementById('passport_photo');
    const passportPhotoThumb = document.querySelector('.upload-box .thumb');
    const passportPhotoMeta = document.querySelector('.upload-box .upload-meta');
    const passportPhotoInfo = document.getElementById('passport_photo_info');
    const passportPhotoDownload = document.getElementById('passport_photo_download');
    const passportPhotoRemove = document.getElementById('passport_photo_remove');
    
    if (passportPhotoInput) {
        passportPhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert(getText('imageOnly') || '이미지 파일만 업로드 가능합니다.');
                    return;
                }
                
                passportPhotoFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (passportPhotoThumb) {
                        passportPhotoThumb.style.backgroundImage = `url("${e.target.result}")`;
                        passportPhotoThumb.style.backgroundSize = 'cover';
                        passportPhotoThumb.style.backgroundPosition = 'center';
                    }
                    if (passportPhotoMeta) {
                        passportPhotoMeta.style.display = 'flex';
                    }
                    if (passportPhotoInfo) {
                        const fileSize = (file.size / 1024).toFixed(0);
                        const extension = file.name.split('.').pop()?.toLowerCase() || 'jpg';
                        passportPhotoInfo.textContent = `Image ${extension}, ${fileSize}KB`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
        
        if (passportPhotoRemove) {
            passportPhotoRemove.addEventListener('click', function() {
                passportPhotoFile = null;
                passportPhotoInput.value = '';
                if (passportPhotoThumb) {
                    passportPhotoThumb.style.backgroundImage = '';
                }
                if (passportPhotoMeta) {
                    passportPhotoMeta.style.display = 'none';
                }
            });
        }
        
        if (passportPhotoDownload) {
            passportPhotoDownload.addEventListener('click', function() {
                if (passportPhotoFile) {
                    const url = URL.createObjectURL(passportPhotoFile);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = passportPhotoFile.name;
                    a.click();
                    URL.revokeObjectURL(url);
                }
            });
        }
    }
});

function generateRandomPassword() {
    const length = 12;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    return password;
}

function generateRandomString(length) {
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    return result;
}

function generateRandomNumber(length) {
    let result = '';
    for (let i = 0; i < length; i++) {
        result += Math.floor(Math.random() * 10);
    }
    return result;
}

function generateRandomDate(startYear = 1950, endYear = 2005) {
    const year = Math.floor(Math.random() * (endYear - startYear + 1)) + startYear;
    const month = String(Math.floor(Math.random() * 12) + 1).padStart(2, '0');
    const day = String(Math.floor(Math.random() * 28) + 1).padStart(2, '0');
    return `${year}${month}${day}`;
}

function generatePassportNumber() {
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const letter1 = letters[Math.floor(Math.random() * letters.length)];
    const letter2 = letters[Math.floor(Math.random() * letters.length)];
    const numbers = generateRandomNumber(7);
    return `${letter1}${letter2}${numbers}`;
}

function fillTestData() {
    // 기본 정보
    const firstName = generateRandomString(5);
    const lastName = generateRandomString(6);
    const fullName = `${firstName} ${lastName}`;
    
    // 고객명
    const custNameInput = document.getElementById('cust_name');
    if (custNameInput) custNameInput.value = fullName;
    
    // 국가 코드는 랜덤 선택
    const countryCodes = ['+63', '+82', '+81', '+1'];
    const randomCountryCode = countryCodes[Math.floor(Math.random() * countryCodes.length)];
    const countryCodeSelect = document.getElementById('country_code');
    if (countryCodeSelect) {
        countryCodeSelect.value = randomCountryCode;
        countryCodeSelect.dispatchEvent(new Event('change'));
    }
    
    // 연락처 (10자리 숫자)
    const phoneInput = document.getElementById('cust_phone');
    if (phoneInput) phoneInput.value = generateRandomNumber(10);
    
    // 이메일
    const email = `test${generateRandomNumber(5)}@example.com`;
    const emailInput = document.getElementById('cust_email');
    if (emailInput) emailInput.value = email;
    
    // 비밀번호
    const passwordInput = document.getElementById('cust_pw');
    if (passwordInput) passwordInput.value = generateRandomPassword();
    
    // Note (에디터) - Quill 에디터에 내용 삽입
    const noteKo = `테스트 메모입니다. 고객 등록 테스트를 위해 생성되었습니다. 생성 시간: ${new Date().toLocaleString('ko-KR')}`;
    const noteEn = `Test memo for customer registration. Created at ${new Date().toLocaleString('en-US')}`;
    const note = getCurrentLang() === 'eng' ? noteEn : noteKo;
    
    // Quill 에디터에 내용 삽입
    setTimeout(() => {
        const editorArea = document.querySelector('.jweditor');
        if (editorArea) {
            const editorRoot = editorArea.closest('.jw-editor');
            if (editorRoot) {
                // Quill 인스턴스 찾기
                const quillEditor = editorArea.querySelector('.ql-editor');
                if (quillEditor && quillEditor.__quill) {
                    // Quill이 초기화된 경우
                    quillEditor.__quill.root.innerHTML = `<p>${note}</p>`;
                } else {
                    // Quill이 아직 초기화되지 않은 경우
                    editorArea.innerHTML = `<p>${note}</p>`;
                    // Quill 초기화 시도
                    if (typeof window.board === 'function') {
                        window.board();
                        setTimeout(() => {
                            const qlEditor = editorArea.querySelector('.ql-editor');
                            if (qlEditor && qlEditor.__quill) {
                                qlEditor.__quill.root.innerHTML = `<p>${note}</p>`;
                            }
                        }, 100);
                    }
                }
            }
        }
    }, 100);
    
    // 여행자 정보
    // 호칭 (mr 또는 ms만 가능)
    const titles = ['mr', 'ms'];
    const randomTitle = titles[Math.floor(Math.random() * titles.length)];
    const titleSelect = document.getElementById('title');
    if (titleSelect) {
        titleSelect.value = randomTitle;
        titleSelect.dispatchEvent(new Event('change'));
    }
    
    const firstNameInput = document.getElementById('first_name');
    if (firstNameInput) firstNameInput.value = firstName;
    
    const lastNameInput = document.getElementById('last_name');
    if (lastNameInput) lastNameInput.value = lastName;
    
    // 성별 (male 또는 female만 가능)
    const genders = ['male', 'female'];
    const randomGender = genders[Math.floor(Math.random() * genders.length)];
    const genderSelect = document.getElementById('gender');
    if (genderSelect) {
        genderSelect.value = randomGender;
        genderSelect.dispatchEvent(new Event('change'));
    }
    
    // 나이 (20-60)
    const age = Math.floor(Math.random() * 41) + 20;
    const ageInput = document.getElementById('age');
    if (ageInput) ageInput.value = age;
    
    // 생년월일 (나이에 맞게)
    const currentYear = new Date().getFullYear();
    const birthYear = currentYear - age;
    const birthDate = generateRandomDate(birthYear - 1, birthYear);
    const birthInput = document.getElementById('birth');
    if (birthInput) birthInput.value = birthDate;
    
    // 출신국가
    const nationalities = ['Philippines', 'South Korea', 'Japan', 'United States', 'China', 'Thailand', 'Vietnam'];
    const randomNationality = nationalities[Math.floor(Math.random() * nationalities.length)];
    const nationalityInput = document.getElementById('nationality');
    if (nationalityInput) nationalityInput.value = randomNationality;
    
    // 여권번호
    const passportNoInput = document.getElementById('passport_no');
    if (passportNoInput) passportNoInput.value = generatePassportNumber();
    
    // 여권 발행일 (생년월일 이후, 18세 이후)
    const birthYearNum = parseInt(birthDate.substring(0, 4));
    const passportIssueYear = birthYearNum + 18;
    const passportIssueDate = generateRandomDate(passportIssueYear, passportIssueYear + 5);
    const passportIssueInput = document.getElementById('passport_issue');
    if (passportIssueInput) passportIssueInput.value = passportIssueDate;
    
    // 여권 만료일 (발행일 이후 10년)
    const passportIssueYearNum = parseInt(passportIssueDate.substring(0, 4));
    const passportExpireYear = passportIssueYearNum + 10;
    const passportExpireDate = generateRandomDate(passportExpireYear - 1, passportExpireYear);
    const passportExpireInput = document.getElementById('passport_expire');
    if (passportExpireInput) passportExpireInput.value = passportExpireDate;
    
    // select 필드 업데이트 (jw_select 함수가 있다면 호출)
    if (typeof jw_select === 'function') {
        jw_select();
    }
    
    alert(getText('testData') + getText('testDataFilled'));
}

async function handleSave() {
    try {
        // 기본 정보 수집
        const customerName = document.getElementById('cust_name')?.value.trim() || '';
        const countryCode = document.getElementById('country_code')?.value || '+63';
        const phone = document.getElementById('cust_phone')?.value.trim() || '';
        const email = document.getElementById('cust_email')?.value.trim() || '';
        const passwordInput = document.getElementById('cust_pw');
        const password = passwordInput?.value || '';
        // Note 에디터에서 내용 가져오기
        const editorArea = document.querySelector('.jweditor');
        let memo = '';
        if (editorArea) {
            // Quill 에디터의 경우 .ql-editor 내부의 내용을 가져옴
            const quillEditor = editorArea.querySelector('.ql-editor');
            if (quillEditor) {
                memo = quillEditor.innerHTML;
            } else {
                // Quill이 아직 초기화되지 않은 경우
                memo = editorArea.innerHTML;
            }
        }
        
        // 필수 필드 검증
        if (!customerName || !email || !phone) {
            alert(getText('requiredFields'));
            return;
        }
        
        const nameParts = customerName.trim().split(' ');
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(' ') || '';
        
        if (!firstName) {
            alert(getText('enterCustomerName'));
            return;
        }
        
        // 이메일 형식 검증
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert(getText('invalidEmail'));
            return;
        }
        
        // 여행자 정보 수집
        const title = document.getElementById('title')?.value || 'MR';
        const travelerFirstName = document.getElementById('first_name')?.value.trim() || '';
        const travelerLastName = document.getElementById('last_name')?.value.trim() || '';
        const gender = document.getElementById('gender')?.value || 'male';
        const age = document.getElementById('age')?.value || '';
        const birth = document.getElementById('birth')?.value.trim() || '';
        const nationality = document.getElementById('nationality')?.value.trim() || '';
        const passportNo = document.getElementById('passport_no')?.value.trim() || '';
        const passportIssue = document.getElementById('passport_issue')?.value.trim() || '';
        const passportExpire = document.getElementById('passport_expire')?.value.trim() || '';
        
        // 날짜 형식 검증 (YYYYMMDD)
        const dateRegex = /^\d{8}$/;
        if (birth && !dateRegex.test(birth)) {
            alert(getText('invalidBirthDate'));
            return;
        }
        if (passportIssue && !dateRegex.test(passportIssue)) {
            alert(getText('invalidPassportIssue'));
            return;
        }
        if (passportExpire && !dateRegex.test(passportExpire)) {
            alert(getText('invalidPassportExpire'));
            return;
        }
        
        // 날짜를 DB 형식으로 변환 (YYYY-MM-DD)
        const formatDate = (dateStr) => {
            if (!dateStr || dateStr.length !== 8) return null;
            return `${dateStr.substring(0, 4)}-${dateStr.substring(4, 6)}-${dateStr.substring(6, 8)}`;
        };
        
        // FormData 생성 (여권 사진 포함)
        const formData = new FormData();
        formData.append('action', 'createCustomer');
        formData.append('firstName', firstName);
        formData.append('lastName', lastName);
        formData.append('email', email);
        formData.append('countryCode', countryCode);
        formData.append('phone', phone);
        formData.append('password', password || generateRandomPassword());
        formData.append('memo', memo);
        
        // 여행자 정보
        formData.append('travelerTitle', title);
        formData.append('travelerFirstName', travelerFirstName);
        formData.append('travelerLastName', travelerLastName);
        formData.append('travelerGender', gender);
        formData.append('travelerAge', age);
        formData.append('travelerBirth', formatDate(birth) || '');
        formData.append('travelerNationality', nationality);
        formData.append('travelerPassportNo', passportNo);
        formData.append('travelerPassportIssue', formatDate(passportIssue) || '');
        formData.append('travelerPassportExpire', formatDate(passportExpire) || '');
        
        // 여권 사진 업로드
        if (passportPhotoFile) {
            console.log('Adding passport photo to formData:', passportPhotoFile.name, passportPhotoFile.size, 'bytes');
            formData.append('passportPhoto', passportPhotoFile);
        } else {
            console.log('No passport photo file to upload');
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
            throw new Error(getText('parseError'));
        }
        
        if (result.success) {
            window.location.href = 'customer-list.html';
        } else {
            alert(getText('saveFailed') + (result.message || getText('unknownError')));
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert(getText('error') + error.message);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
