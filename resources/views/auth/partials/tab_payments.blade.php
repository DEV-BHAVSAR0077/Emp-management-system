{{-- Payments Tab --}}

@can('view-payment')
<div id="payments-tab" class="tab-content {{ $paymentsTabActive ? 'active' : '' }}">
    <div class="panel">
        <div class="panel-header">
            <h2>All Payments
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $payments->total() }}</span>
            </h2>
            <div class="panel-actions">
                <form method="GET" action="{{ route('payments.index') }}" class="search-form" id="form-payment-search" style="display:flex; align-items:center; gap:0.5rem;">
                    <input type="text" name="payment_search" id="input-payment-search" class="search-input" placeholder="Search payments…" value="{{ $paymentSearch }}" autocomplete="off" style="max-width: 200px; height: 34px;" />
                    
                    <div class="search-input" style="display:flex; align-items:center; gap:0.4rem; width:auto; padding-top:0; padding-bottom:0; background:var(--bg); height: 34px;">
                        <input type="date" name="payment_start_date" value="{{ $paymentStartDate ?? '' }}" title="Start Date" style="border:none; background:transparent; outline:none; font-family:inherit; font-size:inherit; color:var(--text); padding:0; margin:0; max-height: 34px;" />
                        <span style="color:var(--text-muted); font-size:0.85rem;">to</span>
                        <input type="date" name="payment_end_date" value="{{ $paymentEndDate ?? '' }}" title="End Date" style="border:none; background:transparent; outline:none; font-family:inherit; font-size:inherit; color:var(--text); padding:0; margin:0; max-height: 34px;" />
                    </div>

                    @if ($paymentSearch || !empty($paymentStartDate) || !empty($paymentEndDate))
                        <a href="{{ route('payments.index') }}" class="btn btn-ghost btn-sm" id="btn-clear-payment-search" title="Clear filters" style="height: 34px;">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm" id="btn-payment-search" style="height: 34px;">Filter</button>
                </form>
                @can('create-payment')
                <button type="button" class="btn btn-primary btn-sm" id="btn-open-create-payment" style="height: 34px;" onclick="openPaymentModal()">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Record Payment
                </button>
                @endcan
            </div>
        </div>

        @if($paymentSearch || !empty($paymentStartDate) || !empty($paymentEndDate))
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results 
                @if($paymentSearch) for <strong>"{{ $paymentSearch }}"</strong> @endif
                @if(!empty($paymentStartDate) && !empty($paymentEndDate))
                    between <strong>{{ \Carbon\Carbon::parse($paymentStartDate)->format('d M Y') }}</strong> and <strong>{{ \Carbon\Carbon::parse($paymentEndDate)->format('d M Y') }}</strong>
                @elseif(!empty($paymentStartDate))
                    from <strong>{{ \Carbon\Carbon::parse($paymentStartDate)->format('d M Y') }}</strong> onwards
                @elseif(!empty($paymentEndDate))
                    up to <strong>{{ \Carbon\Carbon::parse($paymentEndDate)->format('d M Y') }}</strong>
                @endif
                — {{ $payments->total() }} {{ Str::plural('payment', $payments->total()) }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($payments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent / Vendor</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Notes</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $index => $pay)
                    <tr id="payment-row-{{ $pay->id }}">
                        <td style="color:var(--text-muted); width:50px;">{{ $payments->firstItem() + $index }}</td>
                        <td>
                            @if($pay->agencyVendor)
                                <div style="font-weight:500;">{{ $pay->agencyVendor->name }}</div>
                                <span class="badge" style="background:#e5e7eb; color:#374151; font-size:.68rem;">{{ \App\Models\AgencyVendor::TYPES[$pay->agencyVendor->type] ?? '—' }}</span>
                            @else
                                <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="expense-amount" style="color:{{ $pay->payment_type === 0 ? 'var(--danger)' : 'var(--success)' }};"><span class="amount-symbol">₹</span>{{ number_format($pay->amount, 2) }}</span>
                        </td>
                        <td>
                            @if($pay->payment_type === 0)
                                <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:.7rem;">Debit</span>
                            @else
                                <span class="badge" style="background:#dcfce7; color:#166534; font-size:.7rem;">Credit</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $pay->payment_date->format('d M Y') }}</td>
                        <td>
                            @if($pay->notes)
                                <div style="font-size:.78rem; color:var(--text-muted); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $pay->notes }}">{{ $pay->notes }}</div>
                            @else
                                <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                @can('edit-payment')
                                <button type="button" class="btn btn-edit btn-sm btn-edit-payment"
                                    data-id="{{ $pay->id }}"
                                    data-vendor="{{ $pay->agency_vendor_id }}"
                                    data-amount="{{ $pay->amount }}"
                                    data-payment-type="{{ $pay->payment_type }}"
                                    data-date="{{ $pay->payment_date->format('Y-m-d') }}"
                                    data-notes="{{ $pay->notes }}"
                                    title="Edit payment">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    <!-- Edit -->
                                </button>
                                @endcan
                                @can('delete-payment')
                                <form method="POST" action="{{ route('payments.destroy', $pay) }}" id="form-del-payment-{{ $pay->id }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" id="btn-delete-payment-{{ $pay->id }}" title="Delete payment">
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
                        <td colspan="2" style="padding:.75rem 1rem; font-weight:600; color:var(--text-muted); font-size:.82rem;">Page Total</td>
                        <td style="padding:.75rem 1rem; font-weight:700;">
                            @php
                                $pageNet = $payments->sum(function($p) {
                                    return $p->payment_type === 0 ? -$p->amount : $p->amount;
                                });
                            @endphp
                            <span style="color:{{ $pageNet >= 0 ? 'var(--success)' : 'var(--danger)' }};">₹{{ number_format(abs($pageNet), 2) }}</span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
                    <p>
                        @if($paymentSearch)
                            No payments match your search. <a href="{{ route('payments.index') }}" style="color:var(--info);">Clear search</a>
                        @else
                            No payments recorded yet.
                            @can('create-payment') <button type="button" class="btn btn-ghost" style="color:var(--info); padding:0;" onclick="openPaymentModal()">Record your first payment</button> @endcan
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if($payments->hasPages())
        <div class="pagination-wrap">
            <div>Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }} payments</div>
            <div class="pagination-links">
                @if($payments->onFirstPage()) <span class="disabled">‹</span>
                @else <a href="{{ $payments->previousPageUrl() }}" id="btn-payment-prev">‹</a> @endif
                @foreach(range(1, $payments->lastPage()) as $page)
                    @if($page == $payments->currentPage()) <span class="active">{{ $page }}</span>
                    @else <a href="{{ $payments->url($page) }}" id="btn-payment-page-{{ $page }}">{{ $page }}</a> @endif
                @endforeach
                @if($payments->hasMorePages()) <a href="{{ $payments->nextPageUrl() }}" id="btn-payment-next">›</a>
                @else <span class="disabled">›</span> @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════ Payment Form Modal (Create & Edit) ═══════════ --}}
