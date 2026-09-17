@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const initModalPagination = (tableId, pageSize = 10) => {
        const table = document.getElementById(tableId);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const total = rows.length;
        let current = 1;
        const totalPages = Math.ceil(total / pageSize);
        const render = () => {
            const start = (current - 1) * pageSize;
            const end = start + pageSize;
            rows.forEach((tr, i) => {
                tr.style.display = (i >= start && i < end) ? '' : 'none';
            });
            info.textContent = `Menampilkan ${Math.min(start + 1, total)}‑${Math.min(end, total)} dari ${total}`;
            prevBtn.disabled = current === 1;
            nextBtn.disabled = current === totalPages;
        };
        const container = document.createElement('div');
        container.className = 'd-flex justify-content-between align-items-center my-2';
        const info = document.createElement('span');
        const prevBtn = document.createElement('button');
        const nextBtn = document.createElement('button');
        prevBtn.className = 'btn btn-sm btn-outline-secondary';
        prevBtn.textContent = 'Prev';
        nextBtn.className = 'btn btn-sm btn-outline-secondary';
        nextBtn.textContent = 'Next';
        prevBtn.onclick = () => { if (current > 1) { current--; render(); } };
        nextBtn.onclick = () => { if (current < totalPages) { current++; render(); } };
        container.append(info, prevBtn, nextBtn);
        table.parentNode.insertBefore(container, table.nextSibling);
        render();
    };
    // custom event fired after handover rows are inserted
    document.addEventListener('handoversLoaded', function (e) {
        const tableId = e.detail.tableId; // e.g. 'handoversTable-123'
        const pageSize = e.detail.pageSize || 10;
        initModalPagination(tableId, pageSize);
    });
});
</script>
@endpush
