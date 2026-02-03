<!-- Sidebar -->
<aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <img src="../Assets/Logos/logo-tab.png" alt="Logo" class="logo-image">
            </div>

            <span class="logo-text">SMART TRAVEL</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <p class="nav-section-title">Menu</p>

            <div class="nav-items">

                <!-- ============================== -->
                <!-- Dashboard -->
                <button onclick="navigate('dashboard')" class="nav-item" data-page="dashboard">
                    <i class="fas fa-th-large"></i>
                    <span data-lan-eng="Dashboard">대시보드</span>
                </button>


                <!-- ============================== -->
                <!-- Member Management Dropdown -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-users"></i>
                        <span data-lan-eng="Member Management">회원 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="sub-nav">
                        <button onclick="navigate('products')" class="sub-nav-item" data-lan-eng="Member List">
                            전체 회원 목록
                        </button>
                        <button onclick="navigate('orders')" class="sub-nav-item" data-lan-eng="B2B Customer List">
                            B2B 고객 목록
                        </button>
                        <button onclick="navigate('customers')" class="sub-nav-item" data-lan-eng="B2C Customer List">
                            B2C 고객 목록
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Reservation Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-calendar-check"></i>
                        <span data-lan-eng="Reservation Management">예약 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('products')" class="sub-nav-item" data-lan-eng="B2B Reservation List">
                            B2B 예약 목록
                        </button>
                        <button onclick="navigate('orders')" class="sub-nav-item" data-lan-eng="B2C Reservation List">
                            B2C 예약 목록
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Sales Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-chart-line"></i>
                        <span data-lan-eng="Sales Management">판매 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Sales by Date">
                            날짜별 판매
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Sales by Product">
                            상품별 판매
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Product Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-tags"></i>
                        <span data-lan-eng="Product Management">상품 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Product List">
                            상품 목록
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Product Registration">
                            상품 등록
                        </button>
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Inventory Management">
                            재고 관리
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Template List">
                            템플릿 목록
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Category Management">
                            카테고리 관리
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Inquiry Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-question-circle"></i>
                        <span data-lan-eng="Inquiry Management">문의 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Member Inquiry List">
                            회원 문의 목록
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Agent Inquiry List">
                            에이전트 문의 목록
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Visa Application Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-file-alt"></i>
                        <span data-lan-eng="Visa Application Management">비자 신청 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Visa Application List">
                            비자 신청 목록
                        </button>
                    </div>
                </div>


                <!-- ============================== -->
                <!-- Site Settings Management -->
                <div class="nav-dropdown">
                    <button class="nav-item nav-dropdown-toggle">
                        <i class="fas fa-tools"></i>
                        <span data-lan-eng="Site Settings Management">사이트 설정 관리</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>

                    <div class="sub-nav">
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Popup Management">
                            팝업 관리
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Banner Management">
                            배너 관리
                        </button>
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Announcements">
                            공지사항
                        </button>
                        <button onclick="navigate('system-settings')" class="sub-nav-item" data-lan-eng="Terms of Use">
                            이용약관
                        </button>
                        <button onclick="navigate('profile-settings')" class="sub-nav-item" data-lan-eng="Company Information">
                            회사 정보
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </nav>
</aside>