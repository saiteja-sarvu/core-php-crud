document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('deleteEmployeeId').value = button.getAttribute('data-id');
            document.getElementById('deleteEmployeeName').textContent = button.getAttribute('data-name');
        });
    }

    const search = document.getElementById('employeeSearch');
    const table = document.getElementById('employeesTable');
    const noResults = document.getElementById('noSearchResults');

    if (search && table) {
        search.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            let visible = 0;

            rows.forEach(function (row) {
                const match = row.textContent.toLowerCase().includes(term);
                row.classList.toggle('d-none', !match);
                if (match) visible++;
            });

            if (noResults) noResults.classList.toggle('d-none', visible !== 0);
            table.classList.toggle('d-none', visible === 0);
        });
    }
});
