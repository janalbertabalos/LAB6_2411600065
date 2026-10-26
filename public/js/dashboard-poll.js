/**
 * FitPulse Tracker – Dashboard AJAX Polling (Lab 6)
 * Refreshes statistics and recent data every 30 seconds without full page reload.
 */
(function () {
    'use strict';

    const POLL_INTERVAL_MS = 30000;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    function formatMoney(n) {
        return '$' + Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 });
    }

    function animateValue(el, newText) {
        if (!el) return;
        if (el.textContent === String(newText)) return;
        el.classList.add('text-success');
        el.textContent = newText;
        setTimeout(() => el.classList.remove('text-success'), 600);
    }

    async function fetchJson(url) {
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
            },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    async function refreshStats() {
        try {
            const data = await fetchJson('/api/dashboard/stats');
            animateValue(document.getElementById('statTotalProducts'), data.total_products);
            animateValue(document.getElementById('statLowStock'), data.low_stock_count);
            animateValue(document.getElementById('statOutOfStock'), data.out_of_stock_count);
            animateValue(document.getElementById('statInventoryValue'), formatMoney(data.total_inventory_value));

            const lu = document.getElementById('lastUpdated');
            if (lu) {
                const d = new Date(data.updated_at);
                lu.textContent = d.toLocaleTimeString();
            }
        } catch (e) {
            console.warn('Dashboard stats poll failed:', e);
        }
    }

    async function refreshLowStock() {
        try {
            const { data } = await fetchJson('/api/products/low-stock');
            const list = document.getElementById('lowStockList');
            if (!list) return;

            if (!data.length) {
                list.innerHTML = '<li class="list-group-item text-muted text-center py-4">' +
                    '<i class="bi bi-check-circle text-success me-1"></i> All stock levels healthy.</li>';
                return;
            }

            list.innerHTML = data.map(p => {
                const badge = p.quantity <= 0
                    ? '<span class="badge bg-danger">0 left</span>'
                    : `<span class="badge bg-warning text-dark">${p.quantity} left</span>`;
                return `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="${p.url}" class="text-decoration-none fw-medium text-dark">${p.name}</a>
                        <br><small class="text-muted">Reorder at ${p.reorder_level}</small>
                    </div>
                    ${badge}
                </li>`;
            }).join('');
        } catch (e) {
            console.warn('Low-stock poll failed:', e);
        }
    }

    async function refreshRecentTransactions() {
        try {
            const { data } = await fetchJson('/api/transactions/recent');
            const tbody = document.getElementById('recentTransactionsBody');
            if (!tbody) return;

            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No transactions yet.</td></tr>';
                return;
            }

            tbody.innerHTML = data.slice(0, 8).map(t => {
                const sign = t.type === 'stock_out' ? '−' : (t.type === 'stock_in' ? '+' : '±');
                return `<tr>
                    <td><a href="${t.url}" class="text-decoration-none small fw-semibold">${(t.product_name || '—').substring(0, 25)}</a></td>
                    <td><span class="badge bg-${t.type_badge}">${t.type_label}</span></td>
                    <td class="text-end small fw-bold">${sign}${t.quantity}</td>
                    <td class="small text-muted">${t.created_at_human}</td>
                </tr>`;
            }).join('');
        } catch (e) {
            console.warn('Recent transactions poll failed:', e);
        }
    }

    function pollAll() {
        refreshStats();
        refreshLowStock();
        refreshRecentTransactions();
    }

    // Start polling after initial page load
    if (document.getElementById('statTotalProducts')) {
        setInterval(pollAll, POLL_INTERVAL_MS);
    }
})();
EOF