/**
 * Agent Admin - Register Inquiry Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // 파일 업로드 초기화
    initFileUpload();
    
    // 저장 버튼 이벤트
    const saveButton = document.getElementById('saveInquiryBtn');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }
});

// 선택된 파일들을 추적하는 배열
let selectedFiles = [];

// 파일 업로드 초기화
function initFileUpload() {
    const fileUploadBtn = document.getElementById('file-upload-btn');
    const fileInput = document.getElementById('file-upload');
    const attachList = document.getElementById('attach-list');
    
    if (!fileUploadBtn || !fileInput || !attachList) return;
    
    // 파일 업로드 버튼 클릭
    fileUploadBtn.addEventListener('click', function() {
        fileInput.click();
    });
    
    // 파일 선택
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            // 중복 체크
            if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                selectedFiles.push(file);
                addFileToList(file, selectedFiles.length - 1);
            }
        });
        // 같은 파일을 다시 선택할 수 있도록 리셋
        e.target.value = '';
    });
    
    // 파일 목록에 추가
    function addFileToList(file, index) {
        const fileItem = document.createElement('div');
        fileItem.className = 'attach-item';
        fileItem.dataset.fileIndex = index;
        fileItem.dataset.fileName = file.name;
        
        // 파일 크기 포맷팅
        const formatFileSize = (bytes) => {
            if (!bytes) return '';
            if (bytes < 1024) return bytes + 'B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + 'KB';
            return (bytes / (1024 * 1024)).toFixed(0) + 'MB';
        };
        
        fileItem.innerHTML = `
            <span class="attach-item-name">${escapeHtml(file.name)} (${formatFileSize(file.size)})</span>
            <button type="button" class="attach-item-remove" aria-label="삭제">×</button>
        `;
        
        // 삭제 버튼 이벤트
        const removeBtn = fileItem.querySelector('.attach-item-remove');
        removeBtn.addEventListener('click', function() {
            const fileIndex = parseInt(fileItem.dataset.fileIndex);
            selectedFiles.splice(fileIndex, 1);
            fileItem.remove();
            // 인덱스 재설정
            updateFileIndices();
        });
        
        attachList.appendChild(fileItem);
    }
    
    // 파일 인덱스 업데이트
    function updateFileIndices() {
        const fileItems = attachList.querySelectorAll('.attach-item');
        fileItems.forEach((item, index) => {
            item.dataset.fileIndex = index;
        });
    }
}

// 저장 처리
async function handleSave() {
    try {
        const titleInput = document.getElementById('q-title');
        const editorArea = document.querySelector('.jw-editor .jweditor');
        
        if (!titleInput?.value.trim()) {
            alert('제목을 입력해주세요.');
            titleInput.focus();
            return;
        }
        
        const content = editorArea?.innerHTML || '';
        if (!content.trim()) {
            alert('내용을 입력해주세요.');
            editorArea?.focus();
            return;
        }
        
        // FormData 생성 (파일 업로드를 위해)
        const formData = new FormData();
        formData.append('action', 'registerInquiry');
        formData.append('inquiryTitle', titleInput.value.trim());
        formData.append('inquiryContent', content);
        
        // 선택된 파일들을 FormData에 추가
        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                formData.append(`file_${index}`, file);
            });
        }
        
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('문의가 등록되었습니다.');
            window.location.href = 'inquiry-list.html';
        } else {
            alert('문의 등록에 실패했습니다: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert('문의 등록 중 오류가 발생했습니다.');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
