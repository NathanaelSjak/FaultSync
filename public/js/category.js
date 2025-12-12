/**
 * Category Management JavaScript
 * Handles category CRUD operations, modals, and interactions
 */

document.addEventListener("DOMContentLoaded", () => {
    console.log("Category Management JS Loaded");

    // Initialize all components
    initDeleteConfirmation();
    initCategoryModals();
    initSearchFunctionality();
    initTypeFilters();
    initStatusToggles();
    initBulkActions();
    initFormValidation();
    initDataTable();
    initTooltips();
    initToastNotifications();
});

// ============================================
// 1. DELETE CONFIRMATION
// ============================================
function initDeleteConfirmation() {
    const deleteForms = document.querySelectorAll("form.delete-confirm");
    const deleteButtons = document.querySelectorAll(".btn-delete");

    // For form submissions
    deleteForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            if (!confirm("Apakah Anda yakin ingin menghapus kategori ini?")) {
                e.preventDefault();
                return false;
            }
        });
    });

    // For button clicks (API requests)
    deleteButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name || 'kategori ini';
            
            if (confirm(`Apakah Anda yakin ingin menghapus ${categoryName}?`)) {
                deleteCategory(categoryId);
            }
        });
    });
}

// ============================================
// 2. MODAL MANAGEMENT
// ============================================
function initCategoryModals() {
    const modal = document.getElementById('categoryModal');
    const createBtn = document.getElementById('createCategoryBtn');
    const editBtns = document.querySelectorAll('.edit-category-btn');
    const closeBtns = document.querySelectorAll('.modal-close, .modal-cancel');
    
    // Open create modal
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            openModal('create');
        });
    }
    
    // Open edit modal
    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const categoryId = btn.dataset.id;
            const categoryName = btn.dataset.name;
            const categoryType = btn.dataset.type;
            const categoryDescription = btn.dataset.description || '';
            
            openModal('edit', {
                id: categoryId,
                name: categoryName,
                type: categoryType,
                description: categoryDescription
            });
        });
    });
    
    // Close modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    // Close modal on overlay click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function openModal(action = 'create', data = {}) {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('modalSubmit');
    
    if (!modal) return;
    
    // Reset form
    if (form) form.reset();
    
    if (action === 'create') {
        title.textContent = 'Tambah Kategori Baru';
        submitBtn.textContent = 'Simpan';
        form.action = '/categories';
        form.method = 'POST';
        
        // Set default values
        if (form.elements.type) {
            form.elements.type.value = 'expense';
        }
    } else if (action === 'edit') {
        title.textContent = 'Edit Kategori';
        submitBtn.textContent = 'Update';
        form.action = `/categories/${data.id}`;
        form.method = 'PUT';
        
        // Set form values
        if (form.elements.id) form.elements.id.value = data.id;
        if (form.elements.name) form.elements.name.value = data.name;
        if (form.elements.type) form.elements.type.value = data.type;
        if (form.elements.description) form.elements.description.value = data.description;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Focus on first input
    const firstInput = form.querySelector('input, select, textarea');
    if (firstInput) firstInput.focus();
}

function closeModal() {
    const modal = document.getElementById('categoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// ============================================
// 3. SEARCH FUNCTIONALITY
// ============================================
function initSearchFunctionality() {
    const searchInput = document.getElementById('categorySearch');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearSearch');
    
    if (!searchInput) return;
    
    // Search on input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });
    
    // Search on button click
    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            performSearch(searchInput.value);
        });
    }
    
    // Clear search
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            performSearch('');
        });
    }
    
    // Search on Enter key
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            performSearch(searchInput.value);
        }
    });
}

function performSearch(query) {
    console.log('Searching for:', query);
    
    // Show loading state
    const tableBody = document.querySelector('tbody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8">
                    <div class="spinner mx-auto"></div>
                    <p class="text-gray-500 mt-2">Mencari kategori...</p>
                </td>
            </tr>
        `;
    }
    
    // Send search request
    fetch(`/categories/search?search=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateTable(data.categories);
    })
    .catch(error => {
        console.error('Search error:', error);
        showToast('Gagal melakukan pencarian', 'error');
    });
}

// ============================================
// 4. TYPE FILTERS
// ============================================
function initTypeFilters() {
    const filterButtons = document.querySelectorAll('.type-filter');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter table rows
            filterCategoriesByType(type);
        });
    });
}

function filterCategoriesByType(type) {
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const rowType = row.dataset.type;
        
        if (type === 'all' || rowType === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ============================================
// 5. STATUS TOGGLES
// ============================================
function initStatusToggles() {
    const statusToggles = document.querySelectorAll('.status-toggle');
    
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const categoryId = this.dataset.id;
            const newStatus = this.checked;
            
            updateCategoryStatus(categoryId, newStatus);
        });
    });
}

