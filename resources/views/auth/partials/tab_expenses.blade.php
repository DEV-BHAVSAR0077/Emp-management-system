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
                <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm" id="btn-open-create-expense">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Add Expense
                </a>
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
                        <th>Category</th>
                        <th>Date</th>
                        <th>Logged By</th>
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
                            @if($exp->category)
                                <span class="badge">{{ $exp->category->name }}</span>
                            @else
                                <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $exp->expense_date->format('d M Y') }}</td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar" style="width:28px; height:28px; font-size:.68rem;">{{ mb_substr($exp->user->name, 0, 2) }}</div>
                                <div style="font-size:.84rem; font-weight:500;">
                                    {{ $exp->user->name }}
                                    @if($exp->user_id === Auth::id())
                                        <span class="badge badge-you" style="font-size:.65rem;">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                @if($exp->trashed())
                                    @if($user->hasPermission('delete-expense') || $exp->user_id === Auth::id())
                                    <form method="POST" action="{{ route('expenses.restore', $exp->id) }}" id="form-restore-expense-{{ $exp->id }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm" id="btn-restore-expense-{{ $exp->id }}" title="Restore expense">
                                            <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                                            Restore
                                        </button>
                                    </form>
                                    @endif
                                @else
                                    @if($user->hasPermission('edit-expense') || $exp->user_id === Auth::id())
                                    <a href="{{ route('expenses.edit', $exp) }}" class="btn btn-edit btn-sm" id="btn-edit-expense-{{ $exp->id }}" title="Edit expense">
                                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                        Edit
                                    </a>
                                    @endif
                                    @if($user->hasPermission('delete-expense') || $exp->user_id === Auth::id())
                                    <form method="POST" action="{{ route('expenses.destroy', $exp) }}" id="form-del-expense-{{ $exp->id }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" id="btn-delete-expense-{{ $exp->id }}" title="Delete expense">
                                            <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                @endif
                                
                                @if(!$user->hasPermission('edit-expense') && !$user->hasPermission('delete-expense') && $exp->user_id !== Auth::id())
                                    <span style="font-size:.75rem; color:var(--text-muted);">—</span>
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
@endcan
