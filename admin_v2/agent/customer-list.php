<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Customer List</title>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico" />

	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />
	
	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
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
						<h1 class="page-title" data-lan-eng="Customer List">고객 목록</h1>
					</div>

					<div class="content-wrapper-body">
						<div class="card-panel jw-mgt32">

							<div class="list-header">
								<div class="result-count">
									<span data-lan-eng="Search results">검색결과</span> <span class="result-count__num">999</span><span data-lan-eng="items">개</span>
								</div>
								<form class="search-form" action="" method="get">
									<div class="search-field">
										<input type="text" class="search-input" placeholder="고객명 검색" data-lan-eng="Customer Name Search">
										<button type="submit" class="jw-button search-btn" aria-label="검색">
											<span class="search-ico"><img src="../image/search.svg" alt=""></span>
										</button>
									</div>
								</form>
							</div>

							<table class="jw-tableA">
								<colgroup>
									<col class="col-60">
									<col> <!-- 고객명 (auto) -->
									<col> <!-- 이메일 -->
									<col> <!-- 연락처 -->
									<col> <!-- 등록일시 -->
								</colgroup>
								<thead>
									<tr>
										<th class="no is-center">No</th>
										<th data-lan-eng="Customer Name">고객명</th>
										<th data-lan-eng="Email">이메일</th>
										<th data-lan-eng="Contacts">연락처</th>
										<th data-lan-eng="Registration date/time">등록일시</th>
									</tr>
								</thead>
								<tbody>
									<!-- 데이터는 JavaScript에서 동적으로 로드됩니다 -->
									<tr>
										<td colspan="5" class="is-center">로딩 중...</td>
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
							<button type="button" class="jw-button typeA" onclick="downloadCustomers()" data-lan-eng="Download">다운로드</button>

							<div class="layerToggleWrap">
								<button type="button" class="jw-button typeB" onclick="layerToggle(this)"><img src="../image/buttonB.svg" alt="">Add New Customer</button>
								<div class="fab-menu" aria-hidden="true">
									<button type="button" class="jw-button" onclick="closeFabMenu(); goToCustomerRegister();"><img src="../image/plus.svg" alt=""><span data-lan-eng="Single registration">단건 등록</span></button>
									<button type="button" class="jw-button" onclick="closeFabMenu(); openBatchUploadModal();"><img src="../image/upload.svg" alt=""><span data-lan-eng="Batch Registration">일괄 등록</span></button>
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
<script src="../js/agent.js"></script>
<script src="../js/agent-customer-list.js"></script>


<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar.js"></script>

<script>
	// 팝업 메뉴 닫기 함수
	function closeFabMenu() {
		const menu = document.querySelector('.layerToggleWrap .fab-menu');
		setFabMenuState(menu, false);
	}

	// 메뉴 외부 클릭 시 닫기
	document.addEventListener('DOMContentLoaded', function() {
		document.addEventListener('click', function(e) {
			const layerToggleWrap = document.querySelector('.layerToggleWrap');
			if (layerToggleWrap && !layerToggleWrap.contains(e.target)) {
				closeFabMenu();
			}
		});
	});

	function goToCustomerRegister() {
		window.location.href = 'customer-register.php';
	}

	function openCustomerRegisterModal() {
		modal('customer-register-modal.html', '580px', '300px');
	}

	function openBatchUploadModal() {
		modal('customer-batch-upload-modal.html', '600px', '500px');
	}

	function downloadCustomers() {
		// CSV 다운로드
		window.location.href = '../backend/api/agent-api.php?action=downloadCustomers';
	}
</script>

</html>