function updateCategoryStatus(categoryId, status) {
    fetch(`/categories/${categoryId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status berhasil diperbarui', 'success');
        } else {
            showToast('Gagal memperbarui status', 'error');
            // Revert toggle
            const toggle = document.querySelector(`.status-toggle[data-id="${categoryId}"]`);
            if (toggle) toggle.checked = !status;
        }
    })
    .catch(error => {
        console.error('Update status error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

// ============================================
// 6. BULK ACTIONS
// ============================================
function initBulkActions() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    
    if (!selectAll || !checkboxes.length) return;
    
    // Select all checkbox
    selectAll.addEventListener('change', function() {
        const isChecked = this.checked;
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        toggleBulkActions();
    });
    
    // Individual checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });
    
    // Delete selected
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', deleteSelectedCategories);
    }
}

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
    } else {
        bulkActions.classList.add('hidden');
    }
}

function deleteSelectedCategories() {
    const selectedIds = Array.from(document.querySelectorAll('.category-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedIds.length === 0) return;
    
    if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} kategori?`)) {
        return;
    }
    
    fetch('/categories/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${selectedIds.length} kategori berhasil dihapus`, 'success');
            location.reload();
        } else {
            showToast('Gagal menghapus kategori', 'error');
        }
    })
    .catch(error => {
        console.error('Bulk delete error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

// ============================================
// 7. FORM VALIDATION
// ============================================
function initFormValidation() {
    const forms = document.querySelectorAll('.category-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateCategoryForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateCategoryForm(form) {
    const name = form.elements.name?.value.trim();
    const type = form.elements.type?.value;
    
    let isValid = true;
    
    // Clear previous errors
    clearFormErrors(form);
    
    // Validate name
    if (!name) {
        showFieldError(form.elements.name, 'Nama kategori wajib diisi');
        isValid = false;
    } else if (name.length > 50) {
        showFieldError(form.elements.name, 'Nama kategori maksimal 50 karakter');
        isValid = false;
    }
    
    // Validate type
    if (!type) {
        showFieldError(form.elements.type, 'Tipe kategori wajib dipilih');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    if (!field) return;
    
    field.classList.add('border-red-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFormErrors(form) {
    const errorMessages = form.querySelectorAll('.text-red-500');
    errorMessages.forEach(el => el.remove());
    
    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => field.classList.remove('border-red-500'));
}

// ============================================
// 8. DATA TABLE FUNCTIONS
// ============================================
function initDataTable() {
    const table = document.getElementById('categoriesTable');
    if (!table) return;
    
    // Add data attributes to rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const type = row.cells[1]?.textContent.trim().toLowerCase();
        const status = row.cells[2]?.textContent.trim().toLowerCase();
        
        if (type) row.dataset.type = type;
        if (status) row.dataset.status = status;
    });
}

function updateTable(categories) {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    
    if (categories.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Tidak ada kategori ditemukan</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    categories.forEach(category => {
        html += `
            <tr data-type="${category.type}" data-status="${category.status ? 'active' : 'inactive'}">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" 
                             style="background-color: ${category.color || '#6b7280'}">
                            <i class="${category.icon || 'fas fa-folder'} text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${category.name}</div>
                            ${category.description ? `<div class="text-sm text-gray-500">${category.description}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${getTypeBadgeClass(category.type)}">
                        ${category.type === 'income' ? 'Pendapatan' : 'Pengeluaran'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${getStatusBadgeClass(category.status)}">
                        ${category.status ? 'Aktif' : 'Nonaktif'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${formatDate(category.created_at)}
                </td>
                <td class="px-6 py-4">
                    <div class="flex space-x-2">
                        <button onclick="editCategory(${category.id})" 
                                class="text-blue-600 hover:text-blue-900 p-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(${category.id})" 
                                class="text-red-600 hover:text-red-900 p-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    
    // Reinitialize event listeners
    initDeleteConfirmation();
}

// ============================================
// 9. HELPER FUNCTIONS
// ============================================
function getTypeBadgeClass(type) {
    return type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
}

function getStatusBadgeClass(status) {
    return status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}

// ============================================
// 10. TOOLTIPS
// ============================================
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltipText = this.dataset.tooltip;
    
    const tooltip = document.createElement('div');
    tooltip.className = 'fixed bg-gray-800 text-white text-sm rounded px-2 py-1 z-50';
    tooltip.textContent = tooltipText;
    tooltip.id = 'tooltip';
    
    document.body.appendChild(tooltip);
    
    const rect = this.getBoundingClientRect();
    tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;
    tooltip.style.left = `${rect.left + (rect.width - tooltip.offsetWidth) / 2}px`;
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) tooltip.remove();
}

// ============================================
// 11. TOAST NOTIFICATIONS
// ============================================
function initToastNotifications() {
    // Check for flash messages
    const flashSuccess = document.querySelector('.flash-success');
    const flashError = document.querySelector('.flash-error');
    
    if (flashSuccess) {
        showToast(flashSuccess.textContent, 'success');
        flashSuccess.remove();
    }
    
    if (flashError) {
        showToast(flashError.textContent, 'error');
        flashError.remove();
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${getToastClass(type)}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="${getToastIcon(type)} mr-3"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function getToastClass(type) {
    switch(type) {
        case 'success': return 'bg-green-500 text-white';
        case 'error': return 'bg-red-500 text-white';
        case 'warning': return 'bg-yellow-500 text-white';
        default: return 'bg-blue-500 text-white';
    }
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return 'fas fa-check-circle';
        case 'error': return 'fas fa-exclamation-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        default: return 'fas fa-info-circle';
    }
}

// ============================================
// 12. API FUNCTIONS
// ============================================
function deleteCategory(categoryId) {
    if (!confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        return;
    }
    
    fetch(`/categories/${categoryId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Kategori berhasil dihapus', 'success');
            // Remove row from table
            const row = document.querySelector(`tr[data-id="${categoryId}"]`);
            if (row) row.remove();
            
            // Reload if on single page
            if (window.location.pathname === `/categories/${categoryId}`) {
                window.location.href = '/categories';
            }
        } else {
            showToast(data.message || 'Gagal menghapus kategori', 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

function editCategory(categoryId) {
    // Fetch category data and open edit modal
    fetch(`/categories/${categoryId}`)
    .then(response => response.json())
    .then(category => {
        openModal('edit', category);
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showToast('Gagal memuat data kategori', 'error');
    });
}

// ============================================
// EXPORT FUNCTIONS FOR GLOBAL USE
// ============================================
window.openModal = openModal;
window.closeModal = closeModal;
window.deleteCategory = deleteCategory;
window.editCategory = editCategory;
window.showToast = showToast;