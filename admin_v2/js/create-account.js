/**
 * Create Account Page JavaScript
 * Handles account creation with automatic accounts + agent/employee/guide table insertion
 */

document.addEventListener('DOMContentLoaded', function() {
    // Account Type 변경 시 섹션 표시/숨김
    const accountTypeSelect = document.getElementById('accountType');
    const agentSection = document.getElementById('agentSection');
    const employeeSection = document.getElementById('employeeSection');
    const guideSection = document.getElementById('guideSection');

    if (accountTypeSelect) {
        accountTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;

            // 모든 섹션 숨김
            if (agentSection) agentSection.style.display = 'none';
            if (employeeSection) employeeSection.style.display = 'none';
            if (guideSection) guideSection.style.display = 'none';

            // 선택된 타입에 따라 섹션 표시
            if (selectedType === 'agent' && agentSection) {
                agentSection.style.display = 'block';
            } else if (selectedType === 'employee' && employeeSection) {
                employeeSection.style.display = 'block';
            } else if (selectedType === 'guide' && guideSection) {
                guideSection.style.display = 'block';
            }
        });
    }

    // Display Name 자동 생성
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');
    const middleNameInput = document.getElementById('middleName');
    const displayNameInput = document.getElementById('displayName');

    function updateDisplayName() {
        if (displayNameInput && firstNameInput && lastNameInput) {
            const firstName = firstNameInput.value.trim();
            const middleName = middleNameInput ? middleNameInput.value.trim() : '';
            const lastName = lastNameInput.value.trim();

            const parts = [firstName, middleName, lastName].filter(p => p.length > 0);
            displayNameInput.value = parts.join(' ');
        }
    }

    if (firstNameInput) firstNameInput.addEventListener('input', updateDisplayName);
    if (lastNameInput) lastNameInput.addEventListener('input', updateDisplayName);
    if (middleNameInput) middleNameInput.addEventListener('input', updateDisplayName);

    // Password 자동 생성
    const generatePasswordBtn = document.getElementById('generatePasswordBtn');
    const passwordInput = document.getElementById('password');

    if (generatePasswordBtn && passwordInput) {
        generatePasswordBtn.addEventListener('click', function() {
            passwordInput.value = generateRandomPassword();
        });
    }

    // 테스트 데이터 입력
    const testDataBtn = document.getElementById('testDataBtn');
    if (testDataBtn) {
        testDataBtn.addEventListener('click', fillTestData);
    }

    // 계정 생성 버튼
    const createBtn = document.getElementById('createBtn');
    if (createBtn) {
        createBtn.addEventListener('click', handleCreateAccount);
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
    const charset = 'abcdefghijklmnopqrstuvwxyz';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    return result;
}

function fillTestData() {
    const accountTypes = ['admin', 'agent', 'employee', 'guide'];
    const randomType = accountTypes[Math.floor(Math.random() * accountTypes.length)];

    const firstName = generateRandomString(5);
    const lastName = generateRandomString(6);
    const middleName = generateRandomString(4);
    const username = firstName + lastName + Math.floor(Math.random() * 100);
    const email = username + '@example.com';

    document.getElementById('accountType').value = randomType;
    document.getElementById('accountType').dispatchEvent(new Event('change'));

    document.getElementById('firstName').value = firstName.charAt(0).toUpperCase() + firstName.slice(1);
    document.getElementById('lastName').value = lastName.charAt(0).toUpperCase() + lastName.slice(1);
    document.getElementById('middleName').value = middleName.charAt(0).toUpperCase() + middleName.slice(1);
    document.getElementById('username').value = username;
    document.getElementById('email').value = email;
    document.getElementById('password').value = generateRandomPassword();

    // Display Name 자동 업데이트
    const firstNameInput = document.getElementById('firstName');
    if (firstNameInput) {
        firstNameInput.dispatchEvent(new Event('input'));
    }

    alert('테스트 데이터가 입력되었습니다.');
}

async function handleCreateAccount() {
    try {
        // 필수 필드 검증
        const accountType = document.getElementById('accountType').value;
        const firstName = document.getElementById('firstName').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const middleName = document.getElementById('middleName').value.trim();
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const displayName = document.getElementById('displayName').value.trim();

        if (!accountType || !firstName || !lastName || !username || !email || !password) {
            showMessage('모든 필수 필드를 입력해주세요.', 'error');
            return;
        }

        // 이메일 형식 검증
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showMessage('올바른 이메일 형식을 입력해주세요.', 'error');
            return;
        }

        // 데이터 수집
        const data = {
            accountType,
            firstName,
            lastName,
            middleName,
            username,
            email,
            password,
            displayName
        };

        // Account Type별 추가 정보
        if (accountType === 'agent') {
            data.agentType = document.getElementById('agentType').value;
            data.agentRole = document.getElementById('agentRole').value;
        } else if (accountType === 'employee') {
            data.position = document.getElementById('position').value.trim();
            data.branch = document.getElementById('branch').value;
        } else if (accountType === 'guide') {
            data.phoneNumber = document.getElementById('phoneNumber').value.trim();
            data.languages = document.getElementById('languages').value.trim();
            data.experienceYears = document.getElementById('experienceYears').value;
        }

        // 버튼 비활성화
        const createBtn = document.getElementById('createBtn');
        createBtn.disabled = true;
        createBtn.textContent = 'Creating...';

        // API 호출
        const response = await fetch('../backend/api/create-account-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showMessage(result.message || '계정이 성공적으로 생성되었습니다.', 'success');

            // 폼 초기화
            setTimeout(() => {
                resetForm();
            }, 2000);
        } else {
            showMessage(result.message || '계정 생성에 실패했습니다.', 'error');
            createBtn.disabled = false;
            createBtn.textContent = '계정 생성';
        }
    } catch (error) {
        console.error('Error creating account:', error);
        showMessage('계정 생성 중 오류가 발생했습니다: ' + error.message, 'error');

        const createBtn = document.getElementById('createBtn');
        createBtn.disabled = false;
        createBtn.textContent = '계정 생성';
    }
}

function showMessage(message, type) {
    const messageBox = document.getElementById('messageBox');
    if (!messageBox) return;

    messageBox.className = 'message-box jw-mgt32 ' + (type === 'success' ? 'success' : 'error');
    messageBox.textContent = message;
    messageBox.style.display = 'block';

    // 3초 후 자동 숨김
    setTimeout(() => {
        messageBox.style.display = 'none';
    }, 5000);
}

function resetForm() {
    // 폼 초기화
    document.getElementById('accountType').value = '';
    document.getElementById('firstName').value = '';
    document.getElementById('lastName').value = '';
    document.getElementById('middleName').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('displayName').value = '';

    // 섹션 숨김
    document.getElementById('agentSection').style.display = 'none';
    document.getElementById('employeeSection').style.display = 'none';
    document.getElementById('guideSection').style.display = 'none';

    // 버튼 활성화
    const createBtn = document.getElementById('createBtn');
    createBtn.disabled = false;
    createBtn.textContent = '계정 생성';
}
