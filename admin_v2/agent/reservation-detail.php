<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>SMART TRAVEL ADMIN</title>

	<!-- Common Styles -->
	<link rel="shortcut icon" href="../image/favicon.ico">
	<link rel="stylesheet" href="../css/a_reset.css">
	<link rel="stylesheet" href="../css/a_variables.css">
	<link rel="stylesheet" href="../css/a_components.css">
	<link rel="stylesheet" href="../css/a_contents.css">
</head>

<body>

	<!-- Header placeholder -->
	<header class="layout-header"></header>

	<!-- Main content area -->
	<main class="layout-main">
		<!-- Navigation placeholder -->
		<nav class="layout-nav"></nav>

		<section class="layout-content">
			<div class="page-toolbar">
				<a href="reservation-list.html" class="jw-button jw-button-back" aria-label="Return to list">
					<img src="../image/arrow4.svg" alt="">
					<span>Return to list</span>
				</a>

				<div class="page-toolbar-actions">
					<button type="button" class="jw-button typeB">Save</button>
				</div>
			</div>

			<h1 class="page-title">Reservation Details</h1>
			
			<div class="tab-wrap jw-mgt32">
				<p>Product and Reservation Information</p>
			</div>

			<h2 class="section-title jw-mgt32">Product Information</h2>
			<div class="card-panel jw-mgt16">
				<div class="grid-wrap">

					<div class="grid-item col-span-3">
						<label class="label-name" for="product_name">Product Name</label>
						<input id="product_name" type="text" class="form-control"
							value="Seoul Cherry Blossom Highlights 6-Day, 5-Night Package – Includes Full Itinerary Guide and Meals, with Visits to Nami Island, Seokchon Lake, and Yunjung-ro" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="trip_range">Travel Period</label>
						<input id="trip_range" type="text" class="form-control" value="2025-04-19 - 2025-04-24" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="meet_time">Meeting Time</label>
						<input id="meet_time" type="text" class="form-control" value="2025-04-19 09:00" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="meet_place">Meeting Place</label>
						<input id="meet_place" type="text" class="form-control" value="Incheon International Airport Terminal 2" disabled>
					</div>

				</div>
			</div>


			<h2 class="section-title jw-mgt32">Reservation Information</h2>
			<div class="card-panel jw-mgt16">
				<div class="grid-wrap">

					<div class="grid-item">
						<label class="label-name" for="res_no">Reservation Number</label>
						<input id="res_no" type="text" class="form-control" value="23490871349" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="res_datetime">Reservation Date and Time</label>
						<input id="res_datetime" type="text" class="form-control" value="2025-12-01 12:12" disabled>
					</div>

					<div class="grid-item"></div>

					<div class="grid-item">
						<label class="label-name" for="res_people">Number of People</label>
						<input id="res_people" type="text" class="form-control" value="Adult x1, Child x2" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="room_opt">Room Options</label>
						<input id="room_opt" type="text" class="form-control" value="Standard x1, Single Room x1" disabled>
					</div>

					<div class="grid-item"></div>

					<div class="grid-item col-span-3">
						<label class="label-name" for="seat_req">Airline Seat Request Details</label>
						<div class="editor-box-wrap">
							<textarea id="seat_req" rows="6" style="resize:none;" disabled readonly></textarea>
						</div>
					</div>

					<div class="grid-item col-span-3">
						<label class="label-name" for="etc_req">Other Requests</label>
						<div class="editor-box-wrap">
							<textarea id="etc_req" rows="6" style="resize:none;" disabled readonly></textarea>
						</div>
					</div>

				</div>
			</div>


			<h2 class="section-title jw-mgt32">Payment Information (3 Steps)</h2>
			<div class="card-panel jw-mgt16">
				<div class="grid-wrap">

					<!-- Bank Account Information -->
					<div class="grid-item col-span-3">
						<div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
							<h4 style="font-size: 14px; font-weight: 600; color: #0369a1; margin-bottom: 12px;">Please deposit to the account below:</h4>
							<div style="font-size: 14px; color: #1e293b;">
								<p style="margin: 4px 0;"><strong>Bank:</strong> BDO Bank</p>
								<p style="margin: 4px 0;"><strong>Account No.</strong> 004920342791</p>
								<p style="margin: 4px 0;"><strong>Account Name:</strong> TRAVEL ESCAPE TRAVEL AND TOURS</p>
							</div>
							<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #bae6fd;">
								<p style="font-size: 13px; color: #dc2626; font-weight: 500; margin: 0;">
									<strong>⚠ Notice:</strong> For products with less than 30 days until departure from the reservation date, all payments must be completed within 3 days.
								</p>
							</div>
						</div>
					</div>

					<div class="grid-item col-span-3">
						<label class="label-name" for="order_amount">Total Amount (₱)</label>
						<input id="order_amount" type="text" class="form-control" value="15,000" disabled>
					</div>

					<!-- Step 1: Down Payment -->
					<div class="grid-item col-span-3">
						<h3 style="font-size:16px; font-weight:600; margin-top:20px; margin-bottom:12px; color:#2563eb;">Step 1: Down Payment</h3>
					</div>

					<div class="grid-item">
						<label class="label-name" for="down_payment_amount">Down Payment (₱)</label>
						<input id="down_payment_amount" type="text" class="form-control" value="5,000" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="down_payment_due">Down Payment Deadline</label>
						<input id="down_payment_due" type="text" class="form-control" value="2025-12-04" disabled>
					</div>

					<div class="grid-item"></div>

					<div class="grid-item col-span-3 file-field">
						<label class="label-name"><span>Down Payment Proof File</span></label>
						<input id="down_payment_file_input" type="file" accept="image/*,.pdf" style="display:none;">
						<div class="cell" id="down_payment_proof_container">
							<div class="field-row">
								<div class="file-display">
									<img src="../image/file.svg" alt="">
									<span id="down_payment_proof_name">No file uploaded</span>
								</div>
								<i></i>
								<div class="file-actions">
									<button type="button" class="jw-button typeC" id="down_payment_file_upload_btn">Upload</button>
									<button type="button" class="btn-icon file" id="down_payment_proof_download" disabled>
										<img src="../image/buttun-download.svg" alt="">
									</button>
									<button type="button" class="btn-icon file" id="down_payment_proof_remove" aria-label="Delete" disabled>
										<img src="../image/button-close2.svg" alt="">
									</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Down Payment Rejection Reason -->
					<div class="grid-item col-span-3" id="down_payment_rejection_container" style="display: none;">
						<div style="background-color: #fef2f2; border: 1px solid #dc2626; border-radius: 8px; padding: 12px 16px; margin-top: 8px;">
							<p style="color: #dc2626; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
								Payment Rejected
							</p>
							<p style="color: #7f1d1d; font-size: 13px; margin: 0;" id="down_payment_rejection_reason">
								-
							</p>
						</div>
					</div>

					<!-- Step 2: Second Payment -->
					<div class="grid-item col-span-3">
						<h3 style="font-size:16px; font-weight:600; margin-top:20px; margin-bottom:12px; color:#2563eb;">Step 2: Second Payment</h3>
					</div>

					<div class="grid-item">
						<label class="label-name" for="advance_payment_amount">Second Payment (₱)</label>
						<input id="advance_payment_amount" type="text" class="form-control" value="10,000" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="advance_payment_due">Second Payment Deadline</label>
						<input id="advance_payment_due" type="text" class="form-control" value="-" disabled>
					</div>

					<div class="grid-item"></div>

					<div class="grid-item col-span-3 file-field">
						<label class="label-name"><span>Second Payment Proof File</span></label>
						<input id="advance_payment_file_input" type="file" accept="image/*,.pdf" style="display:none;">
						<div class="cell" id="advance_payment_proof_container">
							<div class="field-row">
								<div class="file-display">
									<img src="../image/file.svg" alt="">
									<span id="advance_payment_proof_name">Available after down payment confirmation</span>
								</div>
								<i></i>
								<div class="file-actions">
									<button type="button" class="jw-button typeC" id="advance_payment_file_upload_btn" disabled>Upload</button>
									<button type="button" class="btn-icon file" id="advance_payment_proof_download" disabled>
										<img src="../image/buttun-download.svg" alt="">
									</button>
									<button type="button" class="btn-icon file" id="advance_payment_proof_remove" aria-label="Delete" disabled>
										<img src="../image/button-close2.svg" alt="">
									</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Second Payment Rejection Reason -->
					<div class="grid-item col-span-3" id="advance_payment_rejection_container" style="display: none;">
						<div style="background-color: #fef2f2; border: 1px solid #dc2626; border-radius: 8px; padding: 12px 16px; margin-top: 8px;">
							<p style="color: #dc2626; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
								Payment Rejected
							</p>
							<p style="color: #7f1d1d; font-size: 13px; margin: 0;" id="advance_payment_rejection_reason">
								-
							</p>
						</div>
					</div>

					<!-- Step 3: Balance -->
					<div class="grid-item col-span-3">
						<h3 style="font-size:16px; font-weight:600; margin-top:20px; margin-bottom:12px; color:#2563eb;">Step 3: Balance</h3>
					</div>

					<div class="grid-item">
						<label class="label-name" for="balance_amount">Balance (₱)</label>
						<input id="balance_amount" type="text" class="form-control" value="0" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="balance_due">Balance Payment Deadline</label>
						<input id="balance_due" type="text" class="form-control" value="2025-03-20" disabled>
					</div>

					<div class="grid-item"></div>

					<div class="grid-item col-span-3 file-field">
						<label class="label-name"><span>Balance Proof File</span></label>
						<input id="balance_file_input" type="file" accept="image/*,.pdf" style="display:none;">
						<div class="cell" id="balance_proof_container">
							<div class="field-row">
								<div class="file-display">
									<img src="../image/file.svg" alt="">
									<span id="balance_proof_name">Available after advance payment confirmation</span>
								</div>
								<i></i>
								<div class="file-actions">
									<button type="button" class="jw-button typeC" id="balance_file_upload_btn" disabled>Upload</button>
									<button type="button" class="btn-icon file" id="balance_proof_download" disabled>
										<img src="../image/buttun-download.svg" alt="">
									</button>
									<button type="button" class="btn-icon file" id="balance_proof_remove" aria-label="Delete" disabled>
										<img src="../image/button-close2.svg" alt="">
									</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Balance Rejection Reason -->
					<div class="grid-item col-span-3" id="balance_rejection_container" style="display: none;">
						<div style="background-color: #fef2f2; border: 1px solid #dc2626; border-radius: 8px; padding: 12px 16px; margin-top: 8px;">
							<p style="color: #dc2626; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
								Payment Rejected
							</p>
							<p style="color: #7f1d1d; font-size: 13px; margin: 0;" id="balance_rejection_reason">
								-
							</p>
						</div>
					</div>

				</div>
			</div>


			<h2 class="section-title jw-mgt32">Agent Notes</h2>
			<div class="card-panel jw-mgt16">
				<div class="grid-wrap">
					<div class="grid-item col-span-3">
						<label class="label-name" for="agent_memo">Note</label>
						<div class="editor-box-wrap">
							<textarea id="agent_memo" rows="8" placeholder="This is a message written by the agent" style="resize:none;" disabled readonly></textarea>
						</div>
					</div>
				</div>
			</div>


			<h2 class="section-title jw-mgt32">Flight Information</h2>
			<div class="card-panel jw-mgt16">
				<p style="color: #dc2626; font-size: 13px; margin-bottom: 16px; padding: 8px 12px; background-color: #fef2f2; border-radius: 6px; border-left: 3px solid #dc2626;">
					<strong>Note:</strong> Times shown are based on Korea Standard Time (KST). Please subtract 1 hour for Philippine Time (PHT).
				</p>
				<h3 class="grid-wrap-title">Departure Flight</h3>

				<div class="grid-wrap jw-mgt12">
					<div class="grid-item">
						<label class="label-name" for="out_flight_no">Flight Number</label>
						<input id="out_flight_no" type="text" class="form-control" value="PR467" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="out_depart_dt">Departure Date and Time</label>
						<input id="out_depart_dt" type="text" class="form-control" value="2025-04-19 12:20" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="out_arrive_dt">Arrival Time</label>
						<input id="out_arrive_dt" type="text" class="form-control" value="2025-04-19 14:20" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="out_depart_airport">Departure Point</label>
						<input id="out_depart_airport" type="text" class="form-control" value="Manila (MNL)" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="out_arrive_airport">Destination</label>
						<input id="out_arrive_airport" type="text" class="form-control" value="Incheon (ICN)" disabled>
					</div>

					<div class="grid-item"></div>
				</div>
			</div>
			
			<div class="card-panel jw-mgt16">
				<h3 class="grid-wrap-title">Return Flight</h3>

				<div class="grid-wrap jw-mgt12">
					<div class="grid-item">
						<label class="label-name" for="in_flight_no">Flight Number</label>
						<input id="in_flight_no" type="text" class="form-control" value="PR468" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="in_depart_dt">Departure Date and Time</label>
						<input id="in_depart_dt" type="text" class="form-control" value="2025-04-24 15:05" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="in_arrive_dt">Arrival Time</label>
						<input id="in_arrive_dt" type="text" class="form-control" value="2025-04-24 17:05" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="in_depart_airport">Departure Point</label>
						<input id="in_depart_airport" type="text" class="form-control" value="Incheon (ICN)" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="in_arrive_airport">Destination</label>
						<input id="in_arrive_airport" type="text" class="form-control" value="Manila (MNL)" disabled>
					</div>

					<div class="grid-item"></div>
				</div>
			</div>


			<h2 class="section-title jw-mgt32">Customer Information</h2>
			<div class="card-panel jw-mgt16">
				<div class="grid-wrap">

					<div class="grid-item">
						<label class="label-name" for="cust_name">Name</label>
						<input id="cust_name" type="text" class="form-control" value="Jose Ramirez" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="cust_email">Email</label>
						<input id="cust_email" type="email" class="form-control" value="ramirez@gmail.com" disabled>
					</div>

					<div class="grid-item">
						<label class="label-name" for="cust_phone">Contact</label>
						<input id="cust_phone" type="text" class="form-control" value="917 123 4567" disabled>
					</div>

				</div>
			</div>


			<h2 class="section-title jw-mgt32">Traveler Information</h2>
			<div class="card-panel jw-mgt16">
				<!-- Horizontal scroll wrapper -->
				<div class="tableA-scroll">
					<table class="jw-tableA booking-detail">
						<colgroup>
							<col style="width:50px;"><!-- No -->
							<col style="width:100px;"><!-- Main Traveler -->
							<col style="width:160px;"><!-- Type -->
							<col style="width:160px;"><!-- Visa Application -->
							<col style="width:120px;"><!-- Title -->
							<col style="width:160px;"><!-- First Name -->
							<col style="width:160px;"><!-- Last Name -->
							<col style="width:120px;"><!-- Gender -->
							<col style="width:100px;"><!-- Age -->
							<col style="width:160px;"><!-- Date of Birth -->
							<col style="width:160px;"><!-- Nationality -->
							<col style="width:180px;"><!-- Passport Number -->
							<col style="width:160px;"><!-- Passport Issue Date -->
							<col style="width:160px;"><!-- Passport Expiry Date -->
							<col style="width:240px;"><!-- Passport Photo -->
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
							</tr>
						</thead>

						<tbody>
							<!-- row 1 -->
							<tr>
								<td class="is-center">1</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" checked>
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 2 -->
							<tr>
								<td class="is-center">2</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1">
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 3 -->
							<tr>
								<td class="is-center">3</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 4 -->
							<tr>
								<td class="is-center">4</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 5 -->
							<tr>
								<td class="is-center">5</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 6 -->
							<tr>
								<td class="is-center">6</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 7 -->
							<tr>
								<td class="is-center">7</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 8 -->
							<tr>
								<td class="is-center">8</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 9 -->
							<tr>
								<td class="is-center">9</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>

							<!-- row 10 -->
							<tr>
								<td class="is-center">10</td>
								<td class="is-center">
									<label class="jw-radio jw-self-center">
										<input type="radio" name="radio1" >
										<i class="icon"></i>
									</label>
								</td>

								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Adult</option>
											<option>Child</option>
											<option>Infant</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Not applied</option>
											<option>Applied</option>
										</select></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>MR</option>
											<option>MRS</option>
											<option>MS</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Ramirez"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Jose"></div>
								</td>
								<td class="show">
									<div class="cell">
										<select class="select" disabled>
											<option selected>Male</option>
											<option>Female</option>
										</select></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" value="40" disabled></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="19710101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="Philippines"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="P1234567"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20200101"></div>
								</td>
								<td class="is-center">
									<div class="cell"><input type="text" class="form-control" disabled value="20300101"></div>
								</td>
								<td class="is-center">
									<div class="cell">
										<div class="field-row jw-center">
											<div class="jw-center jw-gap10"><img src="../image/file.svg" alt="">Passport Photo</div>
											<div class="jw-center jw-gap10">
												<i></i>
												<button type="button" class="jw-button typeF" aria-label="download"><img
														src="../image/buttun-download.svg" alt=""></button>
												
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<div class="jw-pagebox" role="navigation" aria-label="Pagination">
					<div class="contents">
						<button type="button" class="first" aria-label="First Page" aria-disabled="false">
							<img src="../image/first.svg" alt="">
						</button>
						<button type="button" class="prev" aria-label="Previous Page" aria-disabled="false">
							<img src="../image/prev.svg" alt="">
						</button>

						<div class="page" role="list">
							<button type="button" class="p" role="listitem">1</button>
							<button type="button" class="p" role="listitem">2</button>
							<button type="button" class="p show" role="listitem" aria-current="page">3</button>
							<button type="button" class="p" role="listitem">4</button>
							<button type="button" class="p" role="listitem">5</button>
						</div>

						<button type="button" class="next" aria-label="Next Page" aria-disabled="false">
							<img src="../image/next.svg" alt="">
						</button>
						<button type="button" class="last" aria-label="Last Page" aria-disabled="false">
							<img src="../image/last.svg" alt="">
						</button>
					</div>
				</div>



			</div>



		</section>

	</main>

	<!-- Default scripts -->
	<script src="../js/default.js"></script>
	<script src="../js/agent.js"></script>
	<script src="../js/agent-reservation-detail.js"></script>
	<script>
		init({
			headerUrl: '../inc/header.html',
			navUrl: '../inc/nav_agent.html'
		});
	</script>
</body>

</html>
