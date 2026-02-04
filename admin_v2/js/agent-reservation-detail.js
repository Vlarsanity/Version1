/**
 * Agent Admin - Reservation Detail Page JavaScript
 * 3-Tier Payment System Version
 */

let currentBookingId = null;
let isEditMode = false;
let currentBookingData = null;
let isTravelerEditAllowed = false;

// File upload variables
let downPaymentProofFile = null;
let advancePaymentProofFile = null;
let balanceProofFile = null;

// Execute after DOM is fully loaded
function initializePage() {
    // Get bookingId from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentBookingId = urlParams.get('id') || urlParams.get('bookingId');

    if (currentBookingId) {
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                loadReservationDetail();
            });
        });
    } else {
        showError('Reservation ID is missing.');
    }

    // Save button event
    const saveButton = document.querySelector('.page-toolbar-actions .jw-button.typeB');
    if (saveButton) {
        saveButton.addEventListener('click', handleSave);
    }

    // Status change event
    const statusSelect = document.querySelector('.page-toolbar-actions select');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            isEditMode = true;
        });
    }

    // Initialize file upload handlers
    initializeFileUploadHandlers();
}

// Execute after all resources are loaded using window.onload
if (document.readyState === 'complete') {
    initializePage();
} else {
    window.addEventListener('load', initializePage);
}

async function loadReservationDetail() {
    try {
        showLoading();

        const response = await fetch(`../backend/api/agent-api.php?action=getReservationDetail&bookingId=${currentBookingId}`);
        const result = await response.json();

        console.log('API Response:', result);

        if (result.success) {
            currentBookingData = result.data;
            renderReservationDetail(result.data);
        } else {
            showError('Failed to load reservation details: ' + result.message);
        }
    } catch (error) {
        console.error('Error loading reservation detail:', error);
        showError('An error occurred while loading reservation details.');
    } finally {
        hideLoading();
    }
}

function renderReservationDetail(data) {
    const booking = data.booking;
    const selectedOptions = data.selectedOptions || {};
    const travelers = data.travelers || [];

    // Product information
    if (booking.packageName) {
        const productNameInput = document.getElementById('product_name');
        if (productNameInput) productNameInput.value = booking.packageName;
    }

    if (booking.departureDate) {
        const tripRangeInput = document.getElementById('trip_range');
        if (tripRangeInput) {
            const returnDate = booking.returnDate || calculateReturnDate(booking.departureDate, booking.duration_days || booking.durationDays || 5);
            tripRangeInput.value = `${booking.departureDate} - ${returnDate}`;
        }
    }

    if (booking.meetingTime || booking.meetTime) {
        const meetTimeInput = document.getElementById('meet_time');
        if (meetTimeInput) {
            const meetTime = booking.meetingTime || booking.meetTime;
            meetTimeInput.value = meetTime.includes(' ') ? meetTime : `${booking.departureDate} ${meetTime}`;
        }
    }

    if (booking.meetingPlace || booking.meetPlace) {
        const meetPlaceInput = document.getElementById('meet_place');
        if (meetPlaceInput) {
            meetPlaceInput.value = booking.meetingPlace || booking.meetPlace || '';
        }
    }

    // Reservation information
    if (booking.bookingId) {
        const resNoInput = document.getElementById('res_no');
        if (resNoInput) resNoInput.value = booking.bookingId;
    }

    if (booking.createdAt) {
        const resDatetimeInput = document.getElementById('res_datetime');
        if (resDatetimeInput) resDatetimeInput.value = formatDateTime(booking.createdAt);
    }

    if (booking.adults !== undefined || booking.children !== undefined) {
        const resPeopleInput = document.getElementById('res_people');
        if (resPeopleInput) {
            const peopleParts = [];
            if (booking.adults > 0) peopleParts.push(`Adult x${booking.adults}`);
            if (booking.children > 0) peopleParts.push(`Child x${booking.children}`);
            if (booking.infants > 0) peopleParts.push(`Infant x${booking.infants}`);
            resPeopleInput.value = peopleParts.join(', ');
        }
    }

    // Room options
    const roomOptInput = document.getElementById('room_opt');
    if (roomOptInput) {
        const roomParts = [];
        if (selectedOptions.selectedRooms) {
            if (Array.isArray(selectedOptions.selectedRooms)) {
                selectedOptions.selectedRooms.forEach(room => {
                    if (room && (room.count > 0 || room.quantity > 0)) {
                        const count = room.count || room.quantity || 1;
                        const name = room.roomType || room.name || room.roomName || 'Room';
                        roomParts.push(`${name} x${count}`);
                    }
                });
            } else {
                Object.values(selectedOptions.selectedRooms).forEach(room => {
                    if (room && (room.count > 0 || room.quantity > 0)) {
                        const count = room.count || room.quantity || 1;
                        const name = room.roomType || room.name || room.roomName || 'Room';
                        roomParts.push(`${name} x${count}`);
                    }
                });
            }
        }
        roomOptInput.value = roomParts.length > 0 ? roomParts.join(', ') : 'None';
    }

    // Additional options
    const options = selectedOptions.selectedOptions || selectedOptions.options || {};

    const baggageSelect = document.getElementById('opt_baggage');
    if (baggageSelect) {
        const baggageValue = options.baggage || options.carryOnBaggage || selectedOptions.baggage;
        if (baggageValue) {
            if (typeof baggageValue === 'string' && baggageValue.match(/^\d+$/)) {
                baggageSelect.value = baggageValue;
            } else if (typeof baggageValue === 'number') {
                baggageSelect.value = baggageValue.toString();
            } else {
                baggageSelect.value = baggageValue;
            }
        }
    }

    const breakfastSelect = document.getElementById('opt_breakfast');
    if (breakfastSelect) {
        const breakfastValue = options.breakfast || options.breakfastRequest || selectedOptions.breakfast;
        if (breakfastValue) {
            if (breakfastValue === 'applied' || breakfastValue === 'apply' || breakfastValue === 'Applied') {
                breakfastSelect.value = 'Applied';
            } else if (breakfastValue === 'not_applied' || breakfastValue === 'not_apply' || breakfastValue === 'Not Applied') {
                breakfastSelect.value = 'Not Applied';
            } else {
                breakfastSelect.value = breakfastValue;
            }
        }
    }

    const wifiSelect = document.getElementById('opt_wifi');
    if (wifiSelect) {
        const wifiValue = options.wifi || options.wifiRental || selectedOptions.wifi;
        if (wifiValue) {
            if (wifiValue === 'applied' || wifiValue === 'apply' || wifiValue === 'Applied') {
                wifiSelect.value = 'Applied';
            } else if (wifiValue === 'not_applied' || wifiValue === 'not_apply' || wifiValue === 'Not Applied') {
                wifiSelect.value = 'Not Applied';
            } else {
                wifiSelect.value = wifiValue;
            }
        }
    }

    if (selectedOptions.seatRequest) {
        const seatReqTextarea = document.querySelector('#seat_req, textarea[name="seatRequest"]');
        if (seatReqTextarea) seatReqTextarea.value = selectedOptions.seatRequest;
    }

    if (selectedOptions.otherRequest) {
        const etcReqTextarea = document.querySelector('#etc_req, textarea[name="otherRequest"]');
        if (etcReqTextarea) etcReqTextarea.value = selectedOptions.otherRequest;
    }

    if (booking.agentMemo || booking.memo) {
        const agentMemoTextarea = document.getElementById('agent_memo');
        if (agentMemoTextarea) agentMemoTextarea.value = booking.agentMemo || booking.memo || '';
    }

    // Customer information
    renderCustomerInfo(booking);

    // Flight information
    renderFlightInfo(booking, selectedOptions);

    // Render payment info (3-tier)
    renderPaymentInfo(booking);

    // Check if traveler editing is allowed
    const editCheck = checkTravelerEditAllowed(booking);
    isTravelerEditAllowed = editCheck.allowed;

    // Render traveler information
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            renderTravelers(travelers, 0, editCheck);
        });
    });
}

