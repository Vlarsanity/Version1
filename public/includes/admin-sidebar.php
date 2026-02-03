<!-- Sidebar -->
<aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <img src="../assets/images/logo.png" alt="Logo" class="logo-image">
            </div>
            <span class="logo-text">SMT-ESCAPE</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <p class="nav-section-title">Menu</p>

            <div class="nav-items">

                <!-- ============================== -->
                <!-- Dashboard -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-th-large"></i>
                        <span data-lan-eng="Dashboard">대시보드</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="../../public/admin/dashboard.php" class="sub-nav-item" data-page="Operating Status" data-lan-eng="Operating Status">운영현황</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Member Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-users"></i>
                        <span data-lan-eng="Member Management">회원 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="member-list.php" class="sub-nav-item" data-page="member-list" data-lan-eng="Member List">전체 회원 목록</a>
                        <a href="#" class="sub-nav-item" data-page="b2b-customers" data-lan-eng="B2B Customer List">B2B 고객 목록</a>
                        <a href="#" class="sub-nav-item" data-page="b2c-customers" data-lan-eng="B2C Customer List">B2C 고객 목록</a>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Reservation Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-calendar-check"></i>
                        <span data-lan-eng="Reservation Management">예약 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="b2b-reservations" data-lan-eng="B2B Reservation List">B2B 예약 목록</a>
                        <a href="#" class="sub-nav-item" data-page="b2c-reservations" data-lan-eng="B2C Reservation List">B2C 예약 목록</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Sales Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-chart-line"></i>
                        <span data-lan-eng="Sales Management">판매 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="sales-by-date" data-lan-eng="Sales by Date">날짜별 판매</a>
                        <a href="#" class="sub-nav-item" data-page="sales-by-product" data-lan-eng="Sales by Product">상품별 판매</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Product Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-tags"></i>
                        <span data-lan-eng="Product Management">상품 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="product-list" data-lan-eng="Product List">상품 목록</a>
                        <a href="#" class="sub-nav-item" data-page="product-registration" data-lan-eng="Product Registration">상품 등록</a>
                        <a href="#" class="sub-nav-item" data-page="inventory-management" data-lan-eng="Inventory Management">재고 관리</a>
                        <a href="#" class="sub-nav-item" data-page="template-list" data-lan-eng="Template List">템플릿 목록</a>
                        <a href="#" class="sub-nav-item" data-page="category-management" data-lan-eng="Category Management">카테고리 관리</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Inquiry Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-question-circle"></i>
                        <span data-lan-eng="Inquiry Management">문의 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="member-inquiry-list" data-lan-eng="Member Inquiry List">회원 문의 목록</a>
                        <a href="#" class="sub-nav-item" data-page="agent-inquiry-list" data-lan-eng="Agent Inquiry List">에이전트 문의 목록</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Visa Application Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-file-alt"></i>
                        <span data-lan-eng="Visa Application Management">비자 신청 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="visa-application-list" data-lan-eng="Visa Application List">비자 신청 목록</a>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- Site Settings Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle" type="button">
                        <i class="fas fa-tools"></i>
                        <span data-lan-eng="Site Settings Management">사이트 설정 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <a href="#" class="sub-nav-item" data-page="popup-management" data-lan-eng="Popup Management">팝업 관리</a>
                        <a href="#" class="sub-nav-item" data-page="banner-management" data-lan-eng="Banner Management">배너 관리</a>
                        <a href="#" class="sub-nav-item" data-page="announcements" data-lan-eng="Announcements">공지사항</a>
                        <a href="#" class="sub-nav-item" data-page="terms-of-use" data-lan-eng="Terms of Use">이용약관</a>
                        <a href="#" class="sub-nav-item" data-page="company-information" data-lan-eng="Company Information">회사 정보</a>
                    </div>
                </div>

            </div>
        </div>
    </nav>

</aside>