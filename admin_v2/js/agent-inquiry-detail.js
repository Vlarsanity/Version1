/**
 * Agent Admin - Inquiry Detail Page JavaScript
 */

let currentInquiryId = null;
let isAnswered = false;
let currentAttachments = [];
let selectedFiles = []; // 새로 추가된 파일들 추적

document.addEventListener('DOMContentLoaded', function() {
    // URL에서 inquiryId 가져오기
    const urlParams = new URLSearchParams(window.location.search);
    currentInquiryId = urlParams.get('id') || urlParams.get('inquiryId');
    
    if (currentInquiryId) {
        loadInquiryDetail();
    } else {
        showError('문의 ID가 없습니다.');
    }
    
    // 저장 버튼 이벤트
    const saveButton = document.getElementById('save-btn');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }
    
    // 파일 업로드 버튼 이벤트
    const fileUploadBtn = document.getElementById('file-upload-btn');
    const fileUploadInput = document.getElementById('file-upload');
    if (fileUploadBtn && fileUploadInput) {
        fileUploadBtn.addEventListener('click', () => {
            fileUploadInput.click();
        });
        fileUploadInput.addEventListener('change', handleFileUpload);
    }
});

async function loadInquiryDetail() {
    try {
        showLoading();
        
        const response = await fetch(`../backend/api/agent-api.php?action=getInquiryDetail&inquiryId=${currentInquiryId}`);
        const result = await response.json();
        
        if (result.success) {
            renderInquiryDetail(result.data);
        } else {
            showError('문의 정보를 불러오는데 실패했습니다: ' + result.message);
        }
    } catch (error) {
        console.error('Error loading inquiry detail:', error);
        showError('문의 정보를 불러오는 중 오류가 발생했습니다.');
    } finally {
        hideLoading();
    }
}

function renderInquiryDetail(data) {
    const inquiry = data.inquiry;
    
    // 디버깅: 데이터 확인
    console.log('Inquiry data:', inquiry);
    
    // 답변 여부 확인 (replyContent가 있고, status가 completed인 경우)
    isAnswered = !!(inquiry.replyContent && (inquiry.status === 'completed' || inquiry.status === 'answered' || inquiry.repliedAt));
    
    // 상태에 따른 UI 조정
    setupUIForStatus(isAnswered);
    
    // 작성일시
    const createdAtInput = document.getElementById('created_at');
    if (createdAtInput && inquiry.createdAt) {
        createdAtInput.value = formatDateTime(inquiry.createdAt);
    }
    
    // 문의 제목
    const titleInput = document.getElementById('title');
    if (titleInput && inquiry.inquiryTitle) {
        titleInput.value = inquiry.inquiryTitle;
        if (isAnswered) {
            titleInput.readOnly = true;
        }
    }
    
    // 문의 내용 (에디터 콘텐츠)
    const contentEditor = document.getElementById('content');
    if (contentEditor && inquiry.inquiryContent) {
        contentEditor.innerHTML = inquiry.inquiryContent;
    }
    
    // 첨부파일 렌더링
    currentAttachments = inquiry.attachments || [];
    if (currentAttachments.length > 0) {
        renderAttachments(currentAttachments, !isAnswered);
    }
    
    // 답변 내용 (있는 경우)
    if (isAnswered) {
        const replyContentEditor = document.getElementById('reply_content');
        if (replyContentEditor && inquiry.replyContent) {
            replyContentEditor.innerHTML = inquiry.replyContent;
        }
        
        const repliedAtInput = document.getElementById('replied_at');
        if (repliedAtInput && inquiry.repliedAt) {
            repliedAtInput.value = formatDateTime(inquiry.repliedAt);
        }
    }
}

function setupUIForStatus(answered) {
    // 저장 버튼 표시/숨김
    const saveBtn = document.getElementById('save-btn');
    if (saveBtn) {
        saveBtn.style.display = answered ? 'none' : 'inline-block';
    }
    
    // 제목 편집 가능 여부
    const titleInput = document.getElementById('title');
    if (titleInput) {
        titleInput.readOnly = answered;
    }
    
    // 에디터 툴바 표시/숨김
    const editorToolbar = document.getElementById('editor-toolbar');
    const contentEditor = document.getElementById('content');
    if (editorToolbar && contentEditor) {
        if (answered) {
            editorToolbar.style.display = 'none';
            contentEditor.contentEditable = 'false';
            contentEditor.style.background = '#F3F3F3';
        } else {
            editorToolbar.style.display = 'flex';
            contentEditor.contentEditable = 'true';
            contentEditor.style.background = '#fff';
            initRichTextEditor();
        }
    }
    
    // 파일 업로드 버튼 표시/숨김
    const uploadBtnWrap = document.getElementById('upload-btn-wrap');
    if (uploadBtnWrap) {
        uploadBtnWrap.style.display = answered ? 'none' : 'block';
    }
    
    // 답변 섹션 표시/숨김
    const replySectionTitle = document.getElementById('reply-section-title');
    const replySection = document.getElementById('reply-section');
    if (replySectionTitle && replySection) {
        replySectionTitle.style.display = answered ? 'block' : 'none';
        replySection.style.display = answered ? 'block' : 'none';
    }
}

