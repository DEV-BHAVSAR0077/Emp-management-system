@extends('layouts.app')

@section('title', 'Create Category — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header">
            <h2>Add New Category</h2>
            <!-- <div class="panel-actions">
                <a href="{{ route('dashboard', ['tab' => 'categories']) }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
            </div> -->
        </div>
        <div class="panel-body">
            @if ($errors->any())
                <div class="alert alert-danger" style="color:var(--danger); background: #ffebee; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <strong>Validation Error:</strong> Please check the fields below.
                </div>
            @endif

            <form method="POST" action="{{ route('categories.store') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name">Category Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Main Category Name" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="sub-categories-section form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label style="margin: 0;">Sub-Categories</label>
                        <button type="button" class="btn btn-ghost btn-sm btn-add-sub" data-target="new-category-subs">
                            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="margin-right: 4px;"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                            Add Sub-Category
                        </button>
                    </div>
                    
                    <div id="new-category-subs" style="display: flex; flex-direction: column; gap: 10px;">
                        @if(old('sub_categories'))
                            @foreach(old('sub_categories') as $index => $sub)
                                <div class="sub-category-row form-row" style="margin-bottom: 0; display: flex; gap: 10px;">
                                    <div style="flex-grow: 1;">
                                        <input type="text" name="sub_categories[{{ $index }}][name]" value="{{ $sub['name'] ?? '' }}" placeholder="Sub-category name" class="{{ $errors->has('sub_categories.'.$index.'.name') ? 'input-error' : '' }}" required />
                                        @error("sub_categories.{$index}.name")<span class="field-error">{{ $message }}</span>@enderror
                                    </div>
                                    <button type="button" class="btn btn-danger btn-remove-sub" style="flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px;">
                                        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="sub-category-row form-row" style="margin-bottom: 0; display: flex; gap: 10px;">
                                <div style="flex-grow: 1;">
                                    <input type="text" name="sub_categories[0][name]" placeholder="Sub-category name" required />
                                </div>
                                <button type="button" class="btn btn-danger btn-remove-sub" style="flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px;">
                                    <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('categories.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <template id="sub-category-template">
        <div class="sub-category-row form-row" style="margin-top: 10px; margin-bottom: 0; display: flex; gap: 10px;">
            <div style="flex-grow: 1;">
                <input type="text" name="sub_categories[__INDEX__][name]" placeholder="Sub-category name" required />
            </div>
            <button type="button" class="btn btn-danger btn-remove-sub" style="flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px;">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    </template>

    <script>
    // Include the JS for sub-categories
    document.addEventListener('DOMContentLoaded', function () {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrf = csrfMeta ? csrfMeta.content : '';

        var template = document.getElementById('sub-category-template');

        document.querySelectorAll('.btn-add-sub').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-target');
                var container = document.getElementById(targetId);
                if (!container || !template) return;

                var existingRows = container.querySelectorAll('.sub-category-row');
                var nextIndex = 0;
                existingRows.forEach(function (row) {
                    var inputs = row.querySelectorAll('input[name*="sub_categories"]');
                    inputs.forEach(function (inp) {
                        var match = inp.name.match(/sub_categories\[(\d+)\]/);
                        if (match) {
                            var idx = parseInt(match[1], 10);
                            if (idx >= nextIndex) nextIndex = idx + 1;
                        }
                    });
                });

                var clone = template.content.cloneNode(true);
                var inputs = clone.querySelectorAll('input');
                inputs.forEach(function (inp) {
                    inp.name = inp.name.replace('__INDEX__', nextIndex);
                });

                container.appendChild(clone);

                var newRows = container.querySelectorAll('.sub-category-row');
                var lastRow = newRows[newRows.length - 1];
                if (lastRow) {
                    var newInput = lastRow.querySelector('input[type="text"]');
                    if (newInput) newInput.focus();
                }
            });
        });

        document.addEventListener('click', function (e) {
            var removeBtn = e.target.closest('.btn-remove-sub');
            if (!removeBtn) return;

            var row = removeBtn.closest('.sub-category-row');
            if (!row) return;

            row.remove();
        });
    });
    </script>
@endsection
