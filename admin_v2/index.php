<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Login</title>

  <!-- 공통 스타일 -->
  <link rel="shortcut icon" href="./image/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />


  <!-- <link rel="stylesheet" href="./css/a_reset.css">
  <link rel="stylesheet" href="./css/a_variables.css"> -->
  <!-- <link rel="stylesheet" href="./css/a_components.css" />
  <link rel="stylesheet" href="./css/a_contents.css" /> -->

  <link rel="stylesheet" href="./css/login.css?v=<?php echo time(); ?>">


  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

  <!-- Bootstrap 5.3 CSS -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">





</head>

<body>
  <!-- header 들어올 자리 -->
  <!-- <header class="layout-header"></header> -->

  <main class="layout-main">
    <section class="layout-content jw-center">

      <form id="loginForm">

        <div class="login-card">

          <div class="image-wrapper">
            <img src="./image/logo.png" alt="logo" />
          </div>

          <div class="field-wrap">

            <div class="field">
              <label class="label-name" data-lan-eng="ID">ID</label>
              <input
                type="text"
                name="identifier"
                id="identifier"
                placeholder="Enter ID/Email"
                autocomplete="username" />
            </div>

            <div class="field">
              <label class="label-name" data-lan-eng="Password">Password</label>

              <div class="input-box">
                <input
                  type="password"
                  name="password"
                  id="password"
                  placeholder="Enter Password"
                  autocomplete="current-password" />

                <button
                  type="button"
                  class="jw-button show-password-btn"
                  aria-label="Toggle password visibility">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="row-field main-form-bottom">
                <div class="jw-checkbox">
                    <input type="checkbox" name="remember_me" id="remember_me" />
                    <label for="remember_me" class="checkbox-label">
                        <i class="icon"></i>
                        <p class="text" data-lan-eng="Remember ID">아이디 저장</p>
                    </label>
                </div>

                <div class="linkbar">
                    <a href="#" class="link add-account-btn" data-bs-toggle="modal" data-bs-target="#addAccountModal">Add Account</a>
                    <a href="#" class="link find-id-btn" data-lan-eng="Find ID">아이디 찾기</a>
                    <a href="#" class="link reset-password-btn" data-lan-eng="Reset Password">비밀번호 재설정</a>
                </div>
            </div>

            <button
              type="submit"
              class="jw-button typeB login-button jw-mgt8"
              data-lan-eng="Login">
              로그인
            </button>

            <!-- Message box placeholder -->
            <div class="login-message-box"></div>

          </div>

        </div>

      </form>
      
    </section>
  </main>


  <!-- Add Account Modal -->
  <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="addAccountModalLabel">Add Account(s)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">

          <!-- Add Single Account Form -->
          <form id="singleAddFormModal" class="mb-3" autocomplete="off">
            <div class="row g-2">

              <!-- Account Type -->
              <div class="col-12 col-md-3 mb-2">
                <select class="form-select" id="accountTypeModal" required>
                  <option value="">User Type</option>
                  <option value="admin">Admin</option>
                  <option value="agent">Agent</option>
                  <option value="employee">Employee</option>
                  <option value="guide">Guide</option>
                </select>
              </div>

              <!-- Travel Agency (Agent only) -->
              <div class="col-12 col-md-3 mb-2" id="travelAgencyWrapper" style="display: none;">
                <input type="text" class="form-control" id="travelAgencyModal" placeholder="Travel Agency Name">
              </div>

              <!-- First Name -->
              <div class="col-12 col-md-3 mb-2">
                <input type="text" class="form-control" id="firstNameModal" placeholder="First Name" required>
              </div>

              <!-- Middle Name -->
              <div class="col-12 col-md-3 mb-2">
                <input type="text" class="form-control" id="middleNameModal" placeholder="Middle Name">
              </div>

              <!-- Last Name -->
              <div class="col-12 col-md-3 mb-2">
                <input type="text" class="form-control" id="lastNameModal" placeholder="Last Name" required>
              </div>

              <!-- Username -->
              <div class="col-12 col-md-6 mb-2">
                <input type="text" class="form-control" id="usernameModal" placeholder="Username" required>
              </div>

              <!-- Password -->
              <div class="col-12 col-md-6 mb-2">
                <input type="text" class="form-control" id="passwordModal" placeholder="Password" required>
              </div>

              <!-- Email Address -->
              <div class="col-12 col-md-6 mb-2">
                <input type="text" class="form-control" id="emailAddressModal" placeholder="Email Address" required>
              </div>

            </div>

            <!-- Add to Table Button -->
            <div class="mt-2 text-end">
              <button type="submit" class="btn btn-success btn-sm">Add to Table</button>
            </div>
          </form>

          <!-- Preview Table -->
          <table class="table table-bordered table-sm" id="previewTableModal">
            <thead class="table-light">
              <tr>
                <th>User Type</th>
                <th>Travel Agency</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

          <!-- Message Box -->
          <div id="addAccountMessageModal" class="login-message-box" style="display:none;"></div>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" id="submitAllAccountsModal" class="btn btn-primary">Submit All</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>

      </div>
    </div>
  </div>


</body>

<!-- Library Essentials -->

