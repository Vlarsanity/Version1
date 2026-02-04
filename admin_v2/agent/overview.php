<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Operating Status</title>

	<link rel="shortcut icon" href="../image/favicon.ico" />

	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />

	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/agent/css/page-specifics/overview.css?v=<?= time(); ?>"/>
	
</head>

<body>

	<!-- header 들어올 자리 -->
	<header class="layout-header"></header>

	<!-- 본문 영역 -->
	<main class="main-container">

		<div class="wrapper-container">

			<!-- nav 들어올 자리 -->
			<nav class="layout-nav"></nav>

			<section class="main-content">

				<div class="page-header">
					<div class="page-header-left">
						<h1 class="page-title" data-lan-eng="Operating Status">운영현황</h1>
					</div>
					<div class="page-header-right">
						<div class="date-pill-wrapper">
							<time class="page-date" id="current-date" datetime="">January 1, 2025</time>
							<span class="date-icon">
								<svg viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12.5 0C13.0753 0 13.542 0.467 13.542 1.042V2.084H16.667C17.817 2.084 18.75 3.017 18.75 4.167V18.75C18.75 19.829 17.93 20.716 16.879 20.822L16.667 20.834H2.083L1.871 20.822C0.890 20.723 0.110 19.944 0.011 18.963L0 18.75V4.167C0 3.017 0.933 2.084 2.083 2.084H5.208V1.042C5.208 0.467 5.675 0 6.25 0C6.825 0 7.292 0.467 7.292 1.042V2.084H11.458V1.042C11.458 0.467 11.925 0 12.5 0ZM1.562 9.375V18.75C1.562 19.038 1.796 19.271 2.083 19.272H16.667C16.955 19.271 17.188 19.038 17.188 18.75V9.375H1.562ZM2.083 3.646C1.796 3.647 1.562 3.879 1.562 4.167V7.812H17.188V4.167C17.188 3.879 16.955 3.647 16.667 3.646H13.542V4.167C13.542 4.742 13.075 5.209 12.5 5.209C11.925 5.209 11.458 4.742 11.458 4.167V3.646H7.292V4.167C7.292 4.742 6.825 5.209 6.25 5.209C5.675 5.209 5.208 4.742 5.208 4.167V3.646H2.083Z" />
								</svg>
							</span>
						</div>
					</div>
				</div>

				<div class="overview-crd-grid">

					<!-- Payment Status -->
					<article class="payment-card">

						<div class="card-header">
							<h2 class="card-title" data-lan-eng="Payment Status">Payment Status</h2>
							<a href="reservation-list.html" class="card-link">See All →</a>
						</div>

						<div class="payment-grid">
							<!-- Down Payment Wait -->
							<div class="payment-item payment-down">
								<div class="payment-icon">
									<span>💰</span>
								</div>
								<p class="payment-label">Down Payment</p>
								<p class="payment-count" id="down_payment_wait_count">0</p>
								<p class="payment-status">Awaiting</p>
							</div>

							<!-- Advance Payment Wait -->
							<div class="payment-item payment-advance">
								<div class="payment-icon">
									<span>📋</span>
								</div>
								<p class="payment-label">Advance Payment</p>
								<p class="payment-count" id="advance_payment_wait_count">0</p>
								<p class="payment-status">Awaiting</p>
							</div>

							<!-- Balance Wait -->
							<div class="payment-item payment-balance">
								<div class="payment-icon">
									<span>✅</span>
								</div>
								<p class="payment-label">Balance</p>
								<p class="payment-count" id="balance_wait_count">0</p>
								<p class="payment-status">Awaiting</p>
							</div>
						</div>

					</article>

				</div>

				<div class="card-panel">

					<div class="card-header">
						<h2 class="card-title" data-lan-eng="Today's Travel Itinerary">Today's Travel Itinerary</h2>
						<a href="reservation-list.html" class="card-link">See All →</a>
					</div>

				
					<div class="card-panel-header">
						<div class="header-left">
							<div class="header-icon">
								<span>📅</span>
							</div>
							<div class="header-content">
				
								<p class="card-subtitle">
									<strong>0</strong>
									<span>Itineraries</span>
								</p>
							</div>
						</div>
						<div class="header-action">
							<a href="itinerary-list.html" class="show-all-link">Show All</a>
						</div>
					</div>

					<div class="card-panel-body">
						<div class="table-scroll">
							<div class="table-wrapper">
								<table class="data-table">
									<colgroup>
										<col class="col-no">
										<col class="col-product">
										<col class="col-period">
										<col class="col-type">
										<col class="col-travelers">
										<col class="col-guide">
									</colgroup>
									<thead>
										<tr>
											<th>No</th>
											<th data-lan-eng="Product Name">Product Name</th>
											<th data-lan-eng="Travel period">Travel Period</th>
											<th data-lan-eng="Customer Type">Customer Type</th>
											<th data-lan-eng="Number of people">Travelers</th>
											<th data-lan-eng="Assignment Guide">Assigned Guide</th>
										</tr>
									</thead>
									<tbody>
										<!-- 데이터는 JavaScript에서 동적으로 로드됩니다 -->
										<tr>
											<td colspan="6" class="empty-state" data-lan-eng="No travel itineraries for today.">No travel itineraries for today.</td>
										</tr>
									</tbody>
								</table>
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
<script src="../js/agent-overview.js"></script>

<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar.js"></script>
	

</html>