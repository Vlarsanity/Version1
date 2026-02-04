/**
 * Agent Admin - Overview Page JavaScript
 */

// 다국어 텍스트 정의
const overviewTexts = {
  eng: {
    noItineraries: "No travel itineraries for today.",
    case: "",
  },
  kor: {
    noItineraries: "오늘 여행 일정이 없습니다.",
    case: "건",
  },
};

// 현재 언어 가져오기
function getCurrentLang() {
  const lang = getCookie("lang") || "eng";
  return lang === "eng" ? "eng" : "kor";
}

// 다국어 텍스트 가져오기
function getText(key) {
  const lang = getCurrentLang();
  return overviewTexts[lang]?.[key] || overviewTexts["eng"][key] || key;
}

document.addEventListener("DOMContentLoaded", function () {
  // Check if profile is complete before loading page content
  checkProfileCompletion();
});

// Check if agent profile is complete
async function checkProfileCompletion() {
  try {
    const response = await fetch("../backend/api/check-profile.php");
    const result = await response.json();

    if (result.success && result.needsProfile) {
      // Redirect to complete profile page
      window.location.href = "complete-profile.html";
      return;
    }

    // Profile is complete, load page content
    updateCurrentDate();
    loadOverviewData();
    loadTodayItineraries();
  } catch (error) {
    console.error("Error checking profile:", error);
    // Continue loading page even if check fails
    updateCurrentDate();
    loadOverviewData();
    loadTodayItineraries();
  }
}

function updateCurrentDate() {
  const dateElement = document.getElementById("current-date");
  if (dateElement) {
    const now = new Date();
    const months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    const dateString = `${
      months[now.getMonth()]
    } ${now.getDate()}, ${now.getFullYear()}`;
    dateElement.textContent = dateString;
    dateElement.setAttribute("datetime", now.toISOString().split("T")[0]);
  }
}

async function loadOverviewData() {
  try {
    const response = await fetch(
      "../backend/api/agent-api.php?action=getOverview"
    );
    const result = await response.json();

    if (result.success) {
      const data = result.data;

      // Payment Status 업데이트
      updatePaymentStatus(data.paymentStatus);
    } else {
      console.error("Failed to load overview:", result.message);
    }
  } catch (error) {
    console.error("Error loading overview:", error);
  }
}

async function loadTodayItineraries() {
  try {
    const response = await fetch(
      "../backend/api/agent-api.php?action=getTodayItineraries"
    );
    const result = await response.json();

    if (result.success) {
      renderTodayItineraries(result.data);
    } else {
      console.error("Failed to load today itineraries:", result.message);
      const tbody = document.querySelector(".jw-tableA.typeB tbody");
      if (tbody) {
        const noItinerariesText = getText("noItineraries");
        tbody.innerHTML = `<tr><td colspan="6" class="is-center">${escapeHtml(
          noItinerariesText
        )}</td></tr>`;
      }
    }
  } catch (error) {
    console.error("Error loading today itineraries:", error);
    const tbody = document.querySelector(".jw-tableA.typeB tbody");
    if (tbody) {
      const noItinerariesText = getText("noItineraries");
      tbody.innerHTML = `<tr><td colspan="6" class="is-center">${escapeHtml(
        noItinerariesText
      )}</td></tr>`;
    }
  }
}

function updatePaymentStatus(status) {
  // Down Payment Awaiting
  const downPaymentCount = document.getElementById("down_payment_wait_count");
  if (downPaymentCount) {
    downPaymentCount.textContent = status.waitingDownPayment || 0;
  }

  // Advance Payment Awaiting
  const advancePaymentCount = document.getElementById(
    "advance_payment_wait_count"
  );
  if (advancePaymentCount) {
    advancePaymentCount.textContent = status.waitingAdvancePayment || 0;
  }

  // Balance Awaiting
  const balanceCount = document.getElementById("balance_wait_count");
  if (balanceCount) {
    balanceCount.textContent = status.waitingBalance || 0;
  }
}

function renderTodayItineraries(itineraries) {
  const tbody = document.querySelector(".jw-tableA.typeB tbody");
  if (!tbody) return;

  const countElement = document.querySelector(".card-subtitle strong");
  if (countElement) {
    const caseText = getText("case");
    const lang = getCurrentLang();
    if (lang === "eng") {
      countElement.textContent = itineraries.length;
    } else {
      countElement.innerHTML =
        itineraries.length + (caseText ? "<span>" + caseText + "</span>" : "");
    }
  }

  if (itineraries.length === 0) {
    const noItinerariesText = getText("noItineraries");
    tbody.innerHTML = `<tr><td colspan="6" class="is-center">${escapeHtml(
      noItinerariesText
    )}</td></tr>`;
    return;
  }

  tbody.innerHTML = itineraries
    .map(
      (item, index) => `
        <tr onclick="goToReservationDetail('${escapeHtml(
          item.bookingId
        )}')" style="cursor: pointer;">
            <td class="is-center">${index + 1}</td>
            <td>${escapeHtml(item.packageName)}</td>
            <td class="is-center">${item.travelPeriod}</td>
            <td class="is-center">${item.customerType}</td>
            <td class="is-center">${item.numPeople}</td>
            <td class="is-center">${escapeHtml(item.guideName)}</td>
        </tr>
    `
    )
    .join("");
}

function goToReservationDetail(bookingId) {
  window.location.href = `reservation-detail.php?id=${bookingId}`;
}

function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
