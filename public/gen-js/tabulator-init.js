/**
 * =========================================================
 * TABULATOR TABLE MANAGER - FIXED VERSION
 * =========================================================
 * 
 * Fixed Issues:
 * - this.table.getElement() error
 * - Proper initialization order
 * - Better error handling
 * - Fixed timing issues
 */

/* =========================================================
   DEFAULT CONFIGURATION (Fallback)
========================================================= */

const DEFAULT_CONFIG = {
    tableName: "customerTable",
    tableId: "customer",
    
    pagination: {
        defaultSize: 13,
        availableSizes: [13, 20, 30, 50, "all"]
    },
    
    features: {
        selectable: true,
        search: true,
        pagination: true,
        export: true,
        dynamicRowFitting: true,
        hoverCards: true
    },
    
    elements: {
        table: "customerTable",
        searchInput: "tableSearchInput",
        searchBtn: "tableSearchBtn",
        searchClear: "tableSearchClear",
        pageSizeSelector: "pageSizeSelector",
        resetPageSize: "resetPageSize",
        tableInfo: "tableInfo",
        pagination: "tablePagination",
        hoverCard: "branchHoverCard"
    }
};

const DEFAULT_ROW_ACTIONS = {
    enabled: true,
    actionType: "modal",
    
    modal: {
        modalId: "customerModal",
        onOpen: (rowData) => {
            console.log("Opening modal for:", rowData);
        }
    },
    
    redirect: {
        urlPattern: "/customers/{id}/edit",
        openInNewTab: false
    },
    
    offcanvas: {
        offcanvasId: "customerOffcanvas",
        onOpen: (rowData) => {
            console.log("Opening offcanvas for:", rowData);
        }
    },
    
    custom: {
        handler: (rowData, cell, event) => {
            console.log("Custom action triggered:", rowData);
        }
    }
};


/* =========================================================
   CONFIGURATION LOADER
========================================================= */

class ConfigLoader {
    static loadConfig() {
        console.log("🔍 Loading configuration...");
        
        let config = JSON.parse(JSON.stringify(DEFAULT_CONFIG));
        
        if (window.TABLE_CONFIG_OVERRIDE) {
            console.log("✅ Found window.TABLE_CONFIG_OVERRIDE");
            config = this.mergeDeep(config, window.TABLE_CONFIG_OVERRIDE);
        }
        
        if (window.PAGE_CONFIG && window.PAGE_CONFIG.table) {
            console.log("✅ Found window.PAGE_CONFIG");
            config = this.mergeDeep(config, this.mapPageConfig(window.PAGE_CONFIG));
        }
        
        const tableEl = document.getElementById(config.elements.table);
        if (tableEl) {
            const dataConfig = this.loadFromDataAttributes(tableEl);
            if (Object.keys(dataConfig).length > 0) {
                console.log("✅ Found data attributes on table element");
                config = this.mergeDeep(config, dataConfig);
            }
        }
        
        const scriptConfig = this.loadFromScriptTag();
        if (Object.keys(scriptConfig).length > 0) {
            console.log("✅ Found data attributes on script tag");
            config = this.mergeDeep(config, scriptConfig);
        }
        
        const jsonConfig = this.loadFromJSONScript();
        if (jsonConfig) {
            console.log("✅ Found JSON configuration");
            config = this.mergeDeep(config, jsonConfig);
        }
        
        console.log("📋 Final configuration:", config);
        return config;
    }
    
    static loadRowActions() {
        let actions = JSON.parse(JSON.stringify(DEFAULT_ROW_ACTIONS));
        
        if (window.ROW_ACTIONS_OVERRIDE) {
            console.log("✅ Found window.ROW_ACTIONS_OVERRIDE");
            actions = this.mergeDeep(actions, window.ROW_ACTIONS_OVERRIDE);
        }
        
        if (window.PAGE_CONFIG && window.PAGE_CONFIG.actions) {
            console.log("✅ Found PAGE_CONFIG.actions");
            if (window.PAGE_CONFIG.actions.rowClick) {
                actions.actionType = window.PAGE_CONFIG.actions.rowClick;
            }
            actions = this.mergeDeep(actions, window.PAGE_CONFIG.actions);
        }
        
        return actions;
    }
    