<div id="modal-payment-form" style="display:{{ old('modal_action') ? 'flex' : 'none' }}; position:fixed; inset:0; z-index:1000; align-items:center; justify-content:center;">
    {{-- Backdrop --}}
    <div id="modal-payment-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(3px);"></div>

    {{-- Dialog --}}
    <div id="modal-payment-dialog" style="
        position:relative; z-index:1; background:var(--card-bg, #fff);
        border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.25);
        width:100%; max-width:550px; margin:1rem;
        animation:modalSlideIn .2s ease;
        max-height: 90vh; overflow-y: auto;
    ">
        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color, #e5e7eb);">
            <div style="display:flex; align-items:center; gap:.6rem;">
                <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg,#10b981,#34d399); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="white"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                </div>
                <div>
                    <div id="modal-payment-title" style="font-weight:700; font-size:1rem; color:var(--text-color);">
                        {{ old('modal_action') === 'edit' ? 'Edit Payment' : 'Record New Payment' }}
                    </div>
                </div>
            </div>
            <button type="button" id="btn-close-payment-modal" style="background:none; border:none; cursor:pointer; color:var(--text-muted); padding:.25rem; border-radius:6px;" title="Close">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem;">
            <form id="form-payment-modal" method="POST" action="{{ old('modal_action') === 'edit' ? route('payments.update', old('payment_id', 0)) : route('payments.store') }}" novalidate>
                @csrf
                <input type="hidden" name="_method" id="payment_method" value="{{ old('modal_action') === 'edit' ? 'PUT' : 'POST' }}">
                <input type="hidden" name="modal_action" id="payment_modal_action" value="{{ old('modal_action', 'create') }}">
                <input type="hidden" name="payment_id" id="payment_id" value="{{ old('payment_id') }}">

                <div class="form-group">
                    <label for="agency_vendor_id">Agency / Vendor <span style="color:var(--danger);">*</span></label>
                    <select id="payment_agency_vendor_id" name="agency_vendor_id" class="{{ $errors->has('agency_vendor_id') ? 'input-error' : '' }}" required>
                        <option value="">— Select an Agency / Vendor —</option>
                        @foreach($agencyVendors ?? [] as $av)
                            <option value="{{ $av->id }}" data-balance="{{ ($av->expenses_sum_amount ?? 0) - ($av->payments_sum_amount ?? 0) }}" {{ old('agency_vendor_id') == $av->id ? 'selected' : '' }}>
                                {{ $av->name }} ({{ \App\Models\AgencyVendor::TYPES[$av->type] ?? $av->type }})
                            </option>
                        @endforeach
                    </select>
                    <div id="payment_balance_display" style="display:none; font-size: 0.85rem; margin-top: 0.4rem; color: var(--text-muted);">
                        Left to be paid: <strong id="payment_balance_amount">₹0.00</strong>
                    </div>
                    @error('agency_vendor_id')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="amount">Amount (₹) <span style="color:var(--danger);">*</span></label>
                        <input type="number" id="payment_amount" name="amount" placeholder="0.00" step="0.01" min="0.01" value="{{ old('amount') }}" class="{{ $errors->has('amount') ? 'input-error' : '' }}" required />
                        @error('amount')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="payment_type">Payment Type <span style="color:var(--danger);">*</span></label>
                        <select id="payment_type" name="payment_type" class="{{ $errors->has('payment_type') ? 'input-error' : '' }}" required>
                            <option value="1" {{ old('payment_type', '1') == '1' ? 'selected' : '' }}>Credit</option>
                            <option value="0" {{ old('payment_type') == '0' ? 'selected' : '' }}>Debit</option>
                        </select>
                        @error('payment_type')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="payment_date">Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="{{ $errors->has('payment_date') ? 'input-error' : '' }}" required />
                        @error('payment_date')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="payment_notes" name="notes" placeholder="Optional details about this payment…" maxlength="1000" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions" style="margin-top: 1.5rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-ghost" id="btn-cancel-payment-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-payment">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const modal    = document.getElementById('modal-payment-form');
    const backdrop = document.getElementById('modal-payment-backdrop');
    const form     = document.getElementById('form-payment-modal');

    window.openPaymentModal = function(btn = null) {
        if (btn) {
            // Edit Mode
            document.getElementById('modal-payment-title').textContent = 'Edit Payment';
            document.getElementById('payment_method').value = 'PUT';
            document.getElementById('payment_modal_action').value = 'edit';
            document.getElementById('payment_id').value = btn.dataset.id;
            
            document.getElementById('payment_agency_vendor_id').value = btn.dataset.vendor;
            document.getElementById('payment_amount').value = btn.dataset.amount;
            document.getElementById('payment_type').value = btn.dataset.paymentType || '1';
            document.getElementById('payment_date').value = btn.dataset.date;
            document.getElementById('payment_notes').value = btn.dataset.notes;

            form.action = `{{ url('payments') }}/${btn.dataset.id}`;
        } else {
            // Create Mode
            document.getElementById('modal-payment-title').textContent = 'Record New Payment';
            document.getElementById('payment_method').value = 'POST';
            document.getElementById('payment_modal_action').value = 'create';
            document.getElementById('payment_id').value = '';

            document.getElementById('payment_agency_vendor_id').value = '';
            document.getElementById('payment_amount').value = '';
            document.getElementById('payment_type').value = '1';
            document.getElementById('payment_date').value = '{{ date('Y-m-d') }}';
            document.getElementById('payment_notes').value = '';

            form.action = `{{ route('payments.store') }}`;
        }

        // Trigger change to update balance if in create mode, or hide if in edit mode
        if (btn) {
            document.getElementById('payment_balance_display').style.display = 'none';
        } else {
            document.getElementById('payment_agency_vendor_id').dispatchEvent(new Event('change'));
        }

        // Clear previous errors
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        form.querySelectorAll('.field-error').forEach(el => el.remove());

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    function closePaymentModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Bind edit buttons
    document.querySelectorAll('.btn-edit-payment').forEach(function (btn) {
        btn.addEventListener('click', function () { window.openPaymentModal(btn); });
    });

    // Close triggers
    document.getElementById('btn-close-payment-modal').addEventListener('click', closePaymentModal);
    document.getElementById('btn-cancel-payment-modal').addEventListener('click', closePaymentModal);
    backdrop.addEventListener('click', closePaymentModal);

    // Vendor change event to show left to be paid
    document.getElementById('payment_agency_vendor_id').addEventListener('change', function() {
        const display = document.getElementById('payment_balance_display');
        const amountEl = document.getElementById('payment_balance_amount');
        
        if (document.getElementById('payment_modal_action').value !== 'create') {
            display.style.display = 'none';
            return;
        }

        const option = this.options[this.selectedIndex];
        if (option && option.value && option.hasAttribute('data-balance')) {
            const bal = parseFloat(option.getAttribute('data-balance'));
            if (bal > 0) {
                amountEl.textContent = '₹' + bal.toFixed(2);
                amountEl.style.color = 'var(--danger)';
            } else if (bal < 0) {
                amountEl.textContent = '+₹' + Math.abs(bal).toFixed(2) + ' (Overpaid)';
                amountEl.style.color = 'var(--success)';
            } else {
                amountEl.textContent = '₹0.00';
                amountEl.style.color = 'var(--success)';
            }
            display.style.display = 'block';
        } else {
            display.style.display = 'none';
        }
    });

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closePaymentModal();
    });

    // ----- AJAX Submission & Real-time Validation ----- //

    const removeError = (input) => {
        input.classList.remove('input-error');
        const parent = input.parentNode;
        const existingError = parent.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    };

    const addError = (input, message) => {
        removeError(input);
        input.classList.add('input-error');
        const errorSpan = document.createElement('span');
        errorSpan.className = 'field-error';
        errorSpan.textContent = message;
        input.parentNode.appendChild(errorSpan);
    };

    document.getElementById('payment_amount').addEventListener('input', function() {
        if (this.value === '') {
            addError(this, 'Payment amount is required.');
        } else if (this.value < 0.01 || this.value > 9999999999.99) {
            addError(this, 'Amount must be between 0.01 and 9999999999.99.');
        } else {
            removeError(this);
        }
    });

    document.getElementById('payment_agency_vendor_id').addEventListener('change', function() {
        if (!this.value) {
            addError(this, 'Please select an agency or vendor.');
        } else {
            removeError(this);
        }
    });

    document.getElementById('payment_date').addEventListener('change', function() {
        if (!this.value) {
            addError(this, 'Payment date is required.');
        } else {
            removeError(this);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Final client side validation before submit
        let hasErrors = false;
        ['payment_amount', 'payment_agency_vendor_id', 'payment_date'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.dispatchEvent(new Event(id === 'payment_amount' ? 'input' : 'change'));
                if (el.classList.contains('input-error')) {
                    hasErrors = true;
                }
            }
        });

        if (hasErrors) return;

        const formData = new FormData(form);
        const url = form.action;
        const btnSave = document.getElementById('btn-save-payment');
        const originalText = btnSave.innerHTML;
        
        btnSave.innerHTML = 'Saving...';
        btnSave.disabled = true;

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => { throw { status: 422, errors: data.errors }; });
                }
                throw { status: response.status, message: 'An error occurred.' };
            }
            return response.json();
        })
        .then(data => {
            // Success: reload the page to update table and totals
            window.location.reload();
        })
        .catch(error => {
            btnSave.innerHTML = originalText;
            btnSave.disabled = false;

            if (error.status === 422 && error.errors) {
                for (const [field, messages] of Object.entries(error.errors)) {
                    let input = document.getElementById(`payment_${field}`) || document.querySelector(`[name="${field}"]`);
                    if (input) {
                        addError(input, messages[0]);
                    }
                }
            } else {
                alert(error.message || 'Error saving payment.');
            }
        });
    });
})();
</script>
@endcan
