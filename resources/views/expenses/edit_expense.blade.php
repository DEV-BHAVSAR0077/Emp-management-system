@extends('layouts.app')

@section('title', 'Edit Expense — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header">
            <h2>Edit Expense: {{ $expense->name }}</h2>
            <!-- <div class="panel-actions">
                <a href="{{ route('dashboard', ['tab' => 'expenses']) }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
            </div> -->
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('expenses.update', $expense) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Expense Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" placeholder="e.g. Office Supplies, Travel Reimbursement…" value="{{ old('name', $expense->name) }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="amount">Amount (₹) <span style="color:var(--danger);">*</span></label>
                        <input type="number" id="amount" name="amount" placeholder="0.00" step="0.01" min="0.01" value="{{ old('amount', $expense->amount) }}" class="{{ $errors->has('amount') ? 'input-error' : '' }}" required />
                        @error('amount')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="expense_date">Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" class="{{ $errors->has('expense_date') ? 'input-error' : '' }}" required />
                        @error('expense_date')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                @include('expenses.partials.category_section', [
                    'categories'          => $categories,
                    'selectedCategory'    => old('expense_category_id', $expense->expense_category_id),
                    'selectedSubCategory' => old('expense_sub_category_id', $expense->expense_sub_category_id),
                ])

                <div class="form-group">
                    <label for="agency_vendor_id">Agency / Vendor <span style="color:var(--danger);">*</span></label>
                    <select id="agency_vendor_id" name="agency_vendor_id" class="{{ $errors->has('agency_vendor_id') ? 'input-error' : '' }}" required>
                        <option value="">— Select an Agency / Vendor —</option>
                        @foreach($agencyVendors as $av)
                            <option value="{{ $av->id }}" {{ old('agency_vendor_id', $expense->agency_vendor_id) == $av->id ? 'selected' : '' }}>
                                {{ $av->name }} ({{ $av->type }})
                            </option>
                        @endforeach
                    </select>
                    @error('agency_vendor_id')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" placeholder="Optional details about this expense…" maxlength="1000">{{ old('note', $expense->note) }}</textarea>
                    @error('note')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('expenses.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="btn-update-expense">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
