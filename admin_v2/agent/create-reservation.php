<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create Reservation</title>

	<!-- 언어 설정 스크립트 -->
	<script>
		(function() {
			function getCookie(name) {
				const value = `; ${document.cookie}`;
				const parts = value.split(`; ${name}=`);
				if (parts.length === 2) return parts.pop().split(";").shift();
				return null;
			}
			const lang = getCookie("lang") || "eng";
			const htmlLang =
				document.getElementById("html-lang") || document.documentElement;
			htmlLang.setAttribute("lang", lang === "eng" ? "en" : "ko");
		})();
	</script>

	<!-- 공통 스타일 -->
	<link rel="shortcut icon" href="../image/favicon.ico" />

	<link rel="stylesheet" href="../css/a_reset.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_variables.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../css/a_components.css?v=<?= time(); ?>" />
	<link rel="stylesheet" href="../css/a_contents copy.css?v=<?= time(); ?>" />


	<link rel="stylesheet" href="../../admin_v2/css/dashboard-structure.css?v=<?= time(); ?>">
	<link rel="stylesheet" href="../../admin_v2/agent/css/page-specifics/create-reservation.css?= time(); ?>">

	<!-- Calendar -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

	<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
				<div class="main-content-wrapper">

					<div class="content-wrapper-header">
						<div class="jw-cols jw-between">
							<h1 class="page-title">Create Reservation</h1>
							<div class="jw-center jw-gap8">
								<button type="button" id="test-fill-btn" class="jw-button typeA">
									Test Fill
								</button>
								<button type="button" class="jw-button typeB" id="saveBtn">
									Save
								</button>
							</div>
						</div>


					</div>

					<div class="content-wrapper-body">
						<h2 class="section-title jw-mgt32">Product Information</h2>
						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">
								<div class="grid-item col-span-3">
									<label class="label-name"><span>Product Name</span> <span class="req">*</span></label>
									<div class="jw-center jw-gap20">
										<input type="text" id="product_name" class="form-control jw-wf" placeholder="Please select a product"
											disabled aria-required="true" />
										<button type="button" id="product_search_btn" class="jw-button typeA">
											<img src="../image/search2.svg" alt="" /><span>Product Search</span>
										</button>
									</div>
									<input type="hidden" id="package_id" />
								</div>
								<div class="grid-item">
									<label class="label-name"><span>Travel Start Date</span>
										<span class="req">*</span></label>
									<div class="field-row">
										<input id="departure_date" type="text" class="form-control" placeholder="Please select a date" readonly />
										<button type="button" id="departure_date_btn" class="btn-icon calendar" aria-label="Open calendar" disabled>
											<img src="../image/calendar.svg" alt="" />
										</button>
									</div>
									<input type="hidden" id="departure_date_value" />
								</div>
							</div>
						</div>

						<h2 class="section-title jw-mgt32 hidden">Flight Information</h2>

						<div class="card-panel jw-mgt16 hidden">
							<h3 class="grid-wrap-title">Departure Flight</h3>

							<div class="grid-wrap jw-mgt12">
								<div class="grid-item">
									<label class="label-name" for="out_flight_no">Flight Number</label>
									<input id="out_flight_no" type="text" class="form-control" value="PR467" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="out_depart_dt">Departure Date and Time</label>
									<input id="out_depart_dt" type="text" class="form-control" value="2025-04-19 12:20" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="out_arrive_dt">Arrival Time</label>
									<input id="out_arrive_dt" type="text" class="form-control" value="2025-04-19 14:20" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="out_depart_airport">Departure Point</label>
									<input id="out_depart_airport" type="text" class="form-control" value="Manila (MNL)" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="out_arrive_airport">Destination</label>
									<input id="out_arrive_airport" type="text" class="form-control" value="Incheon (ICN)" disabled />
								</div>

								<div class="grid-item"></div>
							</div>
						</div>

						<div class="jw-cols jw-between jw-mgt32">
							<h2 class="section-title">Customer Information</h2>
							<button type="button" id="customer_search_btn" class="jw-button typeA" onclick="openCustomerSearchModal()">
								Load Customer Information
							</button>
						</div>

						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">
								<div class="grid-item">
									<label for="user_name" class="form-label required">Name</label>
									<input type="text" id="user_name" class="form-control" placeholder="Name" aria-required="true" />
									<input type="hidden" id="customer_account_id" />
								</div>
								<div class="grid-item">
									<label for="user_email" class="form-label required">Email</label>
									<input type="email" id="user_email" class="form-control" placeholder="Email" aria-required="true" />
								</div>
								<div class="grid-item">
									<label for="user_phone" class="form-label required">Contact</label>
									<div class="field-row">
										<select id="country_code" class="select">
											<option value="+63">+63</option>
											<option value="+82">+82</option>
											<option value="+81">+81</option>
											<option value="+1">+1</option>
										</select>
										<input type="text" id="user_phone" class="form-control" placeholder="Phone Number" />
									</div>
								</div>
							</div>
						</div>

						<div class="jw-cols jw-between jw-mgt32">
							<h2 class="section-title">Traveler Information</h2>
							<button type="button" id="load_traveler_customers_btn" class="jw-button typeA" onclick="openTravelCustomerSearchModal()">
								Load Customer Information
							</button>
						</div>

						<div class="card-panel jw-mgt16">
							<div class="jw-mgb12">
								<button type="button" id="add_traveler_btn" class="jw-button typeA">
									+ Add Customer
								</button>
							</div>
							<div class="tableA-scroll jw-mgt20">
								<table class="jw-tableA booking-detail">
									<colgroup>
										<col style="width: 60px" />
										<col style="width: 120px" />
										<col style="width: 180px" />
										<col style="width: 200px" />
										<col style="width: 140px" />
										<col style="width: 200px" />
										<col style="width: 200px" />
										<col style="width: 160px" />
										<col style="width: 120px" />
										<col style="width: 180px" />
										<col style="width: 180px" />
										<col style="width: 180px" />
										<col style="width: 180px" />
										<col style="width: 180px" />
										<col style="width: 200px" />
										<col style="width: 120px" />
									</colgroup>
									<thead>
										<tr>
											<th>No</th>
											<th>Main Traveler</th>
											<th>Type</th>
											<th>Visa Application</th>
											<th>Title</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Gender</th>
											<th>Age</th>
											<th>Date of Birth</th>
											<th>Nationality</th>
											<th>Passport Number</th>
											<th>Passport Issue Date</th>
											<th>Passport Expiry Date</th>
											<th>Passport Photo</th>
											<th>Delete</th>
										</tr>
									</thead>
									<tbody id="travelers-tbody">
										<!-- Traveler information will be dynamically added -->
									</tbody>
								</table>
							</div>
						</div>

						<h2 class="section-title jw-mgt32" data-lan-eng="Reservation Information">Reservation Information</h2>

						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">
								<div class="grid-item">
									<label class="label-name"><span data-lan-eng="Room Options">Room Options </span><span class="req">*</span></label>
									<div>
										<button type="button" id="room_option_btn" class="jw-button typeA" data-lan-eng="Select Room Options">
											Select Room Options
										</button>
									</div>
								</div>

								<!-- multi-editor.js -->
								<div class="grid-item col-span-3">
									<label class="label-name" for="seat_req_editor" data-lan-eng="Airline seat request details">Airline Seat Request Details</label>
									<div class="editor-box-wrap">
										<div class="jw-editor">
											<div class="toolbar">
												<div class="group">
													<button type="button" class="editor-btn ql-bold">
														<img src="../image/editer_bold.svg" />
													</button>
													<button type="button" class="editor-btn ql-italic">
														<img src="../image/editer_italic.svg" />
													</button>
													<button type="button" class="editor-btn ql-underline">
														<img src="../image/editer_underline.svg" />
													</button>
													<button type="button" class="editor-btn ql-strike">
														<img src="../image/editer_strikethrough.svg" />
													</button>

													<!-- 색상 버튼은 this 넘겨주기 -->
													<button type="button" class="editor-btn font-color" onclick="setColor(this);">A</button>
													<button type="button" class="background-color" onclick="setColor(this, 2);"></button>
												</div>

												<div class="group">
													<!-- select도 this 넘겨주기 -->
													<select class="editor-font-size" onchange="fontsize(this);">
														<option value="" selected>Font</option>
														<option value="12px">12px</option>
														<option value="14px">14px</option>
														<option value="16px">16px</option>
														<option value="18px">18px</option>
														<option value="24px">24px</option>
													</select>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-align" value="">
														<img src="../image/editer_left.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="center">
														<img src="../image/editer_center.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="right">
														<img src="../image/editer_right.svg" />
													</button>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-list" value="ordered">
														<img src="../image/editer_ordered.svg" />
													</button>
													<button type="button" class="editor-btn ql-list" value="bullet">
														<img src="../image/editer_unordered.svg" />
													</button>
												</div>

												<div class="group">
													<!-- 이것도 this 넘기게 변경 -->
													<button type="button" class="editor-btn" onclick="removeHtmlTags(this);">
														<img src="../image/editer_removeHtmlTags.svg" />
													</button>
													<button type="button" class="editor-btn" style="font-size: 9px" onclick="toggleHtmlView(this);">
														HTML
													</button>
												</div>

												<div class="group">
													<label class="inputFile">
														<!-- this 넘기기 -->
														<input name="item_img[]" type="file" accept="image/*" multiple onchange="insertImage(this);" />
														<span class="text">Image</span>
														<i class="progress"></i>
													</label>
												</div>
											</div>

											<div class="jweditor" id="seat_req_editor" data-placeholder="Please enter content"></div>
										</div>
									</div>
								</div>

								<!-- multi-editor.js -->
								<div class="grid-item col-span-3">
									<label class="label-name" for="etc_req_editor" data-lan-eng="Other requests">Other Requests</label>
									<div class="editor-box-wrap">
										<div class="jw-editor">
											<div class="toolbar">
												<div class="group">
													<button type="button" class="editor-btn ql-bold">
														<img src="../image/editer_bold.svg" />
													</button>
													<button type="button" class="editor-btn ql-italic">
														<img src="../image/editer_italic.svg" />
													</button>
													<button type="button" class="editor-btn ql-underline">
														<img src="../image/editer_underline.svg" />
													</button>
													<button type="button" class="editor-btn ql-strike">
														<img src="../image/editer_strikethrough.svg" />
													</button>

													<!-- 색상 버튼은 this 넘겨주기 -->
													<button type="button" class="editor-btn font-color" onclick="setColor(this);">A</button>
													<button type="button" class="background-color" onclick="setColor(this, 2);"></button>
												</div>

												<div class="group">
													<!-- select도 this 넘겨주기 -->
													<select class="editor-font-size" onchange="fontsize(this);">
														<option value="" selected>Font</option>
														<option value="12px">12px</option>
														<option value="14px">14px</option>
														<option value="16px">16px</option>
														<option value="18px">18px</option>
														<option value="24px">24px</option>
													</select>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-align" value="">
														<img src="../image/editer_left.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="center">
														<img src="../image/editer_center.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="right">
														<img src="../image/editer_right.svg" />
													</button>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-list" value="ordered">
														<img src="../image/editer_ordered.svg" />
													</button>
													<button type="button" class="editor-btn ql-list" value="bullet">
														<img src="../image/editer_unordered.svg" />
													</button>
												</div>

												<div class="group">
													<!-- 이것도 this 넘기게 변경 -->
													<button type="button" class="editor-btn" onclick="removeHtmlTags(this);">
														<img src="../image/editer_removeHtmlTags.svg" />
													</button>
													<button type="button" class="editor-btn" style="font-size: 9px" onclick="toggleHtmlView(this);">
														HTML
													</button>
												</div>

												<div class="group">
													<label class="inputFile">
														<!-- this 넘기기 -->
														<input name="item_img[]" type="file" accept="image/*" multiple onchange="insertImage(this);" />
														<span class="text">Image</span>
														<i class="progress"></i>
													</label>
												</div>
											</div>

											<div class="jweditor" id="etc_req_editor" data-placeholder="Please enter content"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<h2 class="section-title jw-mgt32" data-lan-eng="Agent Notes">Agent Note</h2>

						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">
								<!-- multi-editor.js -->
								<div class="grid-item col-span-3">
									<label class="label-name" for="memo_editor" data-lan-eng="Note">Note</label>
									<div class="editor-box-wrap">
										<div class="jw-editor">
											<div class="toolbar">
												<div class="group">
													<button type="button" class="editor-btn ql-bold">
														<img src="../image/editer_bold.svg" />
													</button>
													<button type="button" class="editor-btn ql-italic">
														<img src="../image/editer_italic.svg" />
													</button>
													<button type="button" class="editor-btn ql-underline">
														<img src="../image/editer_underline.svg" />
													</button>
													<button type="button" class="editor-btn ql-strike">
														<img src="../image/editer_strikethrough.svg" />
													</button>

													<!-- 색상 버튼은 this 넘겨주기 -->
													<button type="button" class="editor-btn font-color" onclick="setColor(this);">A</button>
													<button type="button" class="background-color" onclick="setColor(this, 2);"></button>
												</div>

												<div class="group">
													<!-- select도 this 넘겨주기 -->
													<select class="editor-font-size" onchange="fontsize(this);">
														<option value="" selected>Font</option>
														<option value="12px">12px</option>
														<option value="14px">14px</option>
														<option value="16px">16px</option>
														<option value="18px">18px</option>
														<option value="24px">24px</option>
													</select>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-align" value="">
														<img src="../image/editer_left.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="center">
														<img src="../image/editer_center.svg" />
													</button>
													<button type="button" class="editor-btn ql-align" value="right">
														<img src="../image/editer_right.svg" />
													</button>
												</div>

												<div class="group">
													<button type="button" class="editor-btn ql-list" value="ordered">
														<img src="../image/editer_ordered.svg" />
													</button>
													<button type="button" class="editor-btn ql-list" value="bullet">
														<img src="../image/editer_unordered.svg" />
													</button>
												</div>

												<div class="group">
													<!-- 이것도 this 넘기게 변경 -->
													<button type="button" class="editor-btn" onclick="removeHtmlTags(this);">
														<img src="../image/editer_removeHtmlTags.svg" />
													</button>
													<button type="button" class="editor-btn" style="font-size: 9px" onclick="toggleHtmlView(this);">
														HTML
													</button>
												</div>

												<div class="group">
													<label class="inputFile">
														<!-- this 넘기기 -->
														<input name="item_img[]" type="file" accept="image/*" multiple onchange="insertImage(this);" />
														<span class="text">Image</span>
														<i class="progress"></i>
													</label>
												</div>
											</div>

											<div class="jweditor" id="memo_editor" data-placeholder="Please enter content"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<h2 class="section-title jw-mgt32" data-lan-eng="Payment Information">Payment Information</h2>

						<div class="card-panel jw-mgt16">
							<div class="grid-wrap">
								<!-- Bank Account Information -->
								<div class="grid-item col-span-3">
									<div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; padding: 16px; 
															margin-bottom: 16px;">
										<h4 style="font-size: 14px; font-weight: 600; color: #0369a1; margin-bottom: 12px;">
											Please deposit to the account below:
										</h4>
										<div style="font-size: 14px; color: #1e293b">
											<p style="margin: 4px 0"><strong>Bank:</strong> BDO Bank</p>
											<p style="margin: 4px 0">
												<strong>Account No.</strong> 004920342791
											</p>
											<p style="margin: 4px 0">
												<strong>Account Name:</strong> TRAVEL ESCAPE TRAVEL AND
												TOURS
											</p>
										</div>
										<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #bae6fd;">
											<p style="font-size: 13px; color: #dc2626; font-weight: 500; margin: 0;">
												<strong>⚠ Notice:</strong> For products with less than 30
												days until departure from the reservation date, all payments
												must be completed within 3 days.
											</p>
										</div>
									</div>
								</div>

								<div class="grid-item">
									<label class="label-name" for="pay_total" data-lan-eng="Order Amount (₱)">Order Amount (₱)</label>
									<input id="pay_total" type="text" class="form-control" value="0" disabled />
								</div>
								<div class="grid-item"></div>
								<div class="grid-item"></div>

								<!-- 1단계: 선금 (Down Payment) -->
								<div class="grid-item col-span-3">
									<h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: #2563eb;" data-lan-eng="Step 1: Down Payment">
										Step 1: Down Payment
									</h3>
								</div>

								<div class="grid-item">
									<label class="label-name" for="pay_down_payment"><span data-lan-eng="Down Payment (₱)">Down Payment (₱)</span></label>
									<input id="pay_down_payment" type="text" class="form-control" value="5,000" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="down_payment_due"><span data-lan-eng="Down Payment Deadline">Down Payment Deadline</span></label>
									<input id="down_payment_due" type="text" class="form-control" value="Within 3 days" data-lan-eng-value="Within 3 days" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="down_payment_file" data-lan-eng="Down Payment Proof File">Down Payment Proof File (Optional)</label>
									<div class="field-col">
										<input id="down_payment_file_input" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.heic,.heif" style="display: none" />
										<button type="button" class="jw-button typeE" id="down_payment_file_upload_btn">
											<img src="../image/upload.svg" alt="" /><span data-lan-eng="File upload">File Upload</span>
										</button>
										<div class="file-info" id="down_payment_file_info" style="display: none; margin-top: 12px">
											<div class="file-info-item" style="display: flex; align-items: center; justify-content: space-between;
												padding: 12px; background: #f7faff; border-radius: 10px;">
												<span class="file-name" id="down_payment_file_name" style="font-weight: 500"></span>
												<button type="button" class="file-remove" id="down_payment_file_remove" aria-label="Remove file"
													style="background: none; border: none; cursor: pointer; padding: 4px;">
													<img src="../image/button-close2.svg" alt="" style="width: 16px; height: 16px" />
												</button>
											</div>
										</div>
										<p class="help-text" style="color: #d58b00; margin-top: 8px" data-lan-eng="Can be uploaded now or later in reservation details.">
											Can be uploaded now or later in reservation details.
										</p>
									</div>
								</div>

								<!-- Down Payment 환불 불가 안내 -->
								<div class="grid-item col-span-3">
									<p style="color: #dc2626; font-weight: 500; font-size: 14px; margin: 8px 0;"
										data-lan-eng="⚠ Notice: Down payment is non-refundable.">
										⚠ Notice: Down payment is non-refundable.
									</p>
								</div>

								<!-- 2단계: 중도금 (Advance Payment) -->
								<div class="grid-item col-span-3">
									<h3 style="font-size: 16px; font-weight: 600; margin: 24px 0 12px; color: #2563eb;"
										data-lan-eng="Step 2: Advance Payment">
										Step 2: Advance Payment
									</h3>
								</div>

								<div class="grid-item">
									<label class="label-name" for="pay_advance_payment"><span data-lan-eng="Advance Payment (₱)">Advance Payment (₱)</span></label>
									<input id="pay_advance_payment" type="text" class="form-control" value="10,000" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="advance_payment_due"><span data-lan-eng="Advance Payment Deadline">Advance Payment Deadline</span></label>
									<input id="advance_payment_due" type="text" class="form-control" value="Within 30 days after down payment approval"
										data-lan-eng-value="Within 30 days after down payment approval" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name"><span data-lan-eng="Advance Payment Proof File">Advance Payment Proof File</span></label>
									<div class="field-col">
										<p class="help-text" style="color: #6b7280; margin: 0" data-lan-eng="Can only be uploaded after reservation in details page.">
											Can only be uploaded after reservation in details page.
										</p>
									</div>
								</div>

								<!-- Advance Payment 환불 불가 안내 -->
								<div class="grid-item col-span-3">
									<p style="color: #dc2626; font-weight: 500; font-size: 14px; margin: 8px 0;" data-lan-eng="⚠ Notice: Advance payment is non-refundable.">
										⚠ Notice: Advance payment is non-refundable.
									</p>
								</div>

								<!-- 3단계: 잔금 (Balance) -->
								<div class="grid-item col-span-3">
									<h3 style="font-size: 16px; font-weight: 600; margin: 24px 0 12px; color: #2563eb;" data-lan-eng="Step 3: Balance">
										Step 3: Balance
									</h3>
								</div>

								<div class="grid-item">
									<label class="label-name" for="pay_balance"><span data-lan-eng="Balance (₱)">Balance (₱)</span></label>
									<input id="pay_balance" type="text" class="form-control" value="0" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name" for="balance_due"><span data-lan-eng="Balance Payment Deadline">Balance Payment Deadline</span></label>
									<input id="balance_due" type="text" class="form-control" value="30 days before departure"
										data-lan-eng-value="30 days before departure" disabled />
								</div>

								<div class="grid-item">
									<label class="label-name"><span data-lan-eng="Balance Proof File">Balance Proof File</span></label>
									<div class="field-col">
										<p class="help-text" style="color: #6b7280; margin: 0" data-lan-eng="Can only be uploaded after reservation in details page.">
											Can only be uploaded after reservation in details page.
										</p>
									</div>
								</div>
							</div>
						</div>


					</div>

				</div>

			</section>

		</div>

	</main>

