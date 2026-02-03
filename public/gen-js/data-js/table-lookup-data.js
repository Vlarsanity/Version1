/**
 * =========================================================
 * LOOKUP DATA - Member Types & Branches
 * =========================================================
 * 
 * This file contains reference data for the table.
 * Load this BEFORE customer-data.js
 */

// Member Types (Agent, B2C, B2B)
window.MEMBER_TYPES = [
    { 
        id: 1, 
        name: "Agent", 
        color: "#ef4444",
        description: "Agent members"
    },
    { 
        id: 2, 
        name: "B2C", 
        color: "#8b5cf6",
        description: "Business to Consumer"
    },
    { 
        id: 3, 
        name: "B2B", 
        color: "#10b981",
        description: "Business to Business"
    }
];

// Branch Information
window.BRANCHES = [
    { 
        id: 1, 
        name: "Sulit Traveler", 
        typeId: 1, 
        color: "#6366f1", 
        owner: "Juan Dela Cruz",
        location: "Manila",
        phone: "+63 2 1234 5678"
    },
    { 
        id: 2, 
        name: "Lipad Lakbay", 
        typeId: 2, 
        color: "#10b981", 
        owner: "Juan Dela Cruz",
        location: "Cebu",
        phone: "+63 32 234 5678"
    },
    { 
        id: 3, 
        name: "P91", 
        typeId: 3, 
        color: "#f59e0b", 
        owner: "Juan Dela Cruz",
        location: "Davao",
        phone: "+63 82 234 5678"
    },
    { 
        id: 4, 
        name: "Future Diamond", 
        typeId: 1, 
        color: "#8b5cf6", 
        owner: "Juan Dela Cruz",
        location: "Makati",
        phone: "+63 2 8765 4321"
    },
    { 
        id: 5, 
        name: "E-Winer", 
        typeId: 2, 
        color: "#6366f1", 
        owner: "Juan Dela Cruz",
        location: "Quezon City",
        phone: "+63 2 9876 5432"
    },
    { 
        id: 6, 
        name: "Francia", 
        typeId: 3, 
        color: "#f59e0b", 
        owner: "Juan Dela Cruz",
        location: "Pasig",
        phone: "+63 2 3456 7890"
    },
    { 
        id: 7, 
        name: "Travel Escape", 
        typeId: 1, 
        color: "#10b981", 
        owner: "Juan Dela Cruz",
        location: "Taguig",
        phone: "+63 2 4567 8901"
    },
    { 
        id: 8, 
        name: "APD", 
        typeId: 2, 
        color: "#6366f1", 
        owner: "Juan Dela Cruz",
        location: "Mandaluyong",
        phone: "+63 2 5678 9012"
    }
];

// Helper functions to get lookup data
window.DataHelpers = {
    getMemberType(id) {
        return window.MEMBER_TYPES.find(type => type.id === id);
    },
    
    getBranch(id) {
        return window.BRANCHES.find(branch => branch.id === id);
    },
    
    getBranchName(id) {
        const branch = this.getBranch(id);
        return branch ? branch.name : 'Unknown';
    },
    
    getMemberTypeName(id) {
        const type = this.getMemberType(id);
        return type ? type.name : 'Unknown';
    },
    
    getBranchesByType(typeId) {
        return window.BRANCHES.filter(branch => branch.typeId === typeId);
    }
};

console.log('✅ Lookup data loaded:', 
    window.MEMBER_TYPES.length, 'member types,', 
    window.BRANCHES.length, 'branches'
);