<!-- Bootstrap 5.3 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {

    // ===========================
    // GLOBAL ARRAY TO STORE ACCOUNTS
    // ===========================
    let accountsToAdd = [];

    // ===========================
    // UTILITY FUNCTIONS
    // ===========================
    const resetAddModalForm = () => $("#singleAddFormModal")[0].reset();

    const showAddModalMessage = (message, type = "success", duration = 3000) => {
      const selector = "#addAccountMessageModal";
      $(selector).removeClass("success error").addClass(type).text(message).fadeIn();
      setTimeout(() => $(selector).fadeOut(), duration);
    };

    const updateAddModalDataIndex = () => {
      $("#previewTableModal tbody tr").each(function(i) {
        $(this).attr("data-index", i);
      });
    };

    // ===========================
    // ACCOUNT TYPE CHANGE - SHOW/HIDE TRAVEL AGENCY
    // ===========================
    $(document).on("change", "#accountTypeModal", function() {
      const accountType = $(this).val();
      if (accountType === 'agent') {
        $("#travelAgencyWrapper").show();
      } else {
        $("#travelAgencyWrapper").hide();
        $("#travelAgencyModal").val('');
      }
    });

    // ===========================
    // ADD ACCOUNT TO ARRAY & TABLE
    // ===========================
    $(document).on("submit", "#singleAddFormModal", function(e) {
      e.preventDefault();

      // Read values from Add Account modal
      const accountTypeModal = $("#accountTypeModal").val();
      const travelAgencyModal = $("#travelAgencyModal").val().trim();
      const firstNameModal = $("#firstNameModal").val().trim();
      const middleNameModal = $("#middleNameModal").val().trim();
      const lastNameModal = $("#lastNameModal").val().trim();
      const usernameModal = $("#usernameModal").val().trim();
      const emailModal = $("#emailAddressModal").val().trim();
      const passwordModal = $("#passwordModal").val();

      // Simple validation
      if (!accountTypeModal || !firstNameModal || !lastNameModal || !usernameModal || !emailModal || !passwordModal) {
        showAddModalMessage("Please fill all required fields", "error");
        return;
      }

      // Add to accounts array
      accountsToAdd.push({
        accountType: accountTypeModal,
        travelAgency: travelAgencyModal,
        firstName: firstNameModal,
        middleName: middleNameModal,
        lastName: lastNameModal,
        username: usernameModal,
        email: emailModal,
        password: passwordModal
      });
      const index = accountsToAdd.length - 1;

      // Append row to table
      const rowHtml = `
            <tr data-index="${index}">
                <td contenteditable="true">${accountTypeModal}</td>
                <td contenteditable="true">${travelAgencyModal || '-'}</td>
                <td contenteditable="true">${firstNameModal}</td>
                <td contenteditable="true">${middleNameModal}</td>
                <td contenteditable="true">${lastNameModal}</td>
                <td contenteditable="true">${usernameModal}</td>
                <td contenteditable="true">${emailModal}</td>
                <td><input type="text" class="form-control password-input" value="${passwordModal}"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            </tr>
        `;
      $("#previewTableModal tbody").append(rowHtml);

      resetAddModalForm();
      $("#travelAgencyWrapper").hide();
      console.log("Accounts Array after add:", accountsToAdd);
    });

    // ===========================
    // REMOVE ROW
    // ===========================
    $(document).on("click", "#previewTableModal .remove-row", function() {
      const rowIndex = $(this).closest("tr").index();
      accountsToAdd.splice(rowIndex, 1);
      $(this).closest("tr").remove();
      updateAddModalDataIndex();
      console.log("Accounts Array after removal:", accountsToAdd);
    });

    // ===========================
    // UPDATE ARRAY ON EDIT
    // ===========================
    $(document).on("input", "#previewTableModal td[contenteditable], #previewTableModal .password-input", function() {
      const row = $(this).closest("tr");
      const index = parseInt(row.attr("data-index"));

      accountsToAdd[index] = {
        accountType: row.find("td:eq(0)").text().trim(),
        travelAgency: row.find("td:eq(1)").text().trim(),
        firstName: row.find("td:eq(2)").text().trim(),
        middleName: row.find("td:eq(3)").text().trim(),
        lastName: row.find("td:eq(4)").text().trim(),
        username: row.find("td:eq(5)").text().trim(),
        email: row.find("td:eq(6)").text().trim(),
        password: row.find(".password-input").val()
      };

      console.log("Accounts Array after edit:", accountsToAdd);
    });

    // ===========================
    // SUBMIT ALL ACCOUNTS
    // ===========================
    $(document).on("click", "#submitAllAccountsModal", function() {

      if (!accountsToAdd.length) {
        showAddModalMessage("No accounts to submit", "error");
        return;
      }

      $.ajax({
        url: './general/functions/add-account-bulk.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
          accounts: accountsToAdd
        }),
        success: function(response) {
          showAddModalMessage(response.message, response.success ? "success" : "error");

          if (response.success) {
            accountsToAdd = [];
            $("#previewTableModal tbody").empty();
            setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide(), 2000);
          }
        },
        error: function(xhr, status, error) {
          console.error("AJAX Error:", status, error, xhr.responseText);
          showAddModalMessage("Server error. Check console.", "error");
        }
      });

      console.log("Accounts array at submit:", accountsToAdd);
    });

  });
</script>


<script src="./js/default.js"></script>
<script src="./js/super.js"></script>

<script>
  init({
    headerUrl: "./inc/header-index.html",
  });
</script>

<!-- Login Script -->
<script src="../admin_v2/general/functions/js/login.js"></script>

</html>