    static loadFromDataAttributes(element) {
        const config = {};
        
        if (element.dataset.tableName) config.tableName = element.dataset.tableName;
        if (element.dataset.tableId) config.tableId = element.dataset.tableId;
        if (element.dataset.apiEndpoint) config.apiEndpoint = element.dataset.apiEndpoint;
        if (element.dataset.pageSize) {
            config.pagination = { defaultSize: parseInt(element.dataset.pageSize) };
        }
        if (element.dataset.actionType) {
            window._dataActionType = element.dataset.actionType;
        }
        
        return config;
    }
    
    static loadFromScriptTag() {
        const config = {};
        const scripts = document.getElementsByTagName('script');
        
        for (let script of scripts) {
            if (script.src && script.src.includes('tabulator')) {
                if (script.dataset.tableName) config.tableName = script.dataset.tableName;
                if (script.dataset.tableId) config.tableId = script.dataset.tableId;
                if (script.dataset.apiEndpoint) config.apiEndpoint = script.dataset.apiEndpoint;
                if (script.dataset.pageSize) {
                    config.pagination = { defaultSize: parseInt(script.dataset.pageSize) };
                }
                if (script.dataset.actionType) {
                    window._dataActionType = script.dataset.actionType;
                }
                break;
            }
        }
        
        return config;
    }
    
    static loadFromJSONScript() {
        const jsonScript = document.getElementById('tableConfig');
        if (jsonScript && jsonScript.type === 'application/json') {
            try {
                return JSON.parse(jsonScript.textContent);
            } catch (e) {
                console.error("Error parsing JSON config:", e);
            }
        }
        return null;
    }
    
    static mapPageConfig(pageConfig) {
        const config = {};
        
        if (pageConfig.table) {
            if (pageConfig.table.name) config.tableName = pageConfig.table.name;
            if (pageConfig.table.id) config.tableId = pageConfig.table.id;
            if (pageConfig.table.pageSize) {
                config.pagination = { defaultSize: pageConfig.table.pageSize };
            }
        }
        
        if (pageConfig.api) {
            config.apiEndpoint = pageConfig.api.endpoint;
            config.apiBaseUrl = pageConfig.api.baseUrl;
            config.apiHeaders = pageConfig.api.headers;
        }
        
        if (pageConfig.features) {
            config.features = { ...config.features, ...pageConfig.features };
        }
        
        return config;
    }
    
