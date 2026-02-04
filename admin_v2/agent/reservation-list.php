<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Template Page</title>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico" />

	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />

	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/agent/css/page-specifics/registration-list.css?v=<?= time(); ?>">


	<!-- 날짜 범위 선택용 라이브러리 -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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
						<h1 class="page-title" data-lan-eng="Reservation List">예약 목록</h1>
					</div>

					<div class="content-wrapper-body">

						<div class="card-panel jw-mgt32">

							<div class="list-header">

								<!-- Result Count -->
								<div class="result-count">
									<span data-lan-eng="Search results">검색결과</span>
									<span class="result-count__num">999</span>
									<span data-lan-eng="items">개</span>
								</div>

								<!-- Search Form -->
								<form class="search-form" action="" method="get">

									<!-- Date Picker -->
									<div class="grid-item">
										<div class="field-row">
											<input
												id="travelStartDate"
												type="text"
												class="jw-w form-control"
												name="travelStartDate"
												placeholder="Travel start date"
												readonly
												data-lan-eng="Travel start date">
											<button
												type="button"
												class="btn-icon calendar"
												aria-label="달력 열기"
												data-target="#travelStartDate">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
												</svg>
											</button>
										</div>
									</div>

									<!-- Status Select -->
									<select class="select" name="status">
										<option value="" data-lan-eng="All Status">전체 상태</option>
										<option value="waiting_down_payment" data-lan-eng="Waiting Down Payment">선금 대기</option>
										<option value="checking_down_payment" data-lan-eng="Checking Down Payment">선금 확인 중</option>
										<option value="waiting_advance_payment" data-lan-eng="Waiting Advance Payment">중도금 대기</option>
										<option value="checking_advance_payment" data-lan-eng="Checking Advance Payment">중도금 확인 중</option>
										<option value="waiting_balance" data-lan-eng="Waiting Balance">잔금 대기</option>
										<option value="checking_balance" data-lan-eng="Checking Balance">잔금 확인 중</option>
										<option value="confirmed" data-lan-eng="Confirmed">예약 확정</option>
										<option value="cancelled" data-lan-eng="Cancelled">예약 취소</option>
										<option value="refund_completed" data-lan-eng="Refund Completed">환불 완료</option>
										<option value="trip_completed" data-lan-eng="Trip Completed">여행 완료</option>
									</select>

									<!-- Search Field -->
									<div class="search-field">
										<select class="select" name="searchType">
											<option value="" data-lan-eng="All">전체</option>
											<option value="product">상품명</option>
											<option value="customer">고객명</option>
											<option value="bookingId">예약번호</option>
										</select>
										<input
											type="text"
											class="search-input"
											name="search"
											placeholder="Search"
											data-lan-eng="Search">
									</div>

									<button type="submit" class="jw-button search-btn" aria-label="검색">
										<span class="search-ico">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
												<path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
									</button>

								</form>

							</div>

							<table class="jw-tableA">
								<colgroup>
									<col class="col-60"> <!-- No -->
									<col> <!-- 상품명 (auto) -->
									<col> <!-- 여행 시작일 -->
									<col> <!-- 예약자명 -->
									<col> <!-- 예약 인원 -->
									<col> <!-- 상태 -->
								</colgroup>
								<thead>
									<tr>
										<th class="no is-center">No</th>
										<th data-lan-eng="Product Name">상품명</th>
										<th data-lan-eng="Travel start date">여행 시작일</th>
										<th data-lan-eng="Reserver's name">예약자명</th>
										<th data-lan-eng="Number of people for reservation">예약 인원</th>
										<th data-lan-eng="Status">상태</th>
									</tr>
								</thead>
								<tbody>
									<!-- 데이터는 JavaScript에서 동적으로 로드됩니다 -->
									<tr>
										<td colspan="6" class="is-center">로딩 중...</td>
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
							<button type="button" class="jw-button typeA" onclick="downloadReservations()" data-lan-eng="Download">다운로드</button>
							<button type="button" class="jw-button typeB" onclick="temp_link('create-reservation.html')"><img src="../image/buttonB.svg" alt="">Create Booking</button>
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
<script src="../js/datepicker.js"></script>
<script src="../js/agent-reservation-list.js"></script>


<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar.js"></script>

</html>