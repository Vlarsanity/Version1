<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Operating Status</title>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico" />

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


	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">


	<!-- Tabulator CSS -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.5.0/css/tabulator.min.css" rel="stylesheet">
	

	<link rel="stylesheet" href="../../admin_v2/super/css/base-table-layout.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/super/css/tabulator-styles.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/super/css/theme-default.css?v=<?= time(); ?>">

	<link rel="stylesheet" href="../../admin_v2/super/css/page-specifics/overview.css?v=<?= time(); ?>" />

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

					</div>

					<div class="content-wrapper-body">
						<div class="overview-card-grid jw-mgt32">
							<!-- 예약 현황 -->
							<article class="card">
								<div>
									<h2 class="card-title" data-lan-eng="Reservation Status">예약 현황</h2>
									<a href="../reservations/reservation-list.html" class="card-link">See All →</a>
								</div>
								<div>
									<ul class="status-list">
										<li class="status-item">
											<i class="dot dot-blue" aria-hidden="true"></i>
											<span class="label" data-lan-eng="Waiting for advance payment deposit">선금 확인 전</span>
											<strong class="count"><span>11</span><span data-lan-eng="Dry">건</span></strong>
										</li>
										<li class="status-item">
											<i class="dot dot-green" aria-hidden="true"></i>
											<span class="label" data-lan-eng="Waiting for balance payment">잔금 확인 전</span>
											<strong class="count" data-lan-eng=""><span>8</span><span data-lan-eng="Dry">건</span></strong>
										</li>
									</ul>
								</div>
							</article>

							<!-- 문의 현황 -->
							<article class="card">
								<div>
									<h2 class="card-title" data-lan-eng="Inquiry Status">문의 현황</h2>
									<a href="../reservations/reservation-list.html" class="card-link">See All →</a>
								</div>
								<div>
									<ul class="status-list">
										<li class="status-item">
											<i class="dot dot-blue" aria-hidden="true"></i>
											<span class="label" data-lan-eng="Unanswered">미답변</span>
											<strong class="count" data-lan-eng=""><span>15</span><span data-lan-eng="Dry">건</span></strong>
										</li>
										<li class="status-item">
											<i class="dot dot-green" aria-hidden="true"></i>
											<span class="label" data-lan-eng="Processing">처리중</span>
											<strong class="count" data-lan-eng=""><span>7</span><span data-lan-eng="Dry">건</span></strong>
										</li>
									</ul>
								</div>
							</article>
						</div>

						<div class="card-panel jw-mgt68">
							<h2 class="card-title" data-lan-eng="Today's Travel Itinerary">오늘 여행 일정</h2>
							<p class="card-subtitle"><strong><span>12</span><span data-lan-eng="Dry">건</span></strong></p>

							<div class="tableA-scroll">
								<div class="jw-tableA typeB">
									<table>
										<colgroup>
											<col style="width:60px;"><!-- No -->
											<col><!-- 상품명 -->
											<col style="width:220px;"><!-- 여행 기간 -->
											<col style="width:120px;"><!-- 고객 유형 -->
											<col style="width:100px;"><!-- 인원 수 -->
											<col style="width:140px;"><!-- 배정 가이드 -->
										</colgroup>
										<thead>
											<tr>
												<th>No</th>
												<th data-lan-eng="Product Name">상품명</th>
												<th data-lan-eng="Travel period">여행 기간</th>
												<th data-lan-eng="Customer Type">고객 유형</th>
												<th data-lan-eng="Number of people">인원 수</th>
												<th data-lan-eng="Assignment Guide">배정 가이드</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="is-center">1</td>
												<td>서울 벚꽃 명소 집중 탐방 5박 6일 패키지 – 전일정 가이드 및 식사 포함, 남이섬·석촌호수·윤중로 포함</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2B</td>
												<td class="is-center">50</td>
												<td class="is-center">김민수</td>
											</tr>
											<tr>
												<td class="is-center">2</td>
												<td>한국 베이직 투어 4박 5일 – 남이섬, 에버랜드 포함</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2B</td>
												<td class="is-center">50</td>
												<td class="is-center">이지훈</td>
											</tr>
											<tr>
												<td class="is-center">3</td>
												<td>단풍 투어 & 설악산 케이블카 4박 5일</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2C</td>
												<td class="is-center">50</td>
												<td class="is-center">이수빈</td>
											</tr>
											<tr>
												<td class="is-center">4</td>
												<td>겨울 패키지 4박 5일 – 에버랜드 & 스키 옵션 포함</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2B</td>
												<td class="is-center">50</td>
												<td class="is-center">최하은</td>
											</tr>
											<tr>
												<td class="is-center">5</td>
												<td>전주·군산 레트로 투어 3박 4일 – 한옥마을 + 철길마을</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2B</td>
												<td class="is-center">50</td>
												<td class="is-center">정예준</td>
											</tr>
											<tr>
												<td class="is-center">6</td>
												<td>낙산 힐링 투어 6박 7일 – 부산·여수·거제 포함</td>
												<td class="is-center">2025-04-19 ~ 2025-04-24</td>
												<td class="is-center">B2C</td>
												<td class="is-center">50</td>
												<td class="is-center">위효빈</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

						</div>

						<div class="card-panel jw-mgt68 sales-range">
							<h2 class="card-title" data-lan-eng="Sales Statistics">매출 통계</h2>

							<h3 class="card-subtitle2 jw-mgt44" data-lan-eng="Select period">기간 선택</h3>
							<div class="jw-cols jw-gap10 jw-mgt16">
								<label class="jw-radio typeA">
									<input type="radio" name="radio1" checked>
									<p class="text">일간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">주간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">월간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">연간</p>
								</label>
							</div>

							<div class="input-box jw-mgt16">
								<input name="" value="2025-01-06 ~ 2025-01-06">
							</div>

							<div class="info-text jw-mgt44" data-lan-eng="Total sales amount (₱)">총 매출액 (₱)</div>
							<div class="info-text2">15,000</div>


							<div class="chart-wrap jw-mgt30">
								<div id="myChart" class="chart"></div>
							</div>

						</div>

						<div class="card-panel jw-mgt68 product-sales-range">
							<h2 class="card-title" data-lan-eng="Sales Status by Product">상품별 판매 현황</h2>
							<h3 class="card-subtitle2 jw-mgt44" data-lan-eng="Select period">기간 선택</h3>

							<div class="jw-cols jw-gap10 jw-mgt16">
								<label class="jw-radio typeA">
									<input type="radio" name="radio1" checked>
									<p class="text">일간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">주간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">월간</p>
								</label>
								<label class="jw-radio typeA">
									<input type="radio" name="radio1">
									<p class="text">연간</p>
								</label>
								<div class="input-box jw-w200">
									<input type="date" name="" value="">
								</div>
							</div>
							<div class="input-box jw-mgt16 jw-w400">
								<input name="" value="2025-01-06 ~ 2025-01-06">
							</div>
							<div class="info-text jw-mgt44" data-lan-eng="Total number of sales">총 판매 건수</div>
							<div class="info-text2">1,000</div>


							<div class="sales-info jw-mgt32">
								<div class="sales-chart">
									<div class="sales-item">
										<p class="sales-title">서울 벚꽃 명소 집중 탐방 5박 6일 패키지 - 전일정 가이드 및 식사 포함, 남이섬·석촌호</p>
										<div class="bar"><span class="bar__fill" style="width:100%"></span></div>
										<div class="value">200건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">한국 베이직 투어 4박 5일 - 남이섬, 에버랜드 포함</p>
										<div class="bar"><span class="bar__fill" style="width:80%"></span></div>
										<div class="value">160건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">단풍 투어 & 설악산 케이블카 4박 5일</p>
										<div class="bar"><span class="bar__fill" style="width:75%"></span></div>
										<div class="value">150건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">겨울 패키지 4박 5일 - 에버랜드 & 스키 옵션 포함</p>
										<div class="bar"><span class="bar__fill" style="width:50%"></span></div>
										<div class="value">100건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">한국 베이직 투어 4박 5일 – 남이섬,전주·군산 레트로 투어 3박 4일 – 한옥마을 + 철길마</p>
										<div class="bar"><span class="bar__fill" style="width:40%"></span></div>
										<div class="value">80건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">남해 힐링 투어 6박 7일 – 부산·여수·거제 포함</p>
										<div class="bar"><span class="bar__fill" style="width:40%"></span></div>
										<div class="value">80건</div>
									</div>

									<div class="sales-item">
										<p class="sales-title">부산&경주 핵심 일주 3박 4일 – 해운대, 불국사 포함</p>
										<div class="bar"><span class="bar__fill" style="width:25%"></span></div>
										<div class="value">50건</div>
									</div>
								</div>
								<div class="sales-product">
									<table class="jw-tableA typeB">
										<colgroup>
											<col style="width:60px;"> <!-- No -->
											<col> <!-- 상품명 -->
											<col style="width:120px;"> <!-- 조회수 -->
											<col style="width:120px;"> <!-- 예약건수 -->
											<col style="width:120px;"> <!-- 예약률 -->
											<col style="width:160px;"> <!-- 판매액 -->
										</colgroup>
										<thead>
											<tr>
												<th>No</th>
												<th data-lan-eng="Product Name">상품명</th>
												<th data-lan-eng="Views">조회수</th>
												<th data-lan-eng="Number of reservations">예약건수</th>
												<th data-lan-eng="Reservation rate">예약률</th>
												<th data-lan-eng="Sales amount">판매액</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="is-center">1</td>
												<td>서울 벚꽃 명소 집중 탐방 5박 6일 패키지 – 전일정 가이드…</td>
												<td class="is-center">2689</td>
												<td class="is-center">200</td>
												<td class="is-center">28.24%</td>
												<td class="is-center">₩2,000,000</td>
											</tr>
											<tr>
												<td class="is-center">2</td>
												<td>한국 베이직 투어 4박 5일 – 남이섬, 에버랜드 포함</td>
												<td class="is-center">2397</td>
												<td class="is-center">160</td>
												<td class="is-center">11.76%</td>
												<td class="is-center">₩800,000</td>
											</tr>
											<tr>
												<td class="is-center">3</td>
												<td>단풍 투어 & 설악산 케이블카 4박 5일</td>
												<td class="is-center">3950</td>
												<td class="is-center">150</td>
												<td class="is-center">8.24%</td>
												<td class="is-center">₩2,500,000</td>
											</tr>
											<tr>
												<td class="is-center">4</td>
												<td>겨울 패키지 4박 5일 – 에버랜드 & 스키 옵션 포함</td>
												<td class="is-center">2782</td>
												<td class="is-center">100</td>
												<td class="is-center">7.06%</td>
												<td class="is-center">₩2,000,000</td>
											</tr>
											<tr>
												<td class="is-center">5</td>
												<td>전주·군산 레트로 투어 3박 4일 – 한옥마을 + 철길마을</td>
												<td class="is-center">398</td>
												<td class="is-center">80</td>
												<td class="is-center">5.88%</td>
												<td class="is-center">₩400,000</td>
											</tr>
											<tr>
												<td class="is-center">6</td>
												<td>남해 힐링 투어 6박 7일 – 부산·여수·거제 포함</td>
												<td class="is-center">2691</td>
												<td class="is-center">70</td>
												<td class="is-center">5.01%</td>
												<td class="is-center">₩490,000</td>
											</tr>
											<tr>
												<td class="is-center">7</td>
												<td>부산&경주 핵심 일주 3박 4일 – 해운대, 불국사 포함</td>
												<td class="is-center">3876</td>
												<td class="is-center">50</td>
												<td class="is-center">4.71%</td>
												<td class="is-center">₩190,000</td>
											</tr>
											<tr>
												<td class="is-center">8</td>
												<td>제주도 환상 일주 4박 5일 – 우도 & 성산일출봉 포함…</td>
												<td class="is-center">1299</td>
												<td class="is-center">48</td>
												<td class="is-center">3.53%</td>
												<td class="is-center">₩390,000</td>
											</tr>
											<tr>
												<td class="is-center">9</td>
												<td>남도 정취 투어 4박 5일 – 목포·순천·보성</td>
												<td class="is-center">693</td>
												<td class="is-center">47</td>
												<td class="is-center">3.53%</td>
												<td class="is-center">₩920,000</td>
											</tr>
											<tr>
												<td class="is-center">10</td>
												<td>강릉&속초 바다 힐링 투어 3박 4일</td>
												<td class="is-center">720</td>
												<td class="is-center">42</td>
												<td class="is-center">2.35%</td>
												<td class="is-center">₩530,000</td>
											</tr>
										</tbody>
									</table>
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


