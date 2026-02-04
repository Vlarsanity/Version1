<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Member List</title>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">


	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />

	<!-- General CSS -->
	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/css/root.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/agent/css/page-specifics/registration-list.css?v=<?= time(); ?>">

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
						<h1 class="page-title" data-lan-eng="Guide List">가이드 목록</h1>
					</div>


					<div class="content-wrapper-body">

						<div class="card-panel jw-mgt32">

							<div class="list-header">
								<div class="result-count">
									<span data-lan-eng="Search results">검색결과</span> <span class="result-count__num">999</span><span data-lan-eng="items">개</span>
								</div>
								<form class="search-form" action="" method="get">
									<select class="select" name="" onchange="">
										<option value="">전체 상태</option>
										<option value="">전체</option>
									</select>

									<div class="search-field">
										<input type="text" class="search-input" placeholder="고객명 검색" data-lan-eng="Search Guide Name">
										<button type="submit" class="jw-button search-btn" aria-label="검색">
											<span class="search-ico"><img src="../image/search.svg" alt=""></span>
										</button>
									</div>
								</form>
							</div>

							<table class="jw-tableA">
								<colgroup>
									<col class="col-60"> <!-- No -->
									<col> <!-- 가이드명 -->
									<col> <!-- 이메일 -->
									<col> <!-- 연락처 -->
									<col> <!-- 계약 기간 -->
									<col> <!-- 활동 상태 -->
								</colgroup>
								<thead>
									<tr>
										<th class="no is-center">No</th>
										<th data-lan-eng="Guide Name">가이드명</th>
										<th data-lan-eng="Email">이메일</th>
										<th data-lan-eng="Contacts">연락처</th>
										<th data-lan-eng="Contract period">계약 기간</th>
										<th data-lan-eng="Activity Status" class="is-center">활동 상태</th>
									</tr>
								</thead>
								<tbody>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">10</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">9</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약 종료</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">8</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">7</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">6</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">5</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">4</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">3</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">2</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
									<tr onclick="temp_link('guide-detail.html')">
										<td class="no is-center">1</td>
										<td class="is-center">Hyunwoo Park</td>
										<td class="is-center">hyunwoo@gmail.com</td>
										<td class="is-center">+82 10 1234 5678</td>
										<td class="is-center">2024-06-01 ~ 2027-06-01</td>
										<td class="is-center">계약중</td>
									</tr>
								</tbody>
							</table>



							<div class="jw-pagebox" role="navigation" aria-label="페이지네이션">
								<div class="contents">
									<button type="button" class="first" aria-label="첫 페이지" aria-disabled="false">
										<img src="../image/first.svg" alt="">
									</button>
									<button type="button" class="prev" aria-label="이전 페이지" aria-disabled="false">
										<img src="../image/prev.svg" alt="">
									</button>

									<div class="page" role="list">
										<button type="button" class="p" role="listitem">1</button>
										<button type="button" class="p" role="listitem">2</button>
										<button type="button" class="p show" role="listitem" aria-current="page">3</button>
										<button type="button" class="p" role="listitem">4</button>
										<button type="button" class="p" role="listitem">5</button>
									</div>

									<button type="button" class="next" aria-label="다음 페이지" aria-disabled="false">
										<img src="../image/next.svg" alt="">
									</button>
									<button type="button" class="last" aria-label="마지막 페이지" aria-disabled="false">
										<img src="../image/last.svg" alt="">
									</button>
								</div>
							</div>

						</div>

						<div class="controller">
							<button type="button" class="jw-button typeA" data-lan-eng="Download">다운로드</button>
							<button type="button" class="jw-button typeB" onclick="temp_link('guide-registration.html')"><img src="../image/buttonB.svg" alt="">Add New Guide</button>
						</div>

					</div>

				</div>

			</section>

		</div>

	</main>

</body>

<!-- 기본 스크립트 -->
<script src="../js/default.js"></script>
<script src="../js/super.js"></script>


<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar-super.js"></script>

</html>