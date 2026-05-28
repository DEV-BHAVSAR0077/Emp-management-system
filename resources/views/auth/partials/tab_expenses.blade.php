{{-- Expenses Tab --}}

@can('view-expense')
<div id="expenses-tab" class="tab-content {{ $expensesTabActive ? 'active' : '' }}">
    <div class="panel">
        <div class="panel-header">
            <h2>All Expenses
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $expenses->total() }}</span>
            </h2>
            <div class="panel-actions">
                <form method="GET" action="{{ route('expenses.index') }}" class="search-form" id="form-expense-search">
                    <select name="expense_status" class="search-input" style="width: auto; margin-right: .2rem;" onchange="this.form.submit()">
                        <option value="active" {{ $expenseStatus === 'active' ? 'selected' : '' }}>Active Expenses</option>
                        <option value="trashed" {{ $expenseStatus === 'trashed' ? 'selected' : '' }}>Deleted Expenses</option>
                    </select>

                    <input type="text" name="expense_search" id="input-expense-search" class="search-input" placeholder="Search expenses…" value="{{ $expenseSearch }}" autocomplete="off" />
                    @if ($expenseSearch)
                        <a href="{{ route('expenses.index', ['expense_status' => $expenseStatus]) }}" class="btn btn-ghost btn-sm" id="btn-clear-expense-search" title="Clear search">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm" id="btn-expense-search">Search</button>
                </form>
                @can('create-expense')
                <div class="dropdown" style="position:relative; display:inline-block;">
                    <button class="btn btn-primary btn-sm" onclick="document.getElementById('add-expense-dropdown').style.display = document.getElementById('add-expense-dropdown').style.display === 'none' ? 'block' : 'none';">
                        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                        Add Expense
                    </button>
                    <div id="add-expense-dropdown" class="dropdown-content" style="display:none; position:absolute; right:0; background-color:var(--card-bg, #fff); min-width:180px; box-shadow:0px 8px 16px 0px rgba(0,0,0,0.15); z-index:100; border-radius:8px; margin-top:5px; border:1px solid var(--border-color, #e5e7eb); overflow:hidden;">
                        <a href="javascript:void(0)" onclick="openImportModal(); document.getElementById('add-expense-dropdown').style.display = 'none';" style="color:var(--text-color); padding:10px 16px; text-decoration:none; display:block; font-size:13px; border-bottom:1px solid var(--border-color, #f3f4f6); transition:background 0.2s;" onmouseover="this.style.background='var(--hover-bg, #f9fafb)'" onmouseout="this.style.background='transparent'">Upload Excel</a>
                        <a href="{{ route('expenses.template') }}" style="color:var(--text-color); padding:10px 16px; text-decoration:none; display:block; font-size:13px; border-bottom:1px solid var(--border-color, #f3f4f6); transition:background 0.2s;" onmouseover="this.style.background='var(--hover-bg, #f9fafb)'" onmouseout="this.style.background='transparent'">Download Template</a>
                        <a href="{{ route('expenses.create') }}" style="color:var(--text-color); padding:10px 16px; text-decoration:none; display:block; font-size:13px; transition:background 0.2s;" onmouseover="this.style.background='var(--hover-bg, #f9fafb)'" onmouseout="this.style.background='transparent'">Add Expense Manually</a>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        @if($expenseSearch)
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results for <strong>"{{ $expenseSearch }}"</strong> — {{ $expenses->total() }} {{ Str::plural('expense', $expenses->total()) }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($expenses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Expense</th>
                        <th>Amount</th>
                        <th>Agency & Vendor</th>
                        <th>Date</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $index => $exp)
                    <tr id="expense-row-{{ $exp->id }}">
                        <td style="color:var(--text-muted); width:50px;">{{ $expenses->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight:500;">{{ $exp->name }}</div>
                            @if($exp->note)
                                <div style="font-size:.75rem; color:var(--text-muted); max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $exp->note }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="expense-amount"><span class="amount-symbol">₹</span>{{ number_format($exp->amount, 2) }}</span>
                        </td>
                        <td>
                            @if($exp->agencyVendor)
                                <div style="font-weight:500;">{{ $exp->agencyVendor->name }}</div>
                                <span class="badge" style="background:#e5e7eb; color:#374151; font-size:12px;">{{ \App\Models\AgencyVendor::TYPES[$exp->agencyVendor->type] ?? '—' }}</span>
                            @else
                                <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $exp->expense_date->format('d M Y') }}</td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">

                                {{-- View Button (always visible for anyone who can view expenses) --}}
                                <button type="button"
                                    class="btn btn-ghost btn-sm btn-view-expense"
                                    id="btn-view-expense-{{ $exp->id }}"
                                    title="View expense details"
                                    data-id="{{ $exp->id }}"
                                    data-name="{{ e($exp->name) }}"
                                    data-amount="{{ number_format($exp->amount, 2) }}"
                                    data-date="{{ $exp->expense_date->format('d M Y') }}"
                                    data-category="{{ $exp->category?->name ?? '—' }}"
                                    data-subcategory="{{ $exp->subCategory?->name ?? '—' }}"
                                    data-vendor="{{ $exp->agencyVendor?->name ?? '—' }}"
                                    data-note="{{ e($exp->note ?? '—') }}"
                                    data-user="{{ e($exp->user?->name ?? '—') }}"
                                    data-user-initials="{{ mb_strtoupper(mb_substr($exp->user?->name ?? '?', 0, 2)) }}"
                                    data-is-you="{{ $exp->user_id === Auth::id() ? '1' : '0' }}"
                                    data-trashed="{{ $exp->trashed() ? '1' : '0' }}">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    <!-- View -->
                                </button>

                                @if($exp->trashed())
                                    @if($user->hasPermission('delete-expense') || $exp->user_id === Auth::id())
                                    <form method="POST" action="{{ route('expenses.restore', $exp->id) }}" id="form-restore-expense-{{ $exp->id }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-restore btn-sm" id="btn-restore-expense-{{ $exp->id }}" title="Restore expense">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                                            Restore
                                        </button>
                                    </form>
                                    @endif
                                @else
                                    @if($user->hasPermission('edit-expense') || $exp->user_id === Auth::id())
                                    <a href="{{ route('expenses.edit', $exp) }}" class="btn btn-edit btn-sm" id="btn-edit-expense-{{ $exp->id }}" title="Edit expense">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                        <!-- Edit -->
                                    </a>
                                    @endif
                                    @if($user->hasPermission('delete-expense') || $exp->user_id === Auth::id())
                                    <form method="POST" action="{{ route('expenses.destroy', $exp) }}" id="form-del-expense-{{ $exp->id }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" id="btn-delete-expense-{{ $exp->id }}" title="Delete expense">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                            <!-- Delete -->
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
                    <p>
                        @if($expenseSearch)
                            No expenses match your search. <a href="{{ route('expenses.index', ['expense_status' => $expenseStatus]) }}" style="color:var(--info);">Clear search</a>
                        @else
                            No expenses recorded yet.
                            @can('create-expense') <a href="{{ route('expenses.create') }}" style="color:var(--info);">Add your first expense</a> @endcan
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if($expenses->hasPages())
        <div class="pagination-wrap">
            <div>Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses</div>
            <div class="pagination-links">
                @if($expenses->onFirstPage()) <span class="disabled">‹</span>
                @else <a href="{{ $expenses->previousPageUrl() }}" id="btn-expense-prev">‹</a> @endif
                @foreach(range(1, $expenses->lastPage()) as $page)
                    @if($page == $expenses->currentPage()) <span class="active">{{ $page }}</span>
                    @else <a href="{{ $expenses->url($page) }}" id="btn-expense-page-{{ $page }}">{{ $page }}</a> @endif
                @endforeach
                @if($expenses->hasMorePages()) <a href="{{ $expenses->nextPageUrl() }}" id="btn-expense-next">›</a>
                @else <span class="disabled">›</span> @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════ Expense View Modal ═══════════ --}}