</body>


<!-- 날짜 선택 캘린더 모달 -->
<div id="date-picker-modal" class="modal" style="display: none">
	<div class="modal-content modal-large">
		<div class="modal-header">
			<h3 data-lan-eng="Select Travel Date">Select Travel Date</h3>
			<button type="button" class="modal-close" onclick="closeModal('date-picker-modal')">
				<img src="../image/button-close2.svg" alt="" />
			</button>
		</div>
		<div class="modal-body">
			<div id="calendar-container">
				<div class="calendar-nav">
					<button type="button"	id="calendar-prev-month" class="calendar-nav-btn">
						<img src="../image/arrow4.svg" alt="" />
					</button>
					<div id="calendar-month-display" class="calendar-month-text"></div>
					<button type="button" id="calendar-next-month" class="calendar-nav-btn">
						<img src="../image/arrow4.svg" alt="" style="transform: rotate(180deg)" />
					</button>
				</div>
				<table class="calendar" role="grid">
					<thead>
						<tr>
							<th data-lan-eng="Sun">Sun</th>
							<th data-lan-eng="Mon">Mon</th>
							<th data-lan-eng="Tue">Tue</th>
							<th data-lan-eng="Wed">Wed</th>
							<th data-lan-eng="Thu">Thu</th>
							<th data-lan-eng="Fri">Fri</th>
							<th data-lan-eng="Sat">Sat</th>
						</tr>
					</thead>
					<tbody id="calendar-body">
						<!-- 캘린더는 JavaScript에서 동적으로 생성됩니다 -->
					</tbody>
				</table>
				<div id="calendar-info" class="calendar-info"></div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="jw-button typeB" id="confirm-date-selection" data-lan-eng="Confirm">Confirm</button>
			<button type="button" class="jw-button typeD" onclick="closeModal('date-picker-modal')" data-lan-eng="Cancel">Cancel</button>
		</div>
	</div>