// Render customer information
function renderCustomerInfo(booking) {
    const custNameInput = document.getElementById('cust_name');
    if (custNameInput) {
        const firstName = booking.customerFirstName || booking.customerFName || '';
        const lastName = booking.customerLastName || booking.customerLName || '';
        custNameInput.value = `${firstName} ${lastName}`.trim() || booking.customerName || '';
    }

    const custEmailInput = document.getElementById('cust_email');
    if (custEmailInput) {
        custEmailInput.value = booking.contactEmail || booking.customerEmail || booking.accountEmail || '';
    }

    const custPhoneInput = document.getElementById('cust_phone');
    if (custPhoneInput) {
        const phone = booking.contactPhone || booking.customerPhone || booking.contactNo || '';
        const countryCode = booking.countryCode || '';
        custPhoneInput.value = countryCode ? `${countryCode} ${phone}` : phone;
    }
}

// Render flight information
function renderFlightInfo(booking, selectedOptions = {}) {
    if (booking.outboundFlight) {
        const outFlight = booking.outboundFlight;
        const outFlightNoInput = document.getElementById('out_flight_no');
        if (outFlightNoInput && outFlight.flightNumber) {
            outFlightNoInput.value = outFlight.flightNumber;
        }

        const outDepartDtInput = document.getElementById('out_depart_dt');
        if (outDepartDtInput && outFlight.departureDateTime) {
            outDepartDtInput.value = formatDateTime(outFlight.departureDateTime);
        }

        const outArriveDtInput = document.getElementById('out_arrive_dt');
        if (outArriveDtInput && outFlight.arrivalDateTime) {
            outArriveDtInput.value = formatDateTime(outFlight.arrivalDateTime);
        }

        const outDepartAirportInput = document.getElementById('out_depart_airport');
        if (outDepartAirportInput && outFlight.departureAirport) {
            outDepartAirportInput.value = outFlight.departureAirport;
        }

        const outArriveAirportInput = document.getElementById('out_arrive_airport');
        if (outArriveAirportInput && outFlight.arrivalAirport) {
            outArriveAirportInput.value = outFlight.arrivalAirport;
        }
    }

    if (booking.inboundFlight) {
        const inFlight = booking.inboundFlight;
        const inFlightNoInput = document.getElementById('in_flight_no');
        if (inFlightNoInput && inFlight.flightNumber) {
            inFlightNoInput.value = inFlight.flightNumber;
        }

        const inDepartDtInput = document.getElementById('in_depart_dt');
        if (inDepartDtInput && inFlight.departureDateTime) {
            inDepartDtInput.value = formatDateTime(inFlight.departureDateTime);
        }

        const inArriveDtInput = document.getElementById('in_arrive_dt');
        if (inArriveDtInput && inFlight.arrivalDateTime) {
            inArriveDtInput.value = formatDateTime(inFlight.arrivalDateTime);
        }

        const inDepartAirportInput = document.getElementById('in_depart_airport');
        if (inDepartAirportInput && inFlight.departureAirport) {
            inDepartAirportInput.value = inFlight.departureAirport;
        }

        const inArriveAirportInput = document.getElementById('in_arrive_airport');
        if (inArriveAirportInput && inFlight.arrivalAirport) {
            inArriveAirportInput.value = inFlight.arrivalAirport;
        }
    }
}

