// ==================== medical-records.js ====================

// Live search/filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#recordsTable tbody tr');

    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const id = row.cells[0].textContent.toLowerCase();
            const pet = row.cells[1].textContent.toLowerCase();
            const date = row.cells[2].textContent.toLowerCase();

            if (id.includes(filter) || pet.includes(filter) || date.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Optional: confirm deletion
    const deleteButtons = document.querySelectorAll('form button.btn.red');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this record?')) {
                e.preventDefault();
            }
        });
    });
});