function initRichTextEditor() {
    const editorContent = document.getElementById('content');
    const toolbarBtns = document.querySelectorAll('.toolbar-btn');
    const styleSelect = document.getElementById('editor-style');
    const fontSelect = document.getElementById('editor-font');
    const sizeSelect = document.getElementById('editor-size');
    
    if (!editorContent) return;
    
    // 툴바 버튼 이벤트
    toolbarBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const cmd = this.dataset.cmd;
            if (cmd) {
                executeCommand(cmd, this);
            }
        });
    });
    
    // 스타일 선택
    if (styleSelect) {
        styleSelect.addEventListener('change', function() {
            const value = this.value;
            editorContent.focus();
            document.execCommand('formatBlock', false, value);
        });
    }
    
    // 폰트 선택
    if (fontSelect) {
        fontSelect.addEventListener('change', function() {
            const value = this.value;
            editorContent.focus();
            document.execCommand('fontName', false, value);
        });
    }
    
    // 폰트 사이즈 선택
    if (sizeSelect) {
        sizeSelect.addEventListener('change', function() {
            const value = this.value;
            editorContent.focus();
            document.execCommand('fontSize', false, '3');
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                const span = document.createElement('span');
                span.style.fontSize = value + 'px';
                try {
                    range.surroundContents(span);
                } catch (e) {
                    // 이미 선택된 경우 처리
                }
            }
        });
    }
    
    // 명령 실행
    function executeCommand(cmd, btn) {
        editorContent.focus();
        
        switch(cmd) {
            case 'bold':
            case 'italic':
            case 'underline':
            case 'strikeThrough':
                document.execCommand(cmd, false, null);
                btn.classList.toggle('active');
                break;
                
            case 'foreColor':
                const color = prompt('글자 색상을 입력하세요 (예: #ff0000)', '#000000');
                if (color) {
                    document.execCommand(cmd, false, color);
                }
                break;
                
            case 'backColor':
                const bgColor = prompt('배경 색상을 입력하세요 (예: #ffff00)', '#ffffff');
                if (bgColor) {
                    document.execCommand(cmd, false, bgColor);
                }
                break;
                
            case 'justifyLeft':
            case 'justifyCenter':
            case 'justifyRight':
            case 'justifyFull':
                document.execCommand(cmd, false, null);
                document.querySelectorAll('[data-cmd^="justify"]').forEach(b => {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
                break;
                
            case 'insertOrderedList':
            case 'insertUnorderedList':
                document.execCommand(cmd, false, null);
                btn.classList.toggle('active');
                break;
                
            case 'createLink':
                const url = prompt('링크 URL을 입력하세요:', 'https://');
                if (url && url !== 'https://') {
                    document.execCommand('createLink', false, url);
                }
                break;
                
            case 'insertImage':
                const imgUrl = prompt('이미지 URL을 입력하세요:', 'https://');
                if (imgUrl && imgUrl !== 'https://') {
                    document.execCommand('insertImage', false, imgUrl);
                }
                break;
        }
    }
}

function handleFileUpload(event) {
    const files = event.target.files;
    if (!files || files.length === 0) return;
    
    // 새로 선택된 파일들을 selectedFiles 배열에 추가
    Array.from(files).forEach(file => {
        // 중복 체크
        if (!selectedFiles.find(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified)) {
            selectedFiles.push(file);
            
            // 미리보기를 위한 임시 attachment 객체 생성
            const reader = new FileReader();
            reader.onload = function(e) {
                const attachment = {
                    fileName: file.name,
                    fileSize: file.size,
                    fileType: file.type,
                    filePath: e.target.result, // 임시로 data URL 사용
                    isNew: true // 새로 추가된 파일 표시
                };
                currentAttachments.push(attachment);
                renderAttachments(currentAttachments, true);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // input 초기화
    event.target.value = '';
}

function renderAttachments(attachments, allowRemove = true) {
    const attachList = document.getElementById('attach-list');
    if (!attachList) return;
    
    attachList.innerHTML = attachments.map((attachment, index) => {
        const fileName = attachment.fileName || attachment.name || '';
        let filePath = attachment.filePath || attachment.path || '';
        const fileSize = attachment.fileSize || 0;
        const fileType = attachment.fileType || '';
        
        // 파일 경로 변환 (uploads/inquiries/ -> www/uploads/inquiries/)
        if (filePath && !filePath.startsWith('http://') && !filePath.startsWith('https://') && !filePath.startsWith('data:')) {
            if (filePath.startsWith('/')) {
                // 절대 경로인 경우
                filePath = window.location.origin + filePath;
            } else if (filePath.startsWith('../')) {
                // ../www/uploads/inquiries/... 형식 처리
                filePath = window.location.origin + '/' + filePath.replace('../www/', '');
            } else if (filePath.startsWith('uploads/')) {
                // 이미 uploads/ 로 시작하면 중복되지 않도록 그대로 사용
                filePath = window.location.origin + '/' + filePath;
            } else {
                // 상대 경로인 경우 uploads/ 접두사 보장
                const normalizedPath = filePath.startsWith('uploads/') ? filePath : `uploads/${filePath}`;
                filePath = window.location.origin + '/' + normalizedPath;
            }
        }
        
        // 파일 크기 포맷팅
        const formatFileSize = (bytes) => {
            if (!bytes) return '';
            if (bytes < 1024) return bytes + 'B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + 'KB';
            return (bytes / (1024 * 1024)).toFixed(0) + 'MB';
        };
        
        // 파일 타입에 따른 아이콘 결정
        const isImage = fileType && fileType.startsWith('image/');
        
        if (isImage) {
            return `
                <div class="grid-item">
                    <div class="upload-box">
                        <img src="${escapeHtml(filePath)}" alt="${escapeHtml(fileName)}" class="preview" style="width: 110px; height: 110px; object-fit: cover; border-radius: 10px;">
                        <div class="upload-meta">
                            <div class="file-title">${escapeHtml(fileName)}</div>
                            <div class="file-info">${formatFileSize(fileSize)}</div>
                            <div class="file-controller">
                                <button type="button" class="btn-icon" aria-label="다운로드" onclick="window.open('${escapeHtml(filePath)}', '_blank')"><img src="../image/button-download.svg" alt=""></button>
                                ${allowRemove ? `<button type="button" class="btn-icon" aria-label="삭제" onclick="removeAttachment(${index})"><img src="../image/button-close2.svg" alt=""></button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="cell">
                    <div class="field-row jw-center">
                        <div class="jw-center jw-gap10"><img src="../image/file.svg" alt=""> ${escapeHtml(fileName)} [${formatFileSize(fileSize)}]</div>
                        <div class="jw-center jw-gap10">
                            <i></i>
                            <button type="button" class="jw-button typeF" aria-label="다운로드" onclick="window.open('${escapeHtml(filePath)}', '_blank')"><img src="../image/buttun-download.svg" alt=""></button>
                            ${allowRemove ? `<button type="button" class="jw-button typeF" aria-label="삭제" onclick="removeAttachment(${index})"><img src="../image/button-close2.svg" alt=""></button>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');
}

function removeAttachment(index) {
    const attachment = currentAttachments[index];
    
    // 새로 추가된 파일인 경우 selectedFiles에서도 제거
    if (attachment && attachment.isNew) {
        const fileName = attachment.fileName;
        selectedFiles = selectedFiles.filter(f => f.name !== fileName);
    }
    
    currentAttachments.splice(index, 1);
    renderAttachments(currentAttachments, !isAnswered);
}

function formatDateTime(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        
        return `${year}-${month}-${day} ${hours}:${minutes}`;
    } catch (e) {
        return dateString;
    }
}

async function handleSave() {
    try {
        const titleInput = document.getElementById('title');
        const contentEditor = document.getElementById('content');
        
        if (!titleInput || !contentEditor) {
            alert('필수 필드를 찾을 수 없습니다.');
            return;
        }
        
        // FormData 생성 (파일 업로드를 위해)
        const formData = new FormData();
        formData.append('action', 'updateInquiry');
        formData.append('inquiryId', currentInquiryId);
        formData.append('inquiryTitle', titleInput.value);
        formData.append('inquiryContent', contentEditor.innerHTML);
        
        // 새로 선택된 파일들을 FormData에 추가
        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                formData.append(`file_${index}`, file);
            });
        }
        
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData // FormData 사용 시 Content-Type 헤더는 자동 설정됨
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('저장되었습니다.');
            // 새로 추가된 파일 목록 초기화
            selectedFiles = [];
            loadInquiryDetail(); // 재로드
        } else {
            alert('저장에 실패했습니다: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert('저장 중 오류가 발생했습니다.');
    }
}

function showLoading() {
    // 로딩 상태 표시
}

function hideLoading() {
    // 로딩 종료
}

function showError(message) {
    alert(message);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
