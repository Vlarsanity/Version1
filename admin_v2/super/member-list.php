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

	<!-- General CSS -->
	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/css/root.css?v=<?= time(); ?>">


	<!-- Tabulator CSS -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.5.0/css/tabulator.min.css" rel="stylesheet">

	<link rel="stylesheet" href="../../admin_v2/super/css/base-table-layout.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/super/css/tabulator-styles.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/super/css/theme-default.css?v=<?= time(); ?>">

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

				<!-- Main Content Wrapper -->
				<div class="main-content-wrapper">

					<!-- Table Header Section -->
					<div class="table-header">
						<div class="table-header__left">
							<h1 class="table-title" data-lan-eng="Member List">전체 회원 목록</h1>
						</div>
						<div class="table-header__right">
							<!-- Multiple buttons can be added here -->
							<button type="button" class="btn btn-primary" data-lan-eng="Download">다운로드</button>
							<!-- Example: <button type="button" class="btn btn-secondary">Add New</button> -->
						</div>
					</div>

					
					<!-- Table Body Section -->
					<div class="table-body">

						<div class="card-panel">

							<div class="table-container-header">

								<!-- LEFT 50% -->
								<div class="container-header-left">

									<div class="search-field">

										<label for="searchInput" class="search-label">
											Search (회원 유형)
										</label>

										<div class="search-input-wrapper">
											<input 
												type="text"
												id="searchInput"
												class="search-input"
												placeholder="고객명 검색"
												data-lan-eng="Customer Name Search"
											>

											<button type="button" class="search-btn" aria-label="검색">
												<span class="search-icon">
													<img src="../image/search.svg" alt="search">
												</span>
											</button>
										</div>

									</div>



								</div>

								<!-- RIGHT 50% -->
								<div class="container-header-right">

									<!-- Sorting Controls -->
									<div class="sorting-controls">

										<!-- Sort by Member Type -->
										<div class="sort-field">
											<label for="sortType" class="sort-label">Member Type (회원 유형)</label>
											<select id="sortType" class="sort-select">
												<option value="">전체</option>
												<option value="b2b">B2B</option>
												<option value="b2c">B2C</option>
												<option value="agent">에이전트</option>
												<option value="guide">가이드</option>
											</select>
										</div>

										<!-- Sort by Date Range -->
										<div class="sort-field">
											<label for="sortDate" class="sort-label">Reg. Period (등록 기간)</label>
											<select id="sortDate" class="sort-select">
												<option value="">전체 기간</option>
												<option value="today">오늘</option>
												<option value="week">최근 7일</option>
												<option value="month">최근 30일</option>
												<option value="quarter">최근 3개월</option>
												<option value="year">올해</option>
											</select>
										</div>

										<!-- Sort Order -->
										<div class="sort-field">
											<label for="sortOrder" class="sort-label">Order Sort (정렬 정렬)</label>
											<select id="sortOrder" class="sort-select">
												<option value="desc">최신순</option>
												<option value="asc">오래된순</option>
												<option value="name-asc">이름 A-Z</option>
												<option value="name-desc">이름 Z-A</option>
											</select>
										</div>

										<!-- Reset Button -->
										<button type="button" class="reset-btn" id="resetFilters" title="필터 초기화">
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
												<path d="M3 3v5h5"></path>
											</svg>
										</button>

									</div>
								</div>

							</div>

							<!-- Table Container with Scroll -->
							<div class="table-container">

								<div id="data-table"></div>

								<!-- Empty State -->
								<div class="empty-state" id="emptyState">
									<div class="empty-state__content">
										<p class="empty-state__text">No members found</p>
										<button type="button" class="btn btn-secondary">Add First Member</button>
									</div>
								</div>

							</div>

							<!-- Custom Pagination Section -->
							<div class="pagination-wrapper">
								<div class="pagination-info">
									<span class="pagination-text">Showing <strong id="showingRange">0-0</strong> of <strong id="totalRows">0</strong></span>
								</div>

								<div class="pagination-controls">
									<nav class="pagination" role="navigation" aria-label="페이지네이션">
										<button type="button" class="pagination-btn pagination-btn--first" aria-label="첫 페이지" aria-disabled="true">
											<img src="../image/first.svg" alt="">
										</button>
										<button type="button" class="pagination-btn pagination-btn--prev" aria-label="이전 페이지" aria-disabled="true">
											<img src="../image/prev.svg" alt="">
										</button>
										<div class="pagination-numbers" id="paginationNumbers">
											<!-- Page numbers dynamically inserted here -->
										</div>
										<button type="button" class="pagination-btn pagination-btn--next" aria-label="다음 페이지" aria-disabled="false">
											<img src="../image/next.svg" alt="">
										</button>
										<button type="button" class="pagination-btn pagination-btn--last" aria-label="마지막 페이지" aria-disabled="false">
											<img src="../image/last.svg" alt="">
										</button>
									</nav>
								</div>
								
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
<script src="../js/super.js"></script>