    static mergeDeep(target, source) {
        const output = Object.assign({}, target);
        
        if (this.isObject(target) && this.isObject(source)) {
            Object.keys(source).forEach(key => {
                if (this.isObject(source[key])) {
                    if (!(key in target)) {
                        Object.assign(output, { [key]: source[key] });
                    } else {
                        output[key] = this.mergeDeep(target[key], source[key]);
                    }
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }
        
        return output;
    }
    
    static isObject(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    }
}


/* =========================================================
   LOOKUP DATA (Can be overridden)
========================================================= */

const MEMBER_TYPES = window.MEMBER_TYPES || [
    { id: 1, name: "Agent", color: "#ef4444" },
    { id: 2, name: "B2C", color: "#8b5cf6" },
    { id: 3, name: "B2B", color: "#10b981" }
];

const BRANCHES = window.BRANCHES || [
    { id: 1, name: "Sulit Traveler", typeId: 1, color: "#6366f1", owner: "Juan Dela Cruz" },
    { id: 2, name: "Lipad Lakbay", typeId: 2, color: "#10b981", owner: "Juan Dela Cruz" },
    { id: 3, name: "P91", typeId: 3, color: "#f59e0b", owner: "Juan Dela Cruz" },
    { id: 4, name: "Future Diamond", typeId: 1, color: "#8b5cf6", owner: "Juan Dela Cruz" },
    { id: 5, name: "E-Winer", typeId: 2, color: "#6366f1", owner: "Juan Dela Cruz" },
    { id: 6, name: "Francia", typeId: 3, color: "#f59e0b", owner: "Juan Dela Cruz" },
    { id: 7, name: "Travel Escape", typeId: 1, color: "#10b981", owner: "Juan Dela Cruz" },
    { id: 8, name: "APD", typeId: 2, color: "#6366f1", owner: "Juan Dela Cruz" }
];


/* =========================================================
   TABLE DATA (Can be overridden or loaded from API)
========================================================= */

const TABLE_DATA = window.TABLE_DATA || [];


/* =========================================================
   CUSTOM FORMATTERS
========================================================= */

function customerFormatter(cell) {
    const data = cell.getValue();
    if (!data) return "";

    const name = data.name || "Unknown";
    const initials = data.initials || "??";

    const avatarHTML = data.avatar
        ? `<img src="${data.avatar}" 
             alt="${name}" 
             class="customer-avatar-img" 
             onerror="this.outerHTML='<div class=\\'customer-avatar\\' style=\\'background-color: ${data.color || '#6b7280'}\\'>${initials}</div>';">`
        : `<div class="customer-avatar" style="background-color: ${data.color || '#6b7280'};">${initials}</div>`;

    return `<div class="customer-info">
        ${avatarHTML}
        <span class="customer-name">${name}</span>
    </div>`;
}

function branchFormatter(cell) {
    const branchId = cell.getValue();
    if (!branchId) return "";

    const branch = BRANCHES.find(b => b.id === branchId);
    if (!branch) return `<span class="error-text">Unknown Branch</span>`;

    const initials = branch.name
        .split(" ")
        .map(w => w[0])
        .join("")
        .toUpperCase()
        .substring(0, 3);

    const memberType = MEMBER_TYPES.find(m => m.id === branch.typeId);

    return `
        <div class="branch-hover-trigger" data-branch-id="${branch.id}">
            <div class="customer-info">
                <div class="customer-avatar" style="background:${branch.color}">
                    ${initials}
                </div>
                <span class="customer-name">${branch.name}</span>
                ${memberType
                    ? `<span class="member-type-badge" style="background:${memberType.color}">
                        ${memberType.name}
                      </span>`
                    : ""
                }
            </div>
        </div>`;
}

function statusFormatter(cell) {
    const status = cell.getValue();
    if (!status) return "";

    const statusKey = status.toLowerCase().replace(/\s+/g, "-");
    return `<span class="status-badge status-${statusKey}">${status}</span>`;
}


/* =========================================================
   TABLE MANAGER CLASS
========================================================= */

class TabulatorTableManager {
    constructor(config, rowActions) {
        this.config = config;
        this.rowActions = rowActions;
        this.table = null;
        this.elements = {};
        this.searchTimeout = null;
        this.resizeTimeout = null;
    }

    init() {

        if (typeof Tabulator === 'undefined') {
            this.showError("Tabulator library not loaded! Please include Tabulator CSS and JS.");
            return;
        }

        this.cacheElements();
        this.initTable();

        if (this.config.features.search) this.initSearch();
        if (this.config.features.pagination) this.initPagination();
        if (this.config.features.hoverCards) this.initHoverCards();
        if (this.config.features.dynamicRowFitting) this.initDynamicRowFitting();

        window[this.config.tableId + 'Table'] = this.table;
        window[this.config.tableId + 'TableManager'] = this;

        console.log(`✅ ${this.config.tableId} table initialized`);
    }

    
    cacheElements() {
        Object.keys(this.config.elements).forEach(key => {
            const elementId = this.config.elements[key];
            this.elements[key] = document.getElementById(elementId);
            
            if (!this.elements[key] && key !== 'hoverCard' && key !== 'searchBtn') {
                console.warn(`Element #${elementId} not found`);
            }
        });
    }

    initTable() {
        const tableConfig = {
            layout: "fitColumns",
            height: "100%",
            selectable: this.config.features.selectable,
            selectableRangeMode: "click",
            pagination: this.config.features.pagination,
            paginationMode: "local",
            paginationSize: this.config.pagination.defaultSize,
            placeholder: "No data available",
            columns: this.getColumns(),
            data: TABLE_DATA,

            tableBuilt: () => this.onTableBuilt(),
            dataLoaded: () => this.onDataLoaded(),
            pageLoaded: () => this.onPageLoaded(),
            rowClick: (e, row) => this.handleRowClick(e, row)
        };

        this.table = new Tabulator(`#${this.config.elements.table}`, tableConfig);
    }

    getColumns() {
        const columns = [];

        if (this.config.features.selectable) {
            columns.push({
                formatter: "rowSelection",
                titleFormatter: "rowSelection",
                hozAlign: "center",
                headerSort: false,
                width: 40,
                minWidth: 40,
                maxWidth: 40,
                resizable: false,
                frozen: true,
                cellClick: (e) => e.stopPropagation()
            });
        }

        columns.push(
            {
                title: "Customer",
                field: "customer",
                formatter: customerFormatter,
                minWidth: 180,
                widthGrow: 2.5,
            },
            {
                title: "Branch",
                field: "branchId",
                formatter: branchFormatter,
                minWidth: 180,
                widthGrow: 2.5,
            },
            {
                title: "Status",
                field: "status",
                formatter: statusFormatter,
                minWidth: 120,
                widthGrow: 1,
            },
            {
                title: "Email",
                field: "email",
                minWidth: 160,
                widthGrow: 2,
            },
            {
                title: "Reg. Date",
                field: "regDate",
                minWidth: 160,
                widthGrow: 1.5,
            }
        );

        return columns;
    }

    handleRowClick(e, row) {
        if (e.target.closest(".tabulator-select-row")) return;
        if (e.target.closest(".branch-hover-trigger")) return;

        if (!this.rowActions.enabled) return;

        const rowData = row.getData();
        const rowId = rowData.id;

        console.log(`Row clicked - ID: ${rowId}`, rowData);

        switch (this.rowActions.actionType) {
            case "modal":
                this.openModal(rowData);
                break;

            case "redirect":
                this.redirectTo(rowId);
                break;

            case "offcanvas":
                this.openOffcanvas(rowData);
                break;

            case "custom":
                this.rowActions.custom.handler(rowData, null, e);
                break;

            default:
                console.warn(`Unknown action type: ${this.rowActions.actionType}`);
        }
    }

    openModal(rowData) {
        if (this.rowActions.modal.onOpen) {
            this.rowActions.modal.onOpen(rowData);
        }
    }

    redirectTo(rowId) {
        const url = this.rowActions.redirect.urlPattern.replace("{id}", rowId);
        
        if (this.rowActions.redirect.openInNewTab) {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }

    openOffcanvas(rowData) {
        if (this.rowActions.offcanvas.onOpen) {
            this.rowActions.offcanvas.onOpen(rowData);
        }
    }

    initSearch() {
        if (!this.elements.searchInput) return;

        const searchInput = this.elements.searchInput;
        const clearBtn = this.elements.searchClear;

        searchInput.addEventListener("input", () => {
            const value = searchInput.value.trim();

            if (clearBtn) {
                clearBtn.classList.toggle("is-visible", value.length > 0);
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(value);
            }, 300);
        });

        if (clearBtn) {
            clearBtn.addEventListener("click", () => {
                searchInput.value = "";
                clearBtn.classList.remove("is-visible");
                this.table.clearFilter();
                searchInput.focus();
            });
        }

        if (this.elements.searchBtn) {
            this.elements.searchBtn.addEventListener("click", () => {
                this.performSearch(searchInput.value.trim());
            });
        }

        searchInput.addEventListener("keyup", (e) => {
            if (e.key === "Enter") {
                this.performSearch(searchInput.value.trim());
            }
        });
    }

    performSearch(searchTerm) {
        if (!searchTerm) {
            this.table.clearFilter();
            return;
        }

        this.table.setFilter((data) => {
            return Object.values(data).some(value => {
                if (typeof value === 'object' && value !== null) {
                    return Object.values(value).some(nestedValue =>
                        String(nestedValue).toLowerCase().includes(searchTerm.toLowerCase())
                    );
                }
                return String(value).toLowerCase().includes(searchTerm.toLowerCase());
            });
        });
    }

    initPagination() {
        setTimeout(() => {
            this.updateTableInfo();
            this.renderPagination();
            this.fitRowsToTable();
        }, 100);

        if (this.elements.pageSizeSelector) {
            this.initPageSizeSelector();
        }
    }

    initPageSizeSelector() {
        const selector = this.elements.pageSizeSelector;
        const resetBtn = this.elements.resetPageSize;

        if (resetBtn) {
            resetBtn.style.display = "none";
        }

        selector.addEventListener("change", () => {
            const size = selector.value === "all" 
                ? this.table.getDataCount() 
                : parseInt(selector.value);

            if (!isNaN(size) && size > 0) {
                this.table.setPageSize(size);
                this.table.setPage(1);
                this.updateTableInfo();
                this.renderPagination();
                setTimeout(() => this.fitRowsToTable(), 100);
            }

            if (resetBtn) {
                resetBtn.style.display = 
                    selector.value != this.config.pagination.defaultSize 
                        ? "inline-block" 
                        : "none";
            }
        });

        if (resetBtn) {
            resetBtn.addEventListener("click", () => {
                selector.value = this.config.pagination.defaultSize;
                selector.dispatchEvent(new Event('change'));
            });
        }
    }

    renderPagination() {
        if (!this.elements.pagination) return;

        const currentPage = this.table.getPage();
        const maxPage = this.table.getPageMax();

        if (maxPage === 0) {
            this.elements.pagination.innerHTML = 
                '<div class="pagination-empty">No pages available</div>';
            return;
        }

        this.elements.pagination.innerHTML = "";

        const prevBtn = this.createPaginationButton("prev", currentPage === 1);
        prevBtn.addEventListener("click", () => {
            this.table.previousPage();
            this.renderPagination();
        });
        this.elements.pagination.appendChild(prevBtn);

        const numbersContainer = document.createElement("div");
        numbersContainer.className = "pagination-numbers";

        numbersContainer.appendChild(this.createPageButton(1, currentPage));

        if (currentPage > 3) {
            numbersContainer.appendChild(this.createEllipsis());
        }

        const startPage = Math.max(2, currentPage - 1);
        const endPage = Math.min(maxPage - 1, currentPage + 1);
        for (let i = startPage; i <= endPage; i++) {
            numbersContainer.appendChild(this.createPageButton(i, currentPage));
        }

        if (currentPage < maxPage - 2) {
            numbersContainer.appendChild(this.createEllipsis());
        }

        if (maxPage > 1) {
            numbersContainer.appendChild(this.createPageButton(maxPage, currentPage));
        }

        this.elements.pagination.appendChild(numbersContainer);

        const nextBtn = this.createPaginationButton("next", currentPage === maxPage);
        nextBtn.addEventListener("click", () => {
            this.table.nextPage();
            this.renderPagination();
        });
        this.elements.pagination.appendChild(nextBtn);
    }

    createPageButton(page, currentPage) {
        const btn = document.createElement("button");
        btn.className = `pagination-number ${currentPage === page ? 'active' : ''}`;
        btn.setAttribute("aria-label", `Go to page ${page}`);
        btn.setAttribute("aria-current", currentPage === page ? 'page' : 'false');
        btn.textContent = page;
        btn.addEventListener("click", () => {
            this.table.setPage(page);
            this.renderPagination();
        });
        return btn;
    }

    createPaginationButton(type, disabled) {
        const btn = document.createElement("button");
        btn.className = `pagination-btn ${type}-btn`;
        btn.disabled = disabled;
        btn.setAttribute("aria-label", type === "prev" ? "Previous page" : "Next page");
        btn.innerHTML = type === "prev" 
            ? '<i class="fas fa-chevron-left"></i>'
            : '<i class="fas fa-chevron-right"></i>';
        return btn;
    }

    createEllipsis() {
        const ellipsis = document.createElement("span");
        ellipsis.className = "pagination-ellipsis";
        ellipsis.textContent = "...";
        return ellipsis;
    }

    updateTableInfo() {
        if (!this.elements.tableInfo) return;

        const pageSize = this.table.getPageSize();
        const page = this.table.getPage();
        const total = this.table.getDataCount();

        if (total === 0) {
            this.elements.tableInfo.textContent = "No results found";
            return;
        }

        const start = ((page - 1) * pageSize) + 1;
        const end = Math.min(page * pageSize, total);

        this.elements.tableInfo.textContent = 
            `Showing ${start}-${end} of ${total} result${total !== 1 ? 's' : ''}`;
    }

    initHoverCards() {
        if (!this.elements.hoverCard) return;

        document.addEventListener("mouseover", (e) => {
            const trigger = e.target.closest(".branch-hover-trigger");
            if (!trigger) return;

            const branchId = Number(trigger.dataset.branchId);
            const branch = BRANCHES.find(b => b.id === branchId);
            if (!branch) return;

            const memberType = MEMBER_TYPES.find(m => m.id === branch.typeId);

            this.elements.hoverCard.innerHTML = `
                <div class="branch-hover-title">${branch.name}</div>
                <div class="branch-hover-row">
                    <span class="label">Owner</span>
                    <span>${branch.owner ?? "—"}</span>
                </div>
                <div class="branch-hover-row">
                    <span class="label">Type</span>
                    <span>${memberType?.name ?? "—"}</span>
                </div>
            `;

            const rect = trigger.getBoundingClientRect();
            this.elements.hoverCard.style.top = `${rect.top + rect.height / 2}px`;
            this.elements.hoverCard.style.left = `${rect.right + 12}px`;
            this.elements.hoverCard.style.opacity = "1";
            this.elements.hoverCard.style.visibility = "visible";
            this.elements.hoverCard.style.transform = "translateY(-50%)";
        });

        document.addEventListener("mouseout", (e) => {
            if (!e.target.closest(".branch-hover-trigger")) return;
            this.elements.hoverCard.style.opacity = "0";
            this.elements.hoverCard.style.visibility = "hidden";
        });
    }

    initDynamicRowFitting() {
        // Get the table container element directly (not from table.getElement())
        const tableContainer = document.getElementById(this.config.elements.table);
        if (!tableContainer) {
            console.warn("Table container not found for dynamic row fitting");
            return;
        }

        const wrapper = tableContainer.closest(".full-table-wrapper");
        if (!wrapper) {
            console.warn("Table wrapper not found for dynamic row fitting");
            return;
        }

        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(() => {
                clearTimeout(this.resizeTimeout);
                this.resizeTimeout = setTimeout(() => {
                    this.fitRowsToTable();
                }, 150);
            });
            resizeObserver.observe(wrapper);
        } else {
            window.addEventListener("resize", () => {
                clearTimeout(this.resizeTimeout);
                this.resizeTimeout = setTimeout(() => {
                    this.fitRowsToTable();
                }, 150);
            });
        }
    }

    /**
     * FIXED: Fit rows to available table height
     * Now uses safe element access methods
     */
    fitRowsToTable() {
        // Check if table exists and is initialized
        if (!this.table) {
            console.warn("Table not initialized yet");
            return;
        }

        // Try to get element using DOM query instead of table.getElement()
        const tableContainer = document.getElementById(this.config.elements.table);
        if (!tableContainer) {
            console.warn("Table container element not found");
            return;
        }

        // Find the actual tabulator element (it's created by Tabulator inside our container)
        const tableEl = tableContainer.querySelector('.tabulator');
        if (!tableEl) {
            console.warn("Tabulator element not found yet");
            return;
        }

        const wrapper = tableEl.closest(".full-table-wrapper");
        const header = tableEl.querySelector(".tabulator-header");
        const footer = wrapper?.querySelector(".table-footer");

        if (!wrapper || !header) {
            console.warn("Required elements not found for row fitting");
            return;
        }

        try {
            const wrapperHeight = wrapper.clientHeight;
            const headerHeight = header.offsetHeight;
            const footerHeight = footer ? footer.offsetHeight : 0;
            
            // Add table header actions height if it exists
            const headerActions = wrapper.querySelector(".table-header-actions");
            const headerActionsHeight = headerActions ? headerActions.offsetHeight : 0;
            
            const bodyHeight = wrapperHeight - headerHeight - footerHeight - headerActionsHeight;

            const rowsPerPage = this.table.getPageSize();
            const MIN_ROW_HEIGHT = 35;
            const calculatedRowHeight = Math.floor(bodyHeight / rowsPerPage);
            const rowHeight = Math.max(calculatedRowHeight, MIN_ROW_HEIGHT);

            this.table.setRowHeight(rowHeight);
            this.table.redraw(true);
        } catch (error) {
            console.error("Error in fitRowsToTable:", error);
        }
    }

    onTableBuilt() {
        console.log(`${this.config.tableId} table built`);
    }

    onDataLoaded() {
        console.log(`Loaded ${this.table.getDataCount()} records`);
    }

    onPageLoaded() {
        this.updateTableInfo();
        this.renderPagination();
    }

    showError(message) {
        const tableContainer = document.getElementById(this.config.elements.table);
        if (tableContainer) {
            tableContainer.innerHTML = `
                <div style="padding: 40px; text-align: center; color: #ef4444;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <p style="font-size: 18px; font-weight: 600;">Error</p>
                    <p style="color: #6b7280; margin-top: 8px;">${message}</p>
                </div>`;
        }
        console.error(message);
    }

    getSelectedRows() {
        return this.table ? this.table.getSelectedData() : [];
    }

    exportToCSV(filename = `${this.config.tableId}.csv`) {
        if (this.table) this.table.download("csv", filename);
    }

    exportToJSON(filename = `${this.config.tableId}.json`) {
        if (this.table) this.table.download("json", filename);
    }

    exportToExcel(filename = `${this.config.tableId}.xlsx`) {
        if (this.table) this.table.download("xlsx", filename, { sheetName: this.config.tableId });
    }

    refreshTable() {
        if (this.table) this.table.setData(TABLE_DATA);
    }

    clearFilters() {
        if (!this.table) return;
        
        this.table.clearFilter();
        if (this.elements.searchInput) {
            this.elements.searchInput.value = "";
            if (this.elements.searchClear) {
                this.elements.searchClear.classList.remove("is-visible");
            }
        }
    }
}




/* =========================================================
   INITIALIZATION WITH CONFIGURATION LOADING
========================================================= */

document.addEventListener("DOMContentLoaded", function() {
    const CONFIG = ConfigLoader.loadConfig();
    const ROW_ACTIONS = ConfigLoader.loadRowActions();

    if (window.TABLES_CONFIG) {
        console.log("🔄 Initializing multiple tables");

        Object.keys(window.TABLES_CONFIG).forEach(key => {
            const tableConfig = ConfigLoader.mergeDeep(
                JSON.parse(JSON.stringify(DEFAULT_CONFIG)),
                window.TABLES_CONFIG[key]
            );

            const manager = new TabulatorTableManager(tableConfig, ROW_ACTIONS);
            manager.init();

            window[`${key}TableManager`] = manager;
        });

    } else {
        console.log("🚀 Initializing table with loaded configuration");

        const tableManager = new TabulatorTableManager(CONFIG, ROW_ACTIONS);
        tableManager.init();

        window.tableUtils = {
            getSelectedRows: () => tableManager.getSelectedRows(),
            exportToCSV: (filename) => tableManager.exportToCSV(filename),
            exportToJSON: (filename) => tableManager.exportToJSON(filename),
            exportToExcel: (filename) => tableManager.exportToExcel(filename),
            refreshTable: () => tableManager.refreshTable(),
            clearFilters: () => tableManager.clearFilters()
        };
    }
});


/* =========================================================
   API DATA LOADER (Optional)
========================================================= */

async function loadDataFromAPI(endpoint, headers = {}) {
    try {
        console.log(`📡 Loading data from: ${endpoint}`);
        const response = await fetch(endpoint, { headers });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log(`✅ Loaded ${data.length} records from API`);
        return data;
    } catch (error) {
        console.error("❌ Error loading data from API:", error);
        return [];
    }
}

window.loadDataFromAPI = loadDataFromAPI;