<script>
	document.addEventListener('DOMContentLoaded', () => {
		/* ========= chart A ========= */
		const data = [130, 55, 55, 210, 20, 100, 80, 110, 200, 20, 25, 50, 30, 140, 100, 220, 75, 180, 90, 220, 150, 95, 105, 100];

		// 예시: 이미지처럼 "March, 2025" / "₽2,000"
		const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		const fmt = (n) => n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

		renderBarChart('#myChart', data, {
			max: 220,
			highlights: [3, 19],
			formatTip: (hour, value) => {
				const title = `${months[hour]}, 2025`;
				const amount = `₽${fmt(value * 1000)}`; // 예시: 값×1000을 금액으로 가정
				return `<div class="tip-title">${title}</div><div class="tip-value">${amount}</div>`;
			}
		});
	});

	//  chart A
	function renderBarChart(container, data, opts = {}) {
		const el = (typeof container === 'string') ? document.querySelector(container) : container;
		if (!el) return;
		el.innerHTML = '';
		el.style.position = 'relative';

		const max = (opts.max ?? Math.max(...data)) || 1;
		const highlights = new Set(opts.highlights || []);
		const formatTip = typeof opts.formatTip === 'function' ? opts.formatTip : null;

		const ylabels = document.createElement('div');
		ylabels.className = 'ylabels';
		['200만', '100만', '50만', '10만', '5만', '0'].forEach(t => {
			const s = document.createElement('span');
			s.textContent = t;
			ylabels.appendChild(s);
		});

		const bars = document.createElement('div');
		bars.className = 'bars';
		data.forEach((v, hour) => {
			const b = document.createElement('div');
			b.className = 'bar' + (highlights.has(hour) ? ' highlights' : '');
			b.style.height = Math.max(0, Math.min(100, (v / max) * 100)) + '%';
			b.dataset.x = hour;
			b.dataset.v = v;
			// 툴팁 내용 미리 저장(HTML 허용)
			b.dataset.tip = formatTip ? formatTip(hour, v) : `${hour}시 · ${v}`;
			bars.appendChild(b);
		});

		const tip = document.createElement('div');
		tip.className = 'tooltip';
		el.appendChild(tip);

		bars.addEventListener('mouseover', (e) => {
			const bar = e.target.closest('.bar');
			if (!bar) return;
			tip.innerHTML = bar.dataset.tip;
			tip.classList.add('show');
			placeTip(el, bar, tip);
		});
		bars.addEventListener('mousemove', (e) => {
			const bar = e.target.closest('.bar');
			if (!bar) return;
			placeTip(el, bar, tip);
		});
		bars.addEventListener('mouseout', (e) => {
			if (!e.relatedTarget || !bars.contains(e.relatedTarget)) tip.classList.remove('show');
		});
		bars.addEventListener('click', (e) => {
			const bar = e.target.closest('.bar');
			if (!bar) return;
			bar.classList.toggle('on');
		});

		el.appendChild(ylabels);
		el.appendChild(bars);
	}
	//  chart A
	function placeTip(container, bar, tip) {
		const cr = container.getBoundingClientRect();
		const br = bar.getBoundingClientRect();
		const x = br.left + br.width / 2 - cr.left;
		const y = br.top - cr.top - 10;
		tip.style.left = x + 'px';
		tip.style.top = y + 'px';
	}
</script>





</html>