</div>

<!-- 상품 검색 모달 -->
<div id="product-search-modal" class="modal" style="display: none">
	<div class="modal-content modal-large">
		<div class="modal-header">
			<h3 data-lan-eng="Product Search">Product Search</h3>
			<button type="button" class="modal-close" onclick="closeModal('product-search-modal')">
				<img src="../image/button-close2.svg" alt="" />
			</button>
		</div>
		<div class="modal-body">
			<div class="search-box">
				<input type="text" id="product-search-input" class="form-control" placeholder="Enter product name" data-lan-eng-placeholder="Enter product name" />
				<button type="button" id="product-search-submit" class="jw-button typeA" data-lan-eng="Search">Search</button>
			</div>
			<div class="search-results" id="product-search-results">
				<!-- 검색 결과가 여기에 표시됩니다 -->
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="jw-button typeB" onclick="confirmProductSelection()" data-lan-eng="Confirm">Confirm</button>
			<button type="button"	class="jw-button typeD" onclick="closeModal('product-search-modal')" data-lan-eng="Cancel">Cancel</button>
		</div>
	</div>
</div>

<!-- 고객 검색 모달 -->
<div id="customer-search-modal" class="modal" style="display: none">
	<div class="modal-content modal-large">
		<div class="modal-header">
			<h3 data-lan-eng="Customer Search">Customer Search</h3>
			<button type="button" class="modal-close" onclick="closeModal('customer-search-modal')">
				<img src="../image/button-close2.svg" alt="" />
			</button>
		</div>
		<div class="modal-body">
			<div class="search-box">
				<input type="text" id="customer-search-input" class="form-control" placeholder="Search by name, email, or contact"
					data-lan-eng-placeholder="Search by name, email, or contact" />
				<button type="button" id="customer-search-submit" class="jw-button typeA" data-lan-eng="Search">Search</button>
			</div>
			<div class="table-container">
				<table class="jw-tableA">
					<colgroup>
						<col style="width: 40px" />
						<col />
						<col style="width: 100px" />
						<col style="width: 120px" />
						<col style="width: 150px" />
						<col />
						<col style="width: 120px" />
						<col style="width: 150px" />
						<col style="width: 120px" />
					</colgroup>
					<thead>
						<tr>
							<th></th>
							<th data-lan-eng="Name">Name</th>
							<th data-lan-eng="Gender">Gender</th>
							<th data-lan-eng="Date of Birth">Date of Birth</th>
							<th data-lan-eng="Contact">Contact</th>
							<th data-lan-eng="Email">Email</th>
							<th data-lan-eng="Nationality">Nationality</th>
							<th data-lan-eng="Passport Number">Passport Number</th>
							<th data-lan-eng="Passport Expiry Date">Passport Expiry Date</th>
						</tr>
					</thead>
					<tbody id="customer-search-results">
						<tr>
							<td colspan="9" class="is-center" data-lan-eng="No search results">No search results</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="jw-pagebox" id="customer-pagination" role="navigation"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="jw-button typeB" onclick="confirmCustomerSelection()" data-lan-eng="Confirm">Confirm</button>
			<button type="button" class="jw-button typeD" onclick="closeModal('customer-search-modal')" data-lan-eng="Cancel">Cancel</button>
		</div>
	</div>
