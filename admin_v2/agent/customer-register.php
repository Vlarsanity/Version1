<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Customer Registration</title>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico" />

	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />
	
	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">

	<!-- editor -->
	<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>


</head>

<body>
	<!-- header 들어올 자리 / Main Navbar -->
	<header class="layout-header"></header>

	<!-- 본문 영역 -->
	<main class="main-container">

		<div class="wrapper-container">

			<!-- nav 들어올 자리 / Sidebar -->
			<nav class="layout-nav"></nav>

			<!-- Main Content Wrapper -->
			<section class="main-content">

				<!-- Main-Body template Here -->
				<div class="main-content-wrapper">

					<div class="content-wrapper-header">
						<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
							<h1 class="page-title" data-lan-eng="Customer Registration" style="margin: 0;">고객 등록</h1>
							<div style="display: flex; gap: 8px; align-items: center;">
								<button type="button" class="jw-button typeC" id="testDataBtn" data-lan-eng="Fill Test Data">테스트 데이터 입력</button>
								<button type="button" class="jw-button typeB" data-lan-eng="Save" id="saveBtn">저장</button>
							</div>
						</div>
					</div>

					<div class="content-wrapper-body">
						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">

								<!-- 1행: 고객명 / 연락처 / 이메일 -->
								<div class="grid-item">
									<label class="label-name" for="cust_name" data-lan-eng="Customer Name">고객명</label>
									<input id="cust_name" type="text" class="form-control" placeholder="고객명" data-lan-eng-placeholder="Customer Name">
								</div>

								<div class="grid-item">
									<label class="label-name" for="cust_phone" data-lan-eng="Contact">연락처</label>
									<div class="field-row">
										<select id="country_code" class="select w-auto" aria-label="국가 코드" data-lan-eng-aria-label="Country Code">
											<option value="+63" selected>+63</option>
											<option value="+82">+82</option>
											<option value="+81">+81</option>
											<option value="+1">+1</option>
										</select>
										<input id="cust_phone" type="tel" class="form-control" placeholder="숫자 입력" inputmode="numeric" data-lan-eng-placeholder="Enter number">
									</div>
								</div>

								<div class="grid-item">
									<label class="label-name" for="cust_email" data-lan-eng="Email">이메일</label>
									<input id="cust_email" type="email" class="form-control" placeholder="이메일" data-lan-eng-placeholder="Email">
								</div>

								<!-- 2행: PW (좌측 한 칸만) -->
								<div class="grid-item">
									<label class="label-name" for="cust_pw">PW</label>
									<div class="input-box">
										<input id="cust_pw" type="password" value="">
										<button type="button" class="jw-button typeD"><img src="../image/replay.svg" alt=""><span data-lan-eng="Auto Input">자동 입력</span></button>
									</div>
								</div>
								<div class="grid-item"></div>
								<div class="grid-item"></div>

								<!-- 3행: Note (전체폭 에디터) -->
								<!-- multi-editor.js -->
								<div class="grid-item col-span-3">
									<label class="label-name" for="content" data-lan-eng="Note">Note</label>
									<div class="editor-box-wrap">
										<div class="jw-editor">
											<div class="toolbar">
												<div class="group">
													<button type="button" class="editor-btn ql-bold">
														<img src="../image/editer_bold.svg">
													</button>
													<button type="button" class="editor-btn ql-italic">
														<img src="../image/editer_italic.svg">
													</button>
													<button type="button" class="editor-btn ql-underline">
														<img src="../image/editer_underline.svg">
													</button>
													<button type="button" class="editor-btn ql-strike">
														<img src="../image/editer_strikethrough.svg">
													</button>

													<!-- 색상 버튼은 this 넘겨주기 -->
													<button type="button" class="editor-btn font-color" onclick="setColor(this);">가</button>
													<button type="button" class="background-color" onclick="setColor(this, 2);"></button>
												</div>

												<div class="group">
													<!-- select도 this 넘겨주기 -->
													<select class="editor-font-size" onchange="fontsize(this);">
														<option value="" selected>폰트</option>
														<option value="12px">12px</option>
														<option value="14px">14px</option>
														<option value="16px">16px</option>
														<option value="18px">18px</option>
														<option value="24px">24px</option>
													</select>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-align" value="">
														<img src="../image/editer_left.svg">
													</button>
													<button type="button" class="editor-btn ql-align" value="center">
														<img src="../image/editer_center.svg">
													</button>
													<button type="button" class="editor-btn ql-align" value="right">
														<img src="../image/editer_right.svg">
													</button>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-list" value="ordered">
														<img src="../image/editer_ordered.svg">
													</button>
													<button type="button" class="editor-btn ql-list" value="bullet">
														<img src="../image/editer_unordered.svg">
													</button>
												</div>

												<div class="group">
													<!-- 이것도 this 넘기게 변경 -->
													<button type="button" class="editor-btn" onclick="removeHtmlTags(this);">
														<img src="../image/editer_removeHtmlTags.svg">
													</button>
													<button type="button" class="editor-btn" style="font-size:9px;" onclick="toggleHtmlView(this);">
														HTML
													</button>
												</div>

												<div class="group">
													<label class="inputFile">
														<!-- this 넘기기 -->
														<input name="item_img[]" type="file" accept="image/*" multiple onchange="insertImage(this);">
														<span class="text">이미지</span>
														<i class="progress"></i>
													</label>
												</div>
											</div>

											<div class="jweditor" data-placeholder="내용을 입력해주세요"></div>
										</div>
									</div>
								</div>

							</div>
						</div>


						<h2 class="section-title jw-mgt32" data-lan-eng="Traveler Information">여행자 정보</h2>

						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">

								<div class="grid-item">
									<label class="label-name" for="title" data-lan-eng="Title">호칭</label>
									<select id="title" class="select">
										<option value="mr" selected>MR</option>
										<option value="ms">MS</option>
									</select>
								</div>

								<div class="grid-item">
									<label class="label-name" for="first_name" data-lan-eng="Name">이름</label>
									<input id="first_name" type="text" class="form-control" placeholder="이름" data-lan-eng-placeholder="Name">
								</div>

								<div class="grid-item">
									<label class="label-name" for="last_name" data-lan-eng="Last Name">성</label>
									<input id="last_name" type="text" class="form-control" placeholder="성" data-lan-eng-placeholder="Last Name">
								</div>

								<!-- 2행: 성별 / 나이 / 생년월일 -->
								<div class="grid-item">
									<label class="label-name" for="gender" data-lan-eng="Gender">성별</label>
									<select id="gender" class="select">
										<option value="male" selected data-lan-eng="Male">남성</option>
										<option value="female" data-lan-eng="Female">여성</option>
									</select>
								</div>

								<div class="grid-item">
									<label class="label-name" for="age" data-lan-eng="Age">나이</label>
									<input id="age" type="number" class="form-control" placeholder="숫자 입력" min="0" data-lan-eng-placeholder="Enter numbers">
								</div>

								<div class="grid-item">
									<label class="label-name" for="birth" data-lan-eng="Date of Birth">생년월일</label>
									<input id="birth" type="text" class="form-control" placeholder="YYYYMMDD" inputmode="numeric">
								</div>

								<!-- 3행: 출신국가 / 여권번호 / 여권 발행일 -->
								<div class="grid-item">
									<label class="label-name" for="nationality" data-lan-eng="Country of origin">출신국가</label>
									<input id="nationality" type="text" class="form-control" placeholder="출신국가" data-lan-eng-placeholder="Country of origin">
								</div>

								<div class="grid-item">
									<label class="label-name" for="passport_no" data-lan-eng="Passport number">여권번호</label>
									<input id="passport_no" type="text" class="form-control" placeholder="여권번호" data-lan-eng-placeholder="Passport number">
								</div>

								<div class="grid-item">
									<label class="label-name" for="passport_issue" data-lan-eng="Passport Issue Date">여권 발행일</label>
									<input id="passport_issue" type="text" class="form-control" placeholder="YYYYMMDD" inputmode="numeric">
								</div>

								<!-- 4행: 여권 만료일 / 여권 사진 / 빈칸 -->
								<div class="grid-item">
									<label class="label-name" for="passport_expire" data-lan-eng="Passport Expiry Date">여권 만료일</label>
									<input id="passport_expire" type="text" class="form-control" placeholder="YYYYMMDD" inputmode="numeric">
								</div>

								<div class="grid-item">
									<label class="label-name" data-lan-eng="Passport photo">여권 사진</label>
									<div class="upload-box">
										<input id="passport_photo" type="file" accept="image/*">
										<label for="passport_photo" class="thumb" aria-label="preview"></label>
										<div class="upload-meta" style="display: none;">
											<div class="file-title">이미지</div>
											<div class="file-info" id="passport_photo_info">jpg, 328KB</div>
											<div class="file-controller">
												<button type="button" class="btn-icon" id="passport_photo_download" aria-label="다운로드"><img src="../image/button-download.svg" alt=""></button>
												<button type="button" class="btn-icon" id="passport_photo_remove" aria-label="삭제"><img src="../image/button-close2.svg" alt=""></button>
											</div>
										</div>
									</div>
								</div>

								<div class="grid-item"></div>

							</div>
						</div>
					</div>

				</div>

			</section>

		</div>

	</main>

</body>

<!-- 기본 스크립트 -->
<script src="../js/default.js"></script>
<script src="../js/agent.js"></script>
<script src="../js/multi-editor.js"></script>
<script src="../js/agent-customer-register.js"></script>


<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar.js"></script>

</html>