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

    // ---- Product list (loaded via AJAX) ----
    const listUrl = form.getAttribute('data-list-url');
    const body = document.getElementById('productsBody');
    const wrap = document.getElementById('productsWrap');
    const emptyBox = document.getElementById('productsEmpty');
    const loading = document.getElementById('productsLoading');
    const listAlert = document.getElementById('productsAlert');
    const countEl = document.getElementById('productCount');
    let products = [];

    function cell(text, className) {
        const td = document.createElement('td');
        if (className) td.className = className;
        td.textContent = text;
        return td;
    }

    function actionButton(label, icon, className) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm ' + className;
        btn.innerHTML = '<i class="bi bi-' + icon + ' me-1"></i>' + label;
        return btn;
    }

    function renderProducts() {
        body.innerHTML = '';
        products.forEach(function (p) {
            const tr = document.createElement('tr');
            tr.appendChild(cell('#' + p.id, 'muted'));
            tr.appendChild(cell(p.name, 'fw-semibold'));
            tr.appendChild(cell(p.category));
            tr.appendChild(cell('₹' + Number(p.price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }), 'fw-semibold'));
            tr.appendChild(cell(p.quantity));
            tr.appendChild(cell(p.description || '', 'muted'));

            const actions = document.createElement('td');
            actions.className = 'text-end text-nowrap';
            const edit = actionButton('Edit', 'pencil', 'btn-light border');
            edit.addEventListener('click', function () { openModal(p); });
            const del = actionButton('Delete', 'trash', 'btn-outline-danger');
            del.setAttribute('data-bs-toggle', 'modal');
            del.setAttribute('data-bs-target', '#deleteModal');
            del.setAttribute('data-id', p.id);
            del.setAttribute('data-name', p.name);
            actions.appendChild(edit);
            actions.appendChild(document.createTextNode(' '));
            actions.appendChild(del);
            tr.appendChild(actions);
            body.appendChild(tr);
        });
        countEl.textContent = products.length;
        wrap.classList.toggle('d-none', products.length === 0);
        emptyBox.classList.toggle('d-none', products.length !== 0);
        document.getElementById('productsTable').classList.remove('d-none');
        document.getElementById('noSearchResults').classList.add('d-none');
        if (search && search.value) search.dispatchEvent(new Event('input'));
    }

    function showListMessage(message, type) {
        listAlert.className = 'alert alert-' + type + ' m-3';
        listAlert.textContent = message;
    }

    function loadProducts() {
        return fetch(listUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                loading.classList.add('d-none');
                if (!data.success) {
                    showListMessage(data.message || 'Unable to load products.', 'danger');
                    return;
                }
                products = data.products;
                renderProducts();
            })
            .catch(function () {
                loading.classList.add('d-none');
                showListMessage('Network error while loading products.', 'danger');
            });
    }

    loadProducts();

    // ---- Delete via AJAX ----
    const deleteForm = document.getElementById('productDeleteForm');
    deleteForm.addEventListener('submit', function (event) {
        event.preventDefault();
        fetch(deleteForm.action, { method: 'POST', body: new FormData(deleteForm), headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                showListMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success) loadProducts();
            })
            .catch(function () { showListMessage('Network error. Please try again.', 'danger'); });
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
                    saveBtn.disabled = false;
                    showListMessage(data.message, 'success');
                    loadProducts();
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