</div>

<!-- 여행 고객 검색 모달 (복수 선택) -->
<div id="travel-customer-search-modal" class="modal" style="display: none">
	<div class="modal-content modal-large">
		<div class="modal-header">
			<h3 data-lan-eng="Customer Search">Customer Search</h3>
			<button type="button" class="modal-close" onclick="closeModal('travel-customer-search-modal')">
				<img src="../image/button-close2.svg" alt="" />
			</button>
		</div>
		<div class="modal-body">
			<div class="search-box">
				<input type="text" id="travel-customer-search-input" class="form-control" placeholder="Search by name, email, or contact"
					data-lan-eng-placeholder="Search by name, email, or contact" />
				<button type="button" id="travel-customer-search-submit" class="jw-button typeA" data-lan-eng="Search">Search</button>
			</div>
			<div class="table-container">
				<table class="jw-tableA">
					<colgroup>
						<col style="width: 40px" />
						<col />
						<col style="width: 100px" />
						<col style="width: 120px" />
						<col style="width: 150px" />
						<col />
						<col style="width: 120px" />
						<col style="width: 150px" />
						<col style="width: 120px" />
					</colgroup>
					<thead>
						<tr>
							<th></th>
							<th data-lan-eng="Name">Name</th>
							<th data-lan-eng="Gender">Gender</th>
							<th data-lan-eng="Date of Birth">Date of Birth</th>
							<th data-lan-eng="Contact">Contact</th>
							<th data-lan-eng="Email">Email</th>
							<th data-lan-eng="Nationality">Nationality</th>
							<th data-lan-eng="Passport Number">Passport Number</th>
							<th data-lan-eng="Passport Expiry Date">Passport Expiry Date</th>
						</tr>
					</thead>
					<tbody id="travel-customer-search-results">
						<tr>
							<td colspan="9" class="is-center" data-lan-eng="No search results">No search results</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="jw-pagebox" id="travel-customer-pagination" role="navigation"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="jw-button typeB" onclick="confirmTravelCustomerSelection()" data-lan-eng="Select">Select</button>
			<button type="button" class="jw-button typeD" onclick="closeModal('travel-customer-search-modal')" data-lan-eng="Cancel">Cancel</button>
		</div>
	</div>