<div id="modal-view-expense" style="display:none; position:fixed; inset:0; z-index:1000; align-items:center; justify-content:center;">
    {{-- Backdrop --}}
    <div id="modal-expense-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(3px);"></div>

    {{-- Dialog --}}
    <div id="modal-expense-dialog" style="
        position:relative; z-index:1; background:var(--card-bg, #fff);
        border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25);
        width:100%; max-width:500px; margin:1rem;
        animation:modalSlideIn .2s ease;
    ">
        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color, #e5e7eb);">
            <div style="display:flex; align-items:center; gap:.6rem;">
                <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg,#6366f1,#8b5cf6); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="white"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <div style="font-weight:700; font-size:1rem; color:var(--text-color);">Expense Details</div>
                    <div id="modal-expense-trashed-badge" style="display:none; font-size:.7rem; color:#ef4444; font-weight:600; margin-top:.1rem;">● Deleted</div>
                </div>
            </div>
            <!-- <button type="button" id="btn-close-expense-modal" style="background:none; border:none; cursor:pointer; color:var(--text-muted); padding:.25rem; border-radius:6px;" title="Close">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button> -->
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">

            {{-- Amount Hero --}}
            <div style="text-align:center; padding:1.25rem; background:linear-gradient(135deg,#f0f0ff,#f5f0ff); border-radius:10px;">
                <div style="font-size:.75rem; color:#7c3aed; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.25rem;">Total Amount</div>
                <div id="modal-expense-amount" style="font-size:2rem; font-weight:800; color:#4f46e5;">₹0.00</div>
            </div>

            {{-- Detail Grid --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">

                <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem;">
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Expense Name</div>
                    <div id="modal-expense-name" style="font-weight:600; font-size:.92rem; color:var(--text-color); word-break:break-word;"></div>
                </div>

                <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem;">
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Date</div>
                    <div id="modal-expense-date" style="font-weight:600; font-size:.92rem; color:var(--text-color);"></div>
                </div>

                <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem;">
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Category</div>
                    <div id="modal-expense-category" style="font-weight:600; font-size:.92rem; color:var(--text-color);"></div>
                </div>

                <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem;">
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Sub-Category</div>
                    <div id="modal-expense-subcategory" style="font-weight:600; font-size:.92rem; color:var(--text-color);"></div>
                </div>

                <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem; grid-column:1 / -1;">
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Agency / Vendor</div>
                    <div id="modal-expense-vendor" style="font-weight:600; font-size:.92rem; color:var(--text-color);"></div>
                </div>

            </div>

            {{-- Note --}}
            <div style="background:var(--hover-bg, #f9fafb); border-radius:8px; padding:.75rem;">
                <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem;">Note</div>
                <div id="modal-expense-note" style="font-size:.9rem; color:var(--text-color); line-height:1.5; white-space:pre-wrap; word-break:break-word;"></div>
            </div>

            {{-- Logged By --}}
            <div style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; background:var(--hover-bg, #f9fafb); border-radius:8px;">
                <div id="modal-expense-avatar" style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#8b5cf6); display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; color:#fff; flex-shrink:0;"></div>
                <div>
                    <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Logged By</div>
                    <div style="display:flex; align-items:center; gap:.4rem; margin-top:.15rem;">
                        <span id="modal-expense-user" style="font-weight:600; font-size:.92rem; color:var(--text-color);"></span>
                        <span id="modal-expense-you-badge" style="display:none; font-size:.65rem; font-weight:700; background:#e0e7ff; color:#4338ca; border-radius:4px; padding:.1rem .35rem;">YOU</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border-color, #e5e7eb); display:flex; justify-content:flex-end;">
            <button type="button" id="btn-close-expense-modal-footer" class="btn btn-ghost">Close</button>
        </div>
    </div>
</div>

<style>
@keyframes modalSlideIn {
    from { opacity:0; transform:translateY(-16px) scale(.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
</style>

<script>
(function () {
    const modal    = document.getElementById('modal-view-expense');
    const backdrop = document.getElementById('modal-expense-backdrop');

    function openExpenseModal(btn) {
        document.getElementById('modal-expense-amount').textContent      = '₹' + btn.dataset.amount;
        document.getElementById('modal-expense-name').textContent        = btn.dataset.name;
        document.getElementById('modal-expense-date').textContent        = btn.dataset.date;
        document.getElementById('modal-expense-category').textContent    = btn.dataset.category;
        document.getElementById('modal-expense-subcategory').textContent = btn.dataset.subcategory;
        document.getElementById('modal-expense-vendor').textContent      = btn.dataset.vendor;
        document.getElementById('modal-expense-note').textContent        = btn.dataset.note;
        document.getElementById('modal-expense-user').textContent        = btn.dataset.user;
        document.getElementById('modal-expense-avatar').textContent      = btn.dataset.userInitials;

        const youBadge = document.getElementById('modal-expense-you-badge');
        youBadge.style.display = btn.dataset.isYou === '1' ? 'inline' : 'none';

        const trashedBadge = document.getElementById('modal-expense-trashed-badge');
        trashedBadge.style.display = btn.dataset.trashed === '1' ? 'block' : 'none';

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeExpenseModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Bind view buttons
    document.querySelectorAll('.btn-view-expense').forEach(function (btn) {
        btn.addEventListener('click', function () { openExpenseModal(btn); });
    });

    // Close triggers
    // document.getElementById('btn-close-expense-modal').addEventListener('click', closeExpenseModal);
    document.getElementById('btn-close-expense-modal-footer').addEventListener('click', closeExpenseModal);
    backdrop.addEventListener('click', closeExpenseModal);

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeExpenseModal();
    });
})();
</script>

{{-- ═══════════ Import Excel Modal ═══════════ --}}
<div id="modal-import-expense" style="display:none; position:fixed; inset:0; z-index:1000; align-items:center; justify-content:center;">
    <div id="modal-import-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(3px);"></div>
    <div id="modal-import-dialog" style="
        position:relative; z-index:1; background:var(--card-bg, #fff);
        border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25);
        width:100%; max-width:450px; margin:1rem;
        animation:modalSlideIn .2s ease;
    ">
        <form action="{{ route('expenses.import') }}" method="POST" enctype="multipart/form-data" id="form-import-expense">
            @csrf
            <div style="display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color, #e5e7eb);">
                <div style="font-weight:700; font-size:1rem; color:var(--text-color);">Import Expenses from Excel</div>
                <button type="button" onclick="closeImportModal()" style="background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:1.2rem;">&times;</button>
            </div>
            <div style="padding:1.5rem;">
                <label style="display:block; font-size:.85rem; font-weight:600; color:var(--text-color); margin-bottom:.5rem;">Upload File (.xlsx, .xls, .csv)</label>
                <div style="position:relative; border:2px dashed var(--border-color, #d1d5db); border-radius:8px; padding:2rem; text-align:center; background:var(--hover-bg, #f9fafb); transition:border-color 0.2s;" id="import-dropzone">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-muted); margin-bottom:10px; display:inline-block;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <div style="font-size:.9rem; color:var(--text-muted); margin-bottom:.5rem;">Drag & drop your file here, or click to browse</div>
                    <input type="file" name="excel_file" id="excel_file" required accept=".csv, .xls, .xlsx" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer;" onchange="document.getElementById('import-file-name').textContent = this.files[0] ? this.files[0].name : ''">
                    <div id="import-file-name" style="font-size:.85rem; font-weight:600; color:var(--info);"></div>
                </div>
                
                @if($errors->has('import_errors') || $errors->has('excel_file'))
                    <div style="margin-top:1rem; padding:.75rem; background:#fee2e2; border:1px solid #ef4444; border-radius:8px; max-height:150px; overflow-y:auto;">
                        @if($errors->has('excel_file'))
                            <div style="font-size:.85rem; color:#b91c1c; font-weight:600; margin-bottom:.25rem;">{{ $errors->first('excel_file') }}</div>
                        @endif
                        @if($errors->has('import_errors'))
                            <div style="font-size:.85rem; color:#b91c1c; font-weight:600; margin-bottom:.25rem;">Import Validation Failed:</div>
                            <ul style="margin:0; padding-left:1.25rem; font-size:.8rem; color:#b91c1c;">
                                @foreach($errors->get('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--border-color, #e5e7eb); display:flex; justify-content:flex-end; gap:.5rem;">
                <button type="button" class="btn btn-ghost" onclick="closeImportModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-import">
                    <span id="import-btn-text">Import Data</span>
                    <span id="import-btn-loader" style="display:none;">Loading...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportModal() {
        document.getElementById('modal-import-expense').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeImportModal() {
        document.getElementById('modal-import-expense').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close dropdown on outside click
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    });

    document.getElementById('form-import-expense').addEventListener('submit', function() {
        document.getElementById('btn-submit-import').disabled = true;
        document.getElementById('import-btn-text').style.display = 'none';
        document.getElementById('import-btn-loader').style.display = 'inline';
    });

    // Auto-open modal if there are errors
    @if($errors->has('import_errors') || $errors->has('excel_file'))
        openImportModal();
    @endif
</script>

@endcan
