{{-- Agency & Vendors Tab --}}
<div id="agency-vendors-tab" class="tab-content {{ $agencyVendorsTabActive ? 'active' : '' }}">
    <div class="panel">
        <div class="panel-header">
            <h2>Agency & Vendors
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $agencyVendors->total() }}</span>
            </h2>
            <div class="panel-actions">
                {{-- Search --}}
                <form method="GET" action="{{ route('agency_vendors.index') }}" class="search-form" id="form-search-av">
                    <input
                        type="text"
                        name="av_search"
                        class="search-input"
                        placeholder="Search name or email…"
                        value="{{ $avSearch }}"
                        autocomplete="off"
                    />
                    @if ($avSearch)
                        <a href="{{ route('agency_vendors.index') }}" class="btn btn-ghost btn-sm" title="Clear search">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm">Search</button>
                </form>

                {{-- Add Agency/Vendor --}}
                @can('create-agency-vendor')
                <a href="{{ route('agency_vendors.create') }}" class="btn btn-primary btn-sm">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Add New
                </a>
                @endcan
            </div>
        </div>

        @if($avSearch)
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results for <strong>"{{ $avSearch }}"</strong> — {{ $agencyVendors->total() }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($agencyVendors->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Remaining Balance</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agencyVendors as $index => $av)
                    <tr>
                        <td style="color:var(--text-muted); width:50px;">
                            {{ $agencyVendors->firstItem() + $index }}
                        </td>
                        <td>
                            <div style="font-weight: 500; color:var(--text-color);">{{ $av->name }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:#e5e7eb; color:#374151; font-size:12px;">{{ App\Models\AgencyVendor::TYPES[$av->type]  }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                @if($av->email)<div>{{ $av->email }}</div>@endif
                                @if($av->phone_number)<div style="color:var(--text-muted);">{{ $av->phone_number }}</div>@endif
                                @if($av->contact_person)<div style="color:var(--text-muted);">Contact Person: {{ $av->contact_person }}</div>@endif
                            </div>
                        </td>
                        <td>
                            @php
                                $remaining = (float)$av->balance;
                            @endphp
                            @if ($remaining < 0)
                                <strong style="color: var(--success);">
                                    +₹{{ number_format(abs($remaining), 2) }}
                                </strong>
                            @else
                                <strong style="color: {{ $remaining > 0 ? 'var(--danger)' : 'var(--text-muted)' }}">
                                    ₹{{ number_format($remaining, 2) }}
                                </strong>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                <button type="button" class="btn btn-primary btn-sm btn-view-payments" data-id="{{ $av->id }}" title="View Payments" style="background-color: var(--info); color: white; border-color: var(--info);">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    <!-- View -->
                                </button>
                                @can('edit-agency-vendor')
                                <a href="{{ route('agency_vendors.edit', $av->id) }}" class="btn btn-edit btn-sm" title="Edit">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    <!-- Edit -->
                                </a>
                                @endcan

                                @can('delete-agency-vendor')
                                <form method="POST" action="{{ route('agency_vendors.destroy', $av->id) }}" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($av->name) }}\'? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        <!-- Delete -->
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--border); background:var(--surface-alt, #f9fafb);">
                        <td colspan="4" style="padding:.75rem 1rem; font-weight:600; color:var(--text-muted); font-size:.82rem;">Page Total</td>
                        <td style="padding:.75rem 1rem; font-weight:700;">
                            @php
                                $totalRemaining = $agencyVendors->sum('balance');
                            @endphp
                            @if ($totalRemaining < 0)
                                <span style="color: var(--success);">+₹{{ number_format(abs($totalRemaining), 2) }}</span>
                            @else
                                <span style="color: {{ $totalRemaining > 0 ? 'var(--danger)' : 'var(--text-muted)' }}">₹{{ number_format($totalRemaining, 2) }}</span>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M3 21h18"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h2"/></svg>
                    <p>
                        @if($avSearch)
                            No records match your search. <a href="{{ route('agency_vendors.index') }}" style="color:var(--info);">Clear search</a>
                        @else
                            No agencies or vendors found. Add the first one!
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($agencyVendors->hasPages())
        <div class="pagination-wrap">
            <div>
                Showing {{ $agencyVendors->firstItem() }}–{{ $agencyVendors->lastItem() }} of {{ $agencyVendors->total() }} records
            </div>
            <div class="pagination-links">
                @if($agencyVendors->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $agencyVendors->previousPageUrl() }}">‹</a>
                @endif

                @foreach(range(1, $agencyVendors->lastPage()) as $page)
                    @if($page == $agencyVendors->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $agencyVendors->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($agencyVendors->hasMorePages())
                    <a href="{{ $agencyVendors->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- View Payments Modal --}}
<div id="modal-view-payments" style="display:none; position:fixed; inset:0; z-index:1000; align-items:center; justify-content:center;">
    <div id="modal-view-payments-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(3px);"></div>
    <div style="
        position:relative; z-index:1; background:var(--card-bg, #fff);
        border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25);
        width:100%; max-width:850px; margin:1rem;
        animation:modalSlideIn .2s ease;
        max-height: 90vh; display:flex; flex-direction:column;
    ">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color, #e5e7eb);">
            <div style="font-weight:700; font-size:1rem; color:var(--text-color);">
                Payment History: <span id="view-payments-vendor-name"></span>
                <span id="view-payments-final-balance" style="margin-left:1rem; font-size:0.9rem; padding:0.2rem 0.6rem; border-radius:6px; display:inline-block;"></span>
            </div>
            <button type="button" id="btn-close-view-payments" style="background:none; border:none; cursor:pointer; color:var(--text-muted); padding:.25rem; border-radius:6px;" title="Close">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>
        <div style="padding:1.5rem; overflow-y:auto; flex:1;">
            <div id="view-payments-loading" style="display:none; text-align:center; padding:2rem 0; color:var(--text-muted);">
                Loading payments...
            </div>
            <div id="view-payments-content">
                <table style="width:100%; margin-bottom:1rem; border-collapse:collapse;" id="view-payments-table">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:0.5rem 0.75rem; border-bottom:1px solid var(--border-color, #e5e7eb); width: 130px;">Date</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; border-bottom:1px solid var(--border-color, #e5e7eb); width: 140px;">Amount</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; border-bottom:1px solid var(--border-color, #e5e7eb); width: 90px;">Type</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; border-bottom:1px solid var(--border-color, #e5e7eb);">Notes</th>
                        </tr>
                    </thead>
                    <tbody id="view-payments-tbody">
                    </tbody>
                </table>
                <div id="view-payments-empty" style="display:none; text-align:center; padding:2rem 0; color:var(--text-muted);">
                    No payments found for this agency/vendor.
                </div>
                <div id="view-payments-pagination" class="pagination-links" style="display:flex; justify-content:center; gap:0.25rem; margin-top:1rem;">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('modal-view-payments');
    const backdrop = document.getElementById('modal-view-payments-backdrop');
    const closeBtn = document.getElementById('btn-close-view-payments');
    const tbody = document.getElementById('view-payments-tbody');
    const table = document.getElementById('view-payments-table');
    const emptyState = document.getElementById('view-payments-empty');
    const pagination = document.getElementById('view-payments-pagination');
    const loading = document.getElementById('view-payments-loading');
    const content = document.getElementById('view-payments-content');
    const vendorNameEl = document.getElementById('view-payments-vendor-name');

    let currentVendorId = null;

    function openModal(vendorId) {
        currentVendorId = vendorId;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        fetchPayments(vendorId, 1);
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function fetchPayments(vendorId, page) {
        loading.style.display = 'block';
        content.style.display = 'none';
        
        fetch(`/agency-vendors/${vendorId}/payments?page=${page}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            vendorNameEl.textContent = data.agency_vendor_name;

            const balEl = document.getElementById('view-payments-final-balance');
            if (balEl) {
                const bal = parseFloat(data.final_balance || 0);
                if (bal < 0) {
                    balEl.textContent = 'Overpaid: +₹' + Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    balEl.style.backgroundColor = '#dcfce7'; // var(--success-bg)
                    balEl.style.color = '#166534'; // var(--success)
                } else if (bal > 0) {
                    balEl.textContent = 'To Pay: ₹' + bal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    balEl.style.backgroundColor = '#fee2e2'; // var(--danger-bg)
                    balEl.style.color = '#991b1b'; // var(--danger)
                } else {
                    balEl.textContent = 'Settled: ₹0.00';
                    balEl.style.backgroundColor = '#f3f4f6';
                    balEl.style.color = 'var(--text-muted)';
                }
            }

            renderPayments(data.payments);
            renderPagination(data.pagination);
            
            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(err => {
            console.error('Error fetching payments:', err);
            loading.style.display = 'none';
        });
    }

    function renderPayments(payments) {
        tbody.innerHTML = '';
        if (payments.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = 'table';
            emptyState.style.display = 'none';
            
            payments.forEach(p => {
                const tr = document.createElement('tr');
                
                const tdDate = document.createElement('td');
                tdDate.style.padding = '0.75rem';
                tdDate.style.borderBottom = '1px solid var(--border-color, #e5e7eb)';
                tdDate.style.color = 'var(--text-muted)';
                tdDate.textContent = p.date_formatted;
                
                const tdAmount = document.createElement('td');
                tdAmount.style.padding = '0.75rem';
                tdAmount.style.borderBottom = '1px solid var(--border-color, #e5e7eb)';
                tdAmount.style.color = p.color;
                tdAmount.style.fontWeight = '500';
                tdAmount.textContent = '₹' + p.amount_formatted;
                
                const tdType = document.createElement('td');
                tdType.style.padding = '0.75rem';
                tdType.style.borderBottom = '1px solid var(--border-color, #e5e7eb)';
                const badge = document.createElement('span');
                badge.className = 'badge';
                badge.style.fontSize = '0.7rem';
                badge.style.background = p.badge_bg;
                badge.style.color = p.badge_color;
                badge.textContent = p.type_label;
                tdType.appendChild(badge);
                
                const tdNotes = document.createElement('td');
                tdNotes.style.padding = '0.75rem';
                tdNotes.style.borderBottom = '1px solid var(--border-color, #e5e7eb)';
                tdNotes.style.color = 'var(--text-muted)';
                tdNotes.style.fontSize = '0.85rem';
                tdNotes.textContent = p.notes || '—';
                
                tr.appendChild(tdDate);
                tr.appendChild(tdAmount);
                tr.appendChild(tdType);
                tr.appendChild(tdNotes);
                tbody.appendChild(tr);
            });
        }
    }

    function renderPagination(pag) {
        pagination.innerHTML = '';
        if (pag.last_page <= 1) return;

        const prev = document.createElement(pag.current_page > 1 ? 'a' : 'span');
        prev.textContent = '<';
        if (pag.current_page > 1) {
            prev.href = 'javascript:void(0)';
            prev.onclick = () => fetchPayments(currentVendorId, pag.current_page - 1);
        } else {
            prev.className = 'disabled';
        }
        pagination.appendChild(prev);

        for (let i = 1; i <= pag.last_page; i++) {
            const page = document.createElement(i === pag.current_page ? 'span' : 'a');
            page.textContent = i;
            if (i === pag.current_page) {
                page.className = 'active';
            } else {
                page.href = 'javascript:void(0)';
                page.onclick = () => fetchPayments(currentVendorId, i);
            }
            pagination.appendChild(page);
        }

        const next = document.createElement(pag.current_page < pag.last_page ? 'a' : 'span');
        next.textContent = '>';
        if (pag.current_page < pag.last_page) {
            next.href = 'javascript:void(0)';
            next.onclick = () => fetchPayments(currentVendorId, pag.current_page + 1);
        } else {
            next.className = 'disabled';
        }
        pagination.appendChild(next);
    }

    document.querySelectorAll('.btn-view-payments').forEach(btn => {
        btn.addEventListener('click', function() {
            openModal(this.dataset.id);
        });
    });

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });
})();
</script>