</div>

<!-- 룸 옵션 선택 모달 -->
<div id="room-option-modal" class="modal" style="display: none">
	<div class="modal-content modal-large">
		<div class="modal-header">
			<h3 data-lan-eng="Select Room Options">Select Room Options</h3>
			<button type="button" class="modal-close" onclick="closeModal('room-option-modal')">
				<img src="../image/button-close2.svg" alt="" />
			</button>
		</div>
		<div class="modal-body">
			<!-- Current Room Combination Banner -->
			<div id="room-combination-banner" class="room-combination-banner">
				<div class="room-combination-text">
					<span data-lan-eng="Current Room Combination">Current Room Combination</span>
					<span id="room-combination-count">(0/0 <span data-lan-eng="People">People</span>)</span>
				</div>
				<div class="room-combination-note" data-lan-eng="*Children (2 years old or younger) are not included in the room occupancy.">
					*Children (2 years old or younger) are not included in the room occupancy.
				</div>
			</div>

			<!-- Notice -->
			<div class="room-option-notice">
				<p data-lan-eng="* All rooms are based on double occupancy. If a customer wants a private room or is traveling alone, Single Room + Single Supplement Surcharge will be applied.">
					* All rooms are based on double occupancy. If a customer wants a 	private room or is traveling alone, Single Room + Single
					Supplement Surcharge will be applied.
				</p>
			</div>

			<div class="room-option-container">
				<!-- Left: Room Type Selection -->
				<div class="room-option-left">
					<div id="room-option-list">
						<!-- Room option list will be displayed here -->
					</div>
				</div>

				<!-- Right: Order Summary -->
				<div class="room-option-right">
					<div class="order-summary">
						<h4 data-lan-eng="Order Summary">Order Summary</h4>
						<div id="order-summary-list">
							<!-- Order summary will be displayed here -->
						</div>
						<div class="order-amount-section">
							<div class="order-amount-label" data-lan-eng="Order Amount">
								Order Amount
							</div>
							<div class="order-amount-value" id="order-amount-value">
								0 (P)
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" id="confirm-room-selection-btn" class="jw-button typeB" onclick="confirmRoomSelection()" data-lan-eng="Selection complete"
				disabled>
				Selection Complete
			</button>
		</div>
	</div>
</div>


<!-- 기본 스크립트 -->
<script src="../js/default.js"></script>
<script src="../js/agent.js"></script>
<script src="../js/agent-create-reservation.js"></script>
<script src="../js/datepicker.js"></script>
<script src="../js/multi-editor.js"></script>



<!-- Initialize Navbar and Sidebar -->
<script src="../../admin_v2/general/functions/js/init-nav-sidebar.js"></script>

</html>