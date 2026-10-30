document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('deleteEmployeeId').value = button.getAttribute('data-id');
            document.getElementById('deleteEmployeeName').textContent = button.getAttribute('data-name');
        });
    }

    const search = document.getElementById('employeeSearch') || document.getElementById('productSearch');
    const table = document.getElementById('employeesTable') || document.getElementById('productsTable');
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

    // Product add/edit modal
    const productModalEl = document.getElementById('productModal');
    if (!productModalEl) return;

    const productModal = new bootstrap.Modal(productModalEl);
    const form = document.getElementById('productForm');
    const alertBox = document.getElementById('productFormAlert');
    const saveBtn = document.getElementById('productSaveBtn');
    const title = document.getElementById('productModalLabel');
    const fields = ['name', 'category', 'price', 'quantity', 'description'];

    function fieldEl(name) { return form.elements[name]; }

    function clearErrors() {
        alertBox.classList.add('d-none');
        fields.forEach(function (name) {
            const el = fieldEl(name);
            el.classList.remove('is-invalid');
            el.parentElement.querySelector('.invalid-feedback').textContent = '';
        });
    }

    function showError(name, message) {
        const el = fieldEl(name);
        el.classList.add('is-invalid');
        el.parentElement.querySelector('.invalid-feedback').textContent = message;
    }

    function validate() {
        const errors = {};
        const v = function (name) { return fieldEl(name).value.trim(); };
        if (!v('name')) errors.name = 'Product name is required.';
        if (!v('category')) errors.category = 'Category is required.';
        if (v('price') === '') errors.price = 'Price is required.';
        else if (isNaN(Number(v('price')))) errors.price = 'Enter a valid price.';
        else if (Number(v('price')) < 0) errors.price = 'Price cannot be negative.';
        if (v('quantity') === '') errors.quantity = 'Quantity is required.';
        else if (!/^-?\d+$/.test(v('quantity'))) errors.quantity = 'Quantity must be a whole number.';
        else if (Number(v('quantity')) < 0) errors.quantity = 'Quantity cannot be negative.';
        return errors;
    }

    function openModal(product) {
        form.reset();
        clearErrors();
        document.getElementById('productId').value = product ? product.id : '';
        title.textContent = product ? 'Edit Product' : 'Add Product';
        if (product) {
            fields.forEach(function (name) { fieldEl(name).value = product[name] ?? ''; });
        }
        productModal.show();
    }

    document.getElementById('addProductBtn').addEventListener('click', function () { openModal(null); });
    document.querySelectorAll('.edit-product-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(JSON.parse(btn.getAttribute('data-product')));
        });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors();

        const errors = validate();
        if (Object.keys(errors).length) {
            Object.keys(errors).forEach(function (name) { showError(name, errors[name]); });
            return;
        }

        saveBtn.disabled = true;
        fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    productModal.hide();
                    window.location.reload();
                    return;
                }
                if (data.errors) {
                    Object.keys(data.errors).forEach(function (name) { showError(name, data.errors[name]); });
                } else {
                    alertBox.textContent = data.message || 'Could not save product.';
                    alertBox.classList.remove('d-none');
                }
                saveBtn.disabled = false;
            })
            .catch(function () {
                alertBox.textContent = 'Network error. Please try again.';
                alertBox.classList.remove('d-none');
                saveBtn.disabled = false;
            });
    });
});