<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar-super.js"></script>


<!-- Tabulator.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.5.0/js/tabulator.min.js"></script>

<!-- Tabulator.js Script -->
<script>

	// Sample data - replace with your actual data source
	const tableData = [{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		},{
			no: 10,
			type: "B2B",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 9,
			type: "B2C",
			name: "Jose Ramirez",
			date: "2025-06-20 12:12"
		},
		{
			no: 8,
			type: "에이전트",
			name: "Bacolod City",
			date: "2025-06-20 12:12"
		},
		{
			no: 7,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 6,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 5,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 4,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 3,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 2,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 1,
			type: "가이드",
			name: "Hyunwoo Park",
			date: "2025-06-20 12:12"
		},
		{
			no: 11,
			type: "B2B",
			name: "Maria Garcia",
			date: "2025-06-21 10:30"
		},
		{
			no: 12,
			type: "B2C",
			name: "John Smith",
			date: "2025-06-22 14:15"
		},
		{
			no: 13,
			type: "에이전트",
			name: "Kim Min-ji",
			date: "2025-06-23 09:45"
		},
		{
			no: 14,
			type: "가이드",
			name: "Sarah Johnson",
			date: "2025-06-24 16:20"
		},
		{
			no: 15,
			type: "B2B",
			name: "Lee Joon-ho",
			date: "2025-06-25 11:00"
		}
	];



	// Pagination state
	let currentPage = 1;
	let totalPages = 1;
	let pageSize = 11;

	
	// Initialize Tabulator
	const table = new Tabulator("#data-table", {
		data: tableData,
		layout: "fitColumns", // Better for responsive: stretches to fit container
		// OR use: "fitColumns" (fits columns to table width)
		// OR use: "fitDataFill" (columns fill space, respect minWidth)

		pagination: true,
		paginationSize: pageSize,
		paginationCounter: function(pageSize, currentRow, currentPage, totalRows, totalPages) {
			updatePaginationInfo(currentRow, pageSize, totalRows, currentPage, totalPages);
			return "";
		},

		// Row height (alternative to CSS)
		// rowHeight: 47.5, // Fixed row height in pixels

		movableColumns: false,
		responsiveLayout: "collapse", // Good! Collapses columns on mobile

		columns: [{
				title: "No",
				field: "no",
				width: 80,
				minWidth: 60, // Minimum width on resize
				responsive: 0, // Priority: 0 = highest (always visible)
				hozAlign: "center",
				sorter: "number",
				headerSort: true
			},
			{
				title: "Member Type",
				field: "type",
				minWidth: 120, // No fixed width = flexible
				responsive: 1,
				hozAlign: "center",
				headerSort: true,
				headerTooltip: "Member type"
			},
			{
				title: "Name",
				field: "name",
				minWidth: 150,
				responsive: 2,
				hozAlign: "center",
				headerSort: true,
				headerTooltip: "Name"
			},
			{
				title: "Reg. Date",
				field: "date",
				width: 160,
				minWidth: 140,
				responsive: 3,
				hozAlign: "center",
				sorter: "datetime",
				sorterParams: {
					format: "YYYY-MM-DD HH:mm"
				},
				headerSort: true,
				headerTooltip: "Registration date/time"
			}
		],

		initialSort: [{
			column: "no",
			dir: "desc"
		}],

		placeholder: "No data available",

		dataFiltered: function(filters, rows) {
			toggleEmptyState(rows.length === 0);
		},

		dataLoaded: function(data) {
			toggleEmptyState(data.length === 0);
			const pageInfo = table.getPageMax();
			const dataCount = table.getDataCount();
			updatePaginationInfo(1, pageSize, dataCount, 1, pageInfo);
		}
	});

	// ============================================
	// CUSTOM PAGINATION FUNCTIONS
	// ============================================

	function updatePaginationInfo(currentRow, pageSize, totalRows, page, pages) {
		currentPage = page;
		totalPages = pages;

		const start = totalRows === 0 ? 0 : currentRow;
		const end = Math.min(currentRow + pageSize - 1, totalRows);

		document.getElementById("showingRange").textContent = `${start}-${end}`;
		document.getElementById("totalRows").textContent = totalRows;

		updatePaginationButtons();
		renderPageNumbers();
	}

	function updatePaginationButtons() {
		const firstBtn = document.querySelector(".pagination-btn--first");
		const prevBtn = document.querySelector(".pagination-btn--prev");
		const nextBtn = document.querySelector(".pagination-btn--next");
		const lastBtn = document.querySelector(".pagination-btn--last");

		// Disable first/prev on first page
		const isFirstPage = currentPage === 1;
		firstBtn.setAttribute("aria-disabled", isFirstPage);
		prevBtn.setAttribute("aria-disabled", isFirstPage);

		// Disable next/last on last page
		const isLastPage = currentPage === totalPages;
		nextBtn.setAttribute("aria-disabled", isLastPage);
		lastBtn.setAttribute("aria-disabled", isLastPage);
	}

	function renderPageNumbers() {
		const container = document.getElementById("paginationNumbers");
		container.innerHTML = "";

		// Calculate which pages to show (max 5 page numbers)
		let startPage = Math.max(1, currentPage - 2);
		let endPage = Math.min(totalPages, startPage + 4);

		// Adjust if near the end
		if (endPage - startPage < 4) {
			startPage = Math.max(1, endPage - 4);
		}

		for (let i = startPage; i <= endPage; i++) {
			const btn = document.createElement("button");
			btn.type = "button";
			btn.className = "pagination-number";
			btn.textContent = i;

			if (i === currentPage) {
				btn.classList.add("pagination-number--active");
				btn.setAttribute("aria-current", "page");
			}

			btn.addEventListener("click", () => {
				table.setPage(i);
			});

			container.appendChild(btn);
		}
	}

	// Pagination button handlers
	document.querySelector(".pagination-btn--first").addEventListener("click", function() {
		if (this.getAttribute("aria-disabled") !== "true") {
			table.setPage(1);
		}
	});

	document.querySelector(".pagination-btn--prev").addEventListener("click", function() {
		if (this.getAttribute("aria-disabled") !== "true") {
			table.previousPage();
		}
	});

	document.querySelector(".pagination-btn--next").addEventListener("click", function() {
		if (this.getAttribute("aria-disabled") !== "true") {
			table.nextPage();
		}
	});

	document.querySelector(".pagination-btn--last").addEventListener("click", function() {
		if (this.getAttribute("aria-disabled") !== "true") {
			table.setPage(totalPages);
		}
	});

	// ============================================
	// SEARCH FUNCTIONALITY
	// ============================================

	const searchInput = document.getElementById("searchInput");
	searchInput.addEventListener("keyup", function() {
		const searchValue = this.value;

		if (searchValue === "") {
			table.clearFilter();
		} else {
			// Search across multiple fields
			table.setFilter([{
					field: "name",
					type: "like",
					value: searchValue
				},
				{
					field: "type",
					type: "like",
					value: searchValue
				},
				{
					field: "date",
					type: "like",
					value: searchValue
				}
			], "or");
		}
	});

	// ============================================
	// EMPTY STATE TOGGLE
	// ============================================

	function toggleEmptyState(isEmpty) {
		const emptyState = document.getElementById("emptyState");
		const tableElement = document.getElementById("data-table");

		if (isEmpty) {
			emptyState.classList.add("show");
			tableElement.style.display = "none";
		} else {
			emptyState.classList.remove("show");
			tableElement.style.display = "block";
		}
	}

	// ============================================
	// SORTING CONTROLS
	// ============================================

	document.addEventListener('DOMContentLoaded', function() {
		const sortTypeSelect = document.getElementById('sortType');
		const sortDateSelect = document.getElementById('sortDate');
		const sortOrderSelect = document.getElementById('sortOrder');
		const resetBtn = document.getElementById('resetFilters');

		// Sort by Member Type
		sortTypeSelect.addEventListener('change', function() {
			const value = this.value;
			if (value === '') {
				table.removeFilter('type');
			} else {
				// Match the exact value from data
				const filterValue = value === 'b2b' ? 'B2B' :
					value === 'b2c' ? 'B2C' :
					value === 'agent' ? '에이전트' :
					value === 'guide' ? '가이드' : value;
				table.setFilter('type', '=', filterValue);
			}
		});

		// Sort by Date Range
		sortDateSelect.addEventListener('change', function() {
			const value = this.value;
			if (value === '') {
				table.removeFilter('date');
				return;
			}

			const now = new Date();
			let startDate;

			switch (value) {
				case 'today':
					startDate = new Date(now.setHours(0, 0, 0, 0));
					break;
				case 'week':
					startDate = new Date(now.setDate(now.getDate() - 7));
					break;
				case 'month':
					startDate = new Date(now.setDate(now.getDate() - 30));
					break;
				case 'quarter':
					startDate = new Date(now.setMonth(now.getMonth() - 3));
					break;
				case 'year':
					startDate = new Date(now.getFullYear(), 0, 1);
					break;
			}

			// Custom filter for date comparison
			table.setFilter([{
				field: "date",
				type: ">=",
				value: startDate.toISOString().split('T')[0]
			}]);
		});

		// Sort Order
		sortOrderSelect.addEventListener('change', function() {
			const value = this.value;

			switch (value) {
				case 'desc':
					table.setSort('no', 'desc');
					break;
				case 'asc':
					table.setSort('no', 'asc');
					break;
				case 'name-asc':
					table.setSort('name', 'asc');
					break;
				case 'name-desc':
					table.setSort('name', 'desc');
					break;
			}
		});

		// Reset All Filters
		resetBtn.addEventListener('click', function() {
			// Reset select values
			sortTypeSelect.value = '';
			sortDateSelect.value = '';
			sortOrderSelect.value = 'desc';

			// Clear all filters
			table.clearFilter();

			// Reset sort
			table.setSort('no', 'desc');

			// Add visual feedback
			this.style.transform = 'rotate(-360deg)';
			setTimeout(() => {
				this.style.transform = '';
			}, 300);
		});
	});

	// ============================================
	// OPTIONAL: ADD MEMBER BUTTON
	// ============================================

	// Uncomment if you have an "Add Member" button
	/*
	document.querySelector(".btn-secondary").addEventListener("click", function() {
		alert("Add new member functionality");
		// Implement your add member logic here
	});
	*/

	// ============================================
	// OPTIONAL: LOAD DATA FROM API
	// ============================================

	// Uncomment to load data from your API endpoint
	// table.setData("your-api-endpoint.php");


</script>


</html>