/**
 * =========================================================
 * FILTER DROPDOWN FUNCTIONALITY
 * =========================================================
 * 
 * Handles the filter dropdown in your table header
 * Works with your existing HTML structure
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================================
    // DROPDOWN TOGGLE FUNCTIONALITY
    // ============================================================
    
    const dropdownBtns = document.querySelectorAll('[data-dropdown]');
    
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const dropdown = this.nextElementSibling;
            const isOpen = dropdown.classList.contains('active');
            
            // Close all dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('active');
            });
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdown.classList.add('active');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('active');
        });
    });
    
    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    
    // ============================================================
    // FILTER FUNCTIONALITY
    // ============================================================
    
    const filterItems = document.querySelectorAll('[data-filter]');
    const filterBadge = document.querySelector('.filter-badge');
    let activeFilters = [];
    
    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            const filterValue = this.dataset.filter;
            
            // Access the table instance
            const table = window.customerTable;
            if (!table) {
                console.error('Table not initialized yet');
                return;
            }
            
            if (filterValue === 'clear') {
                // Clear all filters
                table.clearFilter();
                activeFilters = [];
                updateFilterBadge();
                
                // Remove active state from all filter items
                filterItems.forEach(fi => {
                    if (fi.dataset.filter !== 'clear') {
                        fi.classList.remove('active');
                    }
                });
                
                console.log('✅ Filters cleared');
            } else {
                // Toggle filter
                const isActive = this.classList.contains('active');
                
                if (isActive) {
                    // Remove filter
                    this.classList.remove('active');
                    activeFilters = activeFilters.filter(f => f !== filterValue);
                } else {
                    // Add filter
                    this.classList.add('active');
                    activeFilters.push(filterValue);
                }
                
                // Apply filters to table
                if (activeFilters.length > 0) {
                    table.setFilter('status', 'in', activeFilters);
                } else {
                    table.clearFilter();
                }
                
                updateFilterBadge();
                console.log('✅ Active filters:', activeFilters);
            }
            
            // Close dropdown after selection
            setTimeout(() => {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('active');
                });
            }, 300);
        });
    });
    
    function updateFilterBadge() {
        if (filterBadge) {
            if (activeFilters.length > 0) {
                filterBadge.textContent = activeFilters.length;
                filterBadge.style.display = 'flex';
            } else {
                filterBadge.style.display = 'none';
            }
        }
    }
    
    // Initialize badge state
    updateFilterBadge();
    
    
    // ============================================================
    // EXPORT FUNCTIONALITY (if you want to add export buttons)
    // ============================================================
    
    window.exportTable = {
        toCSV: function() {
            const table = window.customerTable;
            if (table) {
                table.download("csv", "customers.csv");
                console.log('✅ Exported to CSV');
            }
        },
        
        toJSON: function() {
            const table = window.customerTable;
            if (table) {
                table.download("json", "customers.json");
                console.log('✅ Exported to JSON');
            }
        },
        
        toXLSX: function() {
            const table = window.customerTable;
            if (table) {
                table.download("xlsx", "customers.xlsx", {sheetName: "Customers"});
                console.log('✅ Exported to Excel');
            }
        },
        
        toPDF: function() {
            const table = window.customerTable;
            if (table) {
                table.download("pdf", "customers.pdf", {
                    orientation: "portrait",
                    title: "Customer List"
                });
                console.log('✅ Exported to PDF');
            }
        }
    };
    
    console.log('✅ Filter dropdown initialized');
});