function renderTravelers(travelers, retryCount = 0, editCheck = null) {
    const tbody = document.querySelector('.booking-detail tbody');
    if (!tbody) {
        if (retryCount < 10) {
            console.log(`tbody not found, retrying (${retryCount + 1}/10)...`);
            setTimeout(() => renderTravelers(travelers, retryCount + 1, editCheck), 100);
        } else {
            console.error('Failed to find tbody after 10 retries');
        }
        return;
    }

    console.log('renderTravelers called with:', travelers);

    // Check if editing is allowed
    const canEdit = editCheck ? editCheck.allowed : false;
    const disabledAttr = canEdit ? '' : 'disabled';

    if (travelers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="15" class="is-center">No traveler information available.</td></tr>';
        renderTravelerEditNotice(editCheck);
        return;
    }

    // Store travelers data for later use
    window.travelersData = travelers;

    tbody.innerHTML = travelers.map((traveler, index) => {
        const formatDate = (dateStr) => {
            if (!dateStr) return '';
            try {
                if (typeof dateStr === 'string' && dateStr.length === 8 && /^\d{8}$/.test(dateStr)) {
                    return dateStr;
                }
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}${month}${day}`;
            } catch (e) {
                return dateStr;
            }
        };

        const calculateAge = (birthDateStr) => {
            if (!birthDateStr) return '';
            try {
                let birthDate;
                if (typeof birthDateStr === 'string' && birthDateStr.length === 8 && /^\d{8}$/.test(birthDateStr)) {
                    const year = parseInt(birthDateStr.substring(0, 4));
                    const month = parseInt(birthDateStr.substring(4, 6)) - 1;
                    const day = parseInt(birthDateStr.substring(6, 8));
                    birthDate = new Date(year, month, day);
                } else {
                    birthDate = new Date(birthDateStr);
                }
                if (isNaN(birthDate.getTime())) return '';
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            } catch (e) {
                return '';
            }
        };

        let age = traveler.age || '';
        if (!age && traveler.dateOfBirth) {
            age = calculateAge(traveler.dateOfBirth);
        }

        const passportImage = traveler.passportImage || '';
        const hasPassportImage = passportImage && passportImage.trim() !== '';
        const travelerId = traveler.id || traveler.bookingTravelerId || '';

        return `
        <tr data-traveler-index="${index}" data-traveler-id="${travelerId}">
            <td class="is-center">${index + 1}</td>
            <td class="is-center">
                <label class="jw-radio jw-self-center">
                <input type="radio" name="lead_traveler" ${traveler.isMainTraveler ? 'checked' : ''} ${disabledAttr}>
                    <i class="icon"></i>
                </label>
            </td>
            <td class="show">
                <div class="cell">
                    <select class="select" ${disabledAttr}>
                        <option value="adult" ${traveler.travelerType === 'adult' ? 'selected' : ''}>Adult</option>
                        <option value="child" ${traveler.travelerType === 'child' ? 'selected' : ''}>Child</option>
                        <option value="infant" ${traveler.travelerType === 'infant' ? 'selected' : ''}>Infant</option>
                    </select>
                </div>
            </td>
            <td class="show">
                <div class="cell">
                    <select class="select" ${disabledAttr}>
                        <option value="0" ${traveler.visaRequired == 0 ? 'selected' : ''}>Not Applied</option>
                        <option value="1" ${traveler.visaRequired == 1 ? 'selected' : ''}>Applied</option>
                    </select>
                </div>
            </td>
            <td class="show">
                <div class="cell">
                    <select class="select" ${disabledAttr}>
                        <option value="MR" ${traveler.title === 'MR' ? 'selected' : ''}>MR</option>
                        <option value="MRS" ${traveler.title === 'MRS' ? 'selected' : ''}>MRS</option>
                        <option value="MS" ${traveler.title === 'MS' ? 'selected' : ''}>MS</option>
                    </select>
                </div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${escapeHtml(traveler.firstName || traveler.fName || '')}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${escapeHtml(traveler.lastName || traveler.lName || '')}" ${disabledAttr}></div>
            </td>
            <td class="show">
                <div class="cell">
                    <select class="select" ${disabledAttr}>
                        <option value="male" ${traveler.gender === 'male' ? 'selected' : ''}>Male</option>
                        <option value="female" ${traveler.gender === 'female' ? 'selected' : ''}>Female</option>
                    </select>
                </div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="number" class="form-control" value="${age}" disabled></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${formatDate(traveler.dateOfBirth || traveler.birthdate || traveler.birthDate)}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${escapeHtml(traveler.nationality || '')}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${escapeHtml(traveler.passportNumber || traveler.passportNo || '')}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${formatDate(traveler.passportIssueDate || traveler.passportIssuedDate)}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell"><input type="text" class="form-control" value="${formatDate(traveler.passportExpiryDate || traveler.passportExp || traveler.passportExpiry)}" ${disabledAttr}></div>
            </td>
            <td class="is-center">
                <div class="cell">
                    ${hasPassportImage ? `
                        <div class="field-row jw-center">
                            <div class="jw-center jw-gap10"><img src="../image/file.svg" alt=""> Passport Photo</div>
                            <div class="jw-center jw-gap10">
                                <i></i>
                                <button type="button" class="jw-button typeF" aria-label="download" onclick="window.open('${escapeHtml(passportImage)}', '_blank')"><img src="../image/buttun-download.svg" alt=""></button>
                            </div>
                        </div>
                    ` : '<span>-</span>'}
                </div>
            </td>
        </tr>
    `;
    }).join('');

    // Initialize select components
    if (typeof jw_select === 'function') {
        setTimeout(() => {
            jw_select();
        }, 100);
    }

    // Render edit notice and save button
    renderTravelerEditNotice(editCheck);
}

/**
 * Render traveler edit notice and save button
 */
function renderTravelerEditNotice(editCheck) {
    // Find or create the notice container
    const travelerSection = document.querySelector('.booking-detail');
    if (!travelerSection) return;

    // Remove existing notice if any
    const existingNotice = document.getElementById('traveler-edit-notice');
    if (existingNotice) {
        existingNotice.remove();
    }

    // Create notice container
    const noticeContainer = document.createElement('div');
    noticeContainer.id = 'traveler-edit-notice';
    noticeContainer.style.cssText = 'margin-top: 15px; padding: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;';

    if (editCheck && editCheck.allowed) {
        // Editing allowed - show save button
        noticeContainer.style.backgroundColor = '#e8f5e9';
        noticeContainer.style.border = '1px solid #4caf50';
        noticeContainer.innerHTML = `
            <span style="color: #2e7d32;">Traveler information can be edited.</span>
            <button type="button" class="jw-button typeB" onclick="saveTravelerInfo()" style="padding: 8px 20px;">
                Save Traveler Info
            </button>
        `;
    } else if (editCheck && !editCheck.allowed) {
        // Editing not allowed - show reason
        noticeContainer.style.backgroundColor = '#fff3e0';
        noticeContainer.style.border = '1px solid #ff9800';
        noticeContainer.innerHTML = `
            <span style="color: #e65100;">${escapeHtml(editCheck.reason)}</span>
        `;
    }

    // Insert after the table
    travelerSection.parentNode.insertBefore(noticeContainer, travelerSection.nextSibling);
}

// ============================================
// 3-Tier Payment System Functions
// ============================================

function renderPaymentInfo(booking) {
    // Total order amount
    const orderAmountInput = document.getElementById('order_amount');
    if (orderAmountInput && booking.totalAmount) {
        orderAmountInput.value = formatPriceNumber(booking.totalAmount);
    }

    // Step 1: Down Payment
    renderDownPaymentSection(booking);

    // Step 2: Advance Payment
    renderAdvancePaymentSection(booking);

    // Step 3: Balance
    renderBalanceSection(booking);
}

function renderDownPaymentSection(booking) {
    // Down payment amount
    // packageType에 따른 1인당 금액: Full 5000, Land 3000
    // 유아는 인원수에서 제외
    const downPaymentAmountInput = document.getElementById('down_payment_amount');
    if (downPaymentAmountInput) {
        const packageType = booking.packageType || 'full';
        const downPaymentPerPerson = packageType === 'land' ? 3000 : 5000;

        // 인원수 계산 (유아 제외)
        const travelers = currentBookingData?.travelers || [];
        const adults = travelers.filter(t => t.travelerType === 'adult' || t.type === 'adult').length;
        const children = travelers.filter(t => t.travelerType === 'child' || t.type === 'child').length;
        const headcount = adults + children;

        const calculatedDownPayment = downPaymentPerPerson * (headcount || 1);
        // 항상 계산된 금액 표시 (인원수 × 1인당 금액)
        downPaymentAmountInput.value = formatPriceNumber(calculatedDownPayment);
    }

    // Down payment due date
    const downPaymentDueInput = document.getElementById('down_payment_due');
    if (downPaymentDueInput && booking.downPaymentDueDate) {
        downPaymentDueInput.value = booking.downPaymentDueDate;
    }

    // Down payment proof file
    renderDownPaymentProofFile(booking);

    // Set upload button state
    updateDownPaymentUploadButton(booking);

    // Show rejection reason if rejected
    renderDownPaymentRejectionReason(booking);
}

// Show down payment rejection reason
function renderDownPaymentRejectionReason(booking) {
    const container = document.getElementById('down_payment_rejection_container');
    const reasonEl = document.getElementById('down_payment_rejection_reason');

    if (!container || !reasonEl) return;

    const rejectionReason = booking.downPaymentRejectionReason;
    const rejectedAt = booking.downPaymentRejectedAt;

    if (rejectionReason && rejectedAt) {
        reasonEl.textContent = rejectionReason;
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function renderAdvancePaymentSection(booking) {
    // Advance payment amount
    // packageType에 따른 1인당 금액: Full 10000, Land 5000
    // 유아는 인원수에서 제외
    const advancePaymentAmountInput = document.getElementById('advance_payment_amount');
    if (advancePaymentAmountInput) {
        const packageType = booking.packageType || 'full';
        const advancePaymentPerPerson = packageType === 'land' ? 5000 : 10000;

        // 인원수 계산 (유아 제외)
        const travelers = currentBookingData?.travelers || [];
        const adults = travelers.filter(t => t.travelerType === 'adult' || t.type === 'adult').length;
        const children = travelers.filter(t => t.travelerType === 'child' || t.type === 'child').length;
        const headcount = adults + children;

        const calculatedAdvancePayment = advancePaymentPerPerson * (headcount || 1);
        // 항상 계산된 금액 표시 (인원수 × 1인당 금액)
        advancePaymentAmountInput.value = formatPriceNumber(calculatedAdvancePayment);
    }

    // Advance payment due date (calculated value from DB)
    const advancePaymentDueInput = document.getElementById('advance_payment_due');
    if (advancePaymentDueInput) {
        advancePaymentDueInput.value = booking.advancePaymentDueDate || '-';
    }

    // Advance payment proof file
    renderAdvancePaymentProofFile(booking);

    // Set upload button state
    updateAdvancePaymentUploadButton(booking);

    // Show rejection reason if rejected
    renderAdvancePaymentRejectionReason(booking);
}

// Show advance payment rejection reason
function renderAdvancePaymentRejectionReason(booking) {
    const container = document.getElementById('advance_payment_rejection_container');
    const reasonEl = document.getElementById('advance_payment_rejection_reason');

    if (!container || !reasonEl) return;

    const rejectionReason = booking.advancePaymentRejectionReason;
    const rejectedAt = booking.advancePaymentRejectedAt;

    if (rejectionReason && rejectedAt) {
        reasonEl.textContent = rejectionReason;
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function renderBalanceSection(booking) {
    // Balance amount
    const balanceAmountInput = document.getElementById('balance_amount');
    if (balanceAmountInput) {
        const amount = booking.balanceAmount || 0;
        balanceAmountInput.value = formatPriceNumber(amount);
    }

    // Balance due date
    const balanceDueInput = document.getElementById('balance_due');
    if (balanceDueInput && booking.balanceDueDate) {
        balanceDueInput.value = booking.balanceDueDate;
    }

    // Balance proof file
    renderBalanceProofFile(booking);

    // Set upload button state
    updateBalanceUploadButton(booking);

    // Show rejection reason if rejected
    renderBalanceRejectionReason(booking);
}

// Show balance rejection reason
function renderBalanceRejectionReason(booking) {
    const container = document.getElementById('balance_rejection_container');
    const reasonEl = document.getElementById('balance_rejection_reason');

    if (!container || !reasonEl) return;

    const rejectionReason = booking.balanceRejectionReason;
    const rejectedAt = booking.balanceRejectedAt;

    if (rejectionReason && rejectedAt) {
        reasonEl.textContent = rejectionReason;
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

// File upload handlers initialization
function initializeFileUploadHandlers() {
    // Down payment file upload
    const downPaymentUploadBtn = document.getElementById('down_payment_file_upload_btn');
    const downPaymentFileInput = document.getElementById('down_payment_file_input');
    if (downPaymentUploadBtn && downPaymentFileInput) {
        downPaymentUploadBtn.addEventListener('click', () => downPaymentFileInput.click());
        downPaymentFileInput.addEventListener('change', handleDownPaymentFileSelect);
    }

    // Advance payment file upload
    const advancePaymentUploadBtn = document.getElementById('advance_payment_file_upload_btn');
    const advancePaymentFileInput = document.getElementById('advance_payment_file_input');
    if (advancePaymentUploadBtn && advancePaymentFileInput) {
        advancePaymentUploadBtn.addEventListener('click', () => advancePaymentFileInput.click());
        advancePaymentFileInput.addEventListener('change', handleAdvancePaymentFileSelect);
    }

    // Balance file upload
    const balanceUploadBtn = document.getElementById('balance_file_upload_btn');
    const balanceFileInput = document.getElementById('balance_file_input');
    if (balanceUploadBtn && balanceFileInput) {
        balanceUploadBtn.addEventListener('click', () => balanceFileInput.click());
        balanceFileInput.addEventListener('change', handleBalanceFileSelect);
    }
}

// Down Payment file handlers
function handleDownPaymentFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    downPaymentProofFile = file;
    uploadDownPaymentProof();
}

async function uploadDownPaymentProof() {
    if (!downPaymentProofFile || !currentBookingId) {
        alert('No file selected.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'uploadDownPayment');
    formData.append('bookingId', currentBookingId);
    formData.append('downPaymentProof', downPaymentProofFile);

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Down payment proof file has been uploaded.');
            downPaymentProofFile = null;
            loadReservationDetail();
        } else {
            alert('Upload failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error uploading down payment proof:', error);
        alert('An error occurred while uploading the file.');
    }
}

// Advance Payment file handlers
function handleAdvancePaymentFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    advancePaymentProofFile = file;
    uploadAdvancePaymentProof();
}

async function uploadAdvancePaymentProof() {
    if (!advancePaymentProofFile || !currentBookingId) {
        alert('No file selected.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'uploadAdvancePayment');
    formData.append('bookingId', currentBookingId);
    formData.append('advancePaymentProof', advancePaymentProofFile);

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Advance payment proof file has been uploaded.');
            advancePaymentProofFile = null;
            loadReservationDetail();
        } else {
            alert('Upload failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error uploading advance payment proof:', error);
        alert('An error occurred while uploading the file.');
    }
}

// Balance file handlers
function handleBalanceFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    balanceProofFile = file;
    uploadBalanceProof();
}

async function uploadBalanceProof() {
    if (!balanceProofFile || !currentBookingId) {
        alert('No file selected.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'uploadBalance');
    formData.append('bookingId', currentBookingId);
    formData.append('balanceProof', balanceProofFile);

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Balance proof file has been uploaded.');
            balanceProofFile = null;
            loadReservationDetail();
        } else {
            alert('Upload failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error uploading balance proof:', error);
        alert('An error occurred while uploading the file.');
    }
}

// Render file display sections
function renderDownPaymentProofFile(booking) {
    const nameEl = document.getElementById('down_payment_proof_name');
    const downloadBtn = document.getElementById('down_payment_proof_download');
    const removeBtn = document.getElementById('down_payment_proof_remove');

    if (!nameEl) return;

    const filePath = booking.downPaymentFile || booking.downpaymentfile || '';

    if (!filePath) {
        nameEl.textContent = 'No file attached.';
        if (downloadBtn) downloadBtn.disabled = true;
        if (removeBtn) removeBtn.disabled = true;
        return;
    }

    const fileName = extractFileName(filePath);
    nameEl.textContent = fileName || 'Down payment proof file';

    if (downloadBtn) {
        downloadBtn.disabled = false;
        downloadBtn.onclick = () => downloadFile(filePath, fileName);
    }

    if (removeBtn) {
        // Delete only allowed when upload is permitted (waiting or checking status)
        const bookingStatus = booking.bookingStatus || '';
        const canModify = bookingStatus === 'waiting_down_payment' || bookingStatus === 'checking_down_payment';
        removeBtn.disabled = !canModify;
        if (canModify) {
            removeBtn.onclick = () => handleRemoveDownPaymentFile();
        }
    }
}

function renderAdvancePaymentProofFile(booking) {
    const nameEl = document.getElementById('advance_payment_proof_name');
    const downloadBtn = document.getElementById('advance_payment_proof_download');
    const removeBtn = document.getElementById('advance_payment_proof_remove');

    if (!nameEl) return;

    const filePath = booking.advancePaymentFile || booking.advancepaymentfile || '';

    if (!filePath) {
        const bookingStatus = booking.bookingStatus || '';
        if (bookingStatus === 'waiting_down_payment' || bookingStatus === 'checking_down_payment') {
            nameEl.textContent = 'Available after down payment approval.';
        } else {
            nameEl.textContent = 'No file attached.';
        }
        if (downloadBtn) downloadBtn.disabled = true;
        if (removeBtn) removeBtn.disabled = true;
        return;
    }

    const fileName = extractFileName(filePath);
    nameEl.textContent = fileName || 'Advance payment proof file';

    if (downloadBtn) {
        downloadBtn.disabled = false;
        downloadBtn.onclick = () => downloadFile(filePath, fileName);
    }

    if (removeBtn) {
        // Delete only allowed when upload is permitted
        const bookingStatus = booking.bookingStatus || '';
        const canModify = bookingStatus === 'waiting_advance_payment' || bookingStatus === 'checking_advance_payment';
        removeBtn.disabled = !canModify;
        if (canModify) {
            removeBtn.onclick = () => handleRemoveAdvancePaymentFile();
        }
    }
}

function renderBalanceProofFile(booking) {
    const nameEl = document.getElementById('balance_proof_name');
    const downloadBtn = document.getElementById('balance_proof_download');
    const removeBtn = document.getElementById('balance_proof_remove');

    if (!nameEl) return;

    const filePath = booking.balanceFile || booking.balancefile || '';

    if (!filePath) {
        const bookingStatus = booking.bookingStatus || '';
        if (bookingStatus === 'waiting_down_payment' || bookingStatus === 'checking_down_payment' ||
            bookingStatus === 'waiting_advance_payment' || bookingStatus === 'checking_advance_payment') {
            nameEl.textContent = 'Available after advance payment approval.';
        } else {
            nameEl.textContent = 'No file attached.';
        }
        if (downloadBtn) downloadBtn.disabled = true;
        if (removeBtn) removeBtn.disabled = true;
        return;
    }

    const fileName = extractFileName(filePath);
    nameEl.textContent = fileName || 'Balance proof file';

    if (downloadBtn) {
        downloadBtn.disabled = false;
        downloadBtn.onclick = () => downloadFile(filePath, fileName);
    }

    if (removeBtn) {
        // Delete only allowed when upload is permitted
        const bookingStatus = booking.bookingStatus || '';
        const canModify = bookingStatus === 'waiting_balance' || bookingStatus === 'checking_balance';
        removeBtn.disabled = !canModify;
        if (canModify) {
            removeBtn.onclick = () => handleRemoveBalanceFile();
        }
    }
}

// Update upload button states based on booking status
function updateDownPaymentUploadButton(booking) {
    const uploadBtn = document.getElementById('down_payment_file_upload_btn');
    if (!uploadBtn) return;

    const bookingStatus = booking.bookingStatus || '';

    // Down payment upload available in waiting_down_payment or checking_down_payment status
    // Re-upload possible even with existing file (replacement)
    if (bookingStatus === 'waiting_down_payment' || bookingStatus === 'checking_down_payment') {
        uploadBtn.disabled = false;
    } else {
        uploadBtn.disabled = true;
    }
}

function updateAdvancePaymentUploadButton(booking) {
    const uploadBtn = document.getElementById('advance_payment_file_upload_btn');
    if (!uploadBtn) return;

    const bookingStatus = booking.bookingStatus || '';

    // Advance payment upload available in waiting_advance_payment or checking_advance_payment status
    if (bookingStatus === 'waiting_advance_payment' || bookingStatus === 'checking_advance_payment') {
        uploadBtn.disabled = false;
    } else {
        uploadBtn.disabled = true;
    }
}

function updateBalanceUploadButton(booking) {
    const uploadBtn = document.getElementById('balance_file_upload_btn');
    if (!uploadBtn) return;

    const bookingStatus = booking.bookingStatus || '';

    // Balance upload available in waiting_balance or checking_balance status
    if (bookingStatus === 'waiting_balance' || bookingStatus === 'checking_balance') {
        uploadBtn.disabled = false;
    } else {
        uploadBtn.disabled = true;
    }
}

// File removal handlers
async function handleRemoveDownPaymentFile() {
    if (!confirm('Are you sure you want to delete the down payment proof file?')) {
        return;
    }

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'removeDownPaymentFile',
                bookingId: currentBookingId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Down payment proof file has been deleted.');
            loadReservationDetail();
        } else {
            alert('File deletion failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error removing down payment file:', error);
        alert('An error occurred while deleting the file.');
    }
}

async function handleRemoveAdvancePaymentFile() {
    if (!confirm('Are you sure you want to delete the advance payment proof file?')) {
        return;
    }

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'removeAdvancePaymentFile',
                bookingId: currentBookingId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Advance payment proof file has been deleted.');
            loadReservationDetail();
        } else {
            alert('File deletion failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error removing advance payment file:', error);
        alert('An error occurred while deleting the file.');
    }
}

async function handleRemoveBalanceFile() {
    if (!confirm('Are you sure you want to delete the balance proof file?')) {
        return;
    }

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'removeBalanceFile',
                bookingId: currentBookingId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Balance proof file has been deleted.');
            loadReservationDetail();
        } else {
            alert('File deletion failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error removing balance file:', error);
        alert('An error occurred while deleting the file.');
    }
}

// ============================================
// Utility Functions
// ============================================

function downloadFile(filePath, fileName) {
    const fileUrl = buildFileUrl(filePath);
    const a = document.createElement('a');
    a.href = fileUrl;
    a.download = fileName || 'payment_proof';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function extractFileName(path) {
    if (!path) return '';
    const normalized = path.replace(/\\/g, '/');
    const segments = normalized.split('/');
    return segments.pop() || '';
}

function buildFileUrl(path) {
    if (!path) return '';
    let cleaned = path.replace(/\\/g, '/');
    cleaned = cleaned.replace(/smart-travel2\//gi, '');
    cleaned = cleaned.replace(/\/uploads\/uploads\//gi, '/uploads/');
    cleaned = cleaned.replace(/\/{2,}/g, '/');
    if (cleaned.startsWith('http://') || cleaned.startsWith('https://')) {
        return cleaned;
    }
    if (!cleaned.startsWith('/')) {
        cleaned = '/' + cleaned.replace(/^\/+/, '');
    }
    return window.location.origin + cleaned;
}

function formatPriceNumber(price) {
    if (!price) return '0';
    return parseInt(price).toLocaleString();
}

async function handleSave() {
    if (!isEditMode) {
        alert('No changes to save.');
        return;
    }

    try {
        const statusSelect = document.querySelector('.page-toolbar-actions select');
        const status = statusSelect ? statusSelect.value : null;

        const updateData = {
            bookingId: currentBookingId
        };

        if (status) {
            updateData.status = status;
        }

        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: status ? 'updateReservationStatus' : 'updateReservation',
                ...updateData
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Changes saved successfully.');
            isEditMode = false;
            loadReservationDetail();
        } else {
            alert('Failed to save: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving:', error);
        alert('An error occurred while saving.');
    }
}

function calculateReturnDate(departureDate, durationDays) {
    const date = new Date(departureDate);
    date.setDate(date.getDate() + durationDays - 1);
    return date.toISOString().split('T')[0];
}

function formatDateTime(datetime) {
    if (!datetime) return '';

    // 시간만 있는 경우 (예: "12:20:00" 또는 "12:20")
    if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(datetime)) {
        const parts = datetime.split(':');
        return `${parts[0].padStart(2, '0')}:${parts[1]}`;
    }

    const date = new Date(datetime);

    // 유효하지 않은 날짜인 경우 원본 반환
    if (isNaN(date.getTime())) {
        return datetime;
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function showLoading() {
    const tbody = document.querySelector('.booking-detail tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="is-center">Loading...</td></tr>';
    }
}

function hideLoading() {
    // Loading handled in renderTravelers
}

function showError(message) {
    alert(message);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Check if traveler editing is allowed based on conditions:
 * 1. If downPaymentFile exists → editing NOT allowed
 * 2. If current date is within 2 months of departure date → editing NOT allowed
 */
function checkTravelerEditAllowed(booking) {
    // Condition 1: If downpayment is uploaded, disable editing
    const downPaymentFile = booking.downPaymentFile || booking.downpaymentfile || '';
    if (downPaymentFile && downPaymentFile.trim() !== '') {
        return {
            allowed: false,
            reason: 'Traveler information cannot be edited after down payment has been submitted.'
        };
    }

    // Condition 2: Check if current date is within 2 months of departure date
    const departureDate = booking.departureDate;
    if (departureDate) {
        const departure = new Date(departureDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Calculate 2 months before departure
        const twoMonthsBefore = new Date(departure);
        twoMonthsBefore.setMonth(twoMonthsBefore.getMonth() - 2);

        if (today >= twoMonthsBefore) {
            const formattedDeadline = formatDateForDisplay(twoMonthsBefore);
            return {
                allowed: false,
                reason: `Traveler information can only be edited until 2 months before departure (Deadline: ${formattedDeadline}).`
            };
        }
    }

    return {
        allowed: true,
        reason: ''
    };
}

function formatDateForDisplay(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Save traveler information
 */
async function saveTravelerInfo() {
    if (!isTravelerEditAllowed) {
        alert('Traveler information editing is not allowed.');
        return;
    }

    const tbody = document.querySelector('.booking-detail tbody');
    if (!tbody) {
        alert('Could not find traveler table.');
        return;
    }

    const rows = tbody.querySelectorAll('tr[data-traveler-id]');
    if (rows.length === 0) {
        alert('No traveler data to save.');
        return;
    }

    const travelers = [];
    let mainTravelerId = null;

    rows.forEach(row => {
        const travelerId = row.getAttribute('data-traveler-id');
        const inputs = row.querySelectorAll('input, select');

        // Find the main traveler radio button
        const mainTravelerRadio = row.querySelector('input[name="lead_traveler"]');
        if (mainTravelerRadio && mainTravelerRadio.checked) {
            mainTravelerId = travelerId;
        }

        // Extract values from form elements
        const selects = row.querySelectorAll('select');
        const textInputs = row.querySelectorAll('input[type="text"], input[type="number"]');

        const travelerData = {
            id: travelerId,
            travelerType: selects[0]?.value || 'adult',
            visaRequired: selects[1]?.value || '0',
            title: selects[2]?.value || 'MR',
            firstName: textInputs[0]?.value || '',
            lastName: textInputs[1]?.value || '',
            gender: selects[3]?.value || 'male',
            dateOfBirth: textInputs[3]?.value || '',
            nationality: textInputs[4]?.value || '',
            passportNumber: textInputs[5]?.value || '',
            passportIssueDate: textInputs[6]?.value || '',
            passportExpiryDate: textInputs[7]?.value || ''
        };

        travelers.push(travelerData);
    });

    try {
        const response = await fetch('../backend/api/agent-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'updateTravelers',
                bookingId: currentBookingId,
                travelers: travelers,
                mainTravelerId: mainTravelerId
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Traveler information saved successfully.');
            isEditMode = false;
            loadReservationDetail();
        } else {
            alert('Failed to save: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving traveler info:', error);
        alert('An error occurred while saving traveler information.');
    }
}
