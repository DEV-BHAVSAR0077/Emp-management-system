@if($categoriesTabActive)
<div class="tab-pane active" id="tab-categories">
    <div class="panel">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Category Management</h2>
            @can('create-category')
            <button type="button" class="btn btn-primary btn-sm" id="btn-add-main-category">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                New Category
            </button>
            @endcan
        </div>
        <div class="panel-body">
            
            {{-- Error Summary --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="color:var(--danger); background: #ffebee; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <strong>Validation Error:</strong> Please check the fields below.
                </div>
            @endif

            <div id="categories-container" style="display: flex; flex-direction: column; gap: 20px;">
                
                {{-- Form for creating a new Category (Hidden by default, shown via JS) --}}
                <div class="category-card" id="new-category-form-container" style="{{ $errors->has('name') && !old('_method') ? 'display:block;' : 'display:none;' }} border: 1px solid var(--border); padding: 15px; border-radius: 6px; background: var(--surface);">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h3 style="margin: 0; font-size: 1.1rem;">Create New Category</h3>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('new-category-form-container').style.display='none'">Cancel</button>
                        </div>

                        <div class="form-group">
                            <label>Category Name <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="name" value="{{ old('_method') ? '' : old('name') }}" placeholder="Main Category Name" class="{{ $errors->has('name') && !old('_method') ? 'input-error' : '' }}" required />
                            @if($errors->has('name') && !old('_method'))<span class="field-error">{{ $errors->first('name') }}</span>@endif
                        </div>

                        <div class="sub-categories-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label style="margin: 0;">Sub-Categories</label>
                                <button type="button" class="btn btn-ghost btn-sm btn-add-sub" data-target="new-category-subs">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                                    Add Sub-Category
                                </button>
                            </div>
                            
                            <div id="new-category-subs" style="display: flex; flex-direction: column; gap: 10px; margin-left: 1.5rem;">
                                {{-- Dynamic Sub-categories will be appended here --}}
                                @if(!old('_method') && old('sub_categories'))
                                    @foreach(old('sub_categories') as $index => $sub)
                                        <div class="sub-category-row" style="display: flex; gap: 10px;">
                                            <div style="flex-grow: 1;">
                                                <input type="text" name="sub_categories[{{ $index }}][name]" value="{{ $sub['name'] ?? '' }}" placeholder="Sub-category name" class="cat-inline-input" required />
                                                @error("sub_categories.{$index}.name")<span class="field-error">{{ $message }}</span>@enderror
                                            </div>
                                            <button type="button" class="btn btn-danger btn-remove-sub" style="color: var(--danger); background: transparent; border: 1px solid var(--danger);">
                                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="sub-category-row" style="display: flex; gap: 10px;">
                                        <div style="flex-grow: 1;">
                                            <input type="text" name="sub_categories[0][name]" placeholder="Sub-category name" class="cat-inline-input" required />
                                        </div>
                                        <button type="button" class="btn btn-danger btn-remove-sub" style="color: var(--danger); background: transparent; border: 1px solid var(--danger);">
                                            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Existing Categories Loop --}}
                @foreach($categories as $category)
                    <div class="category-card" style="border: 1px solid var(--border); padding: 15px; border-radius: 6px; background: var(--surface);">
                        <form method="POST" action="{{ route('categories.update', $category) }}">
                            @csrf
                            @method('PUT')
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <h3 style="margin: 0; font-size: 1.1rem;">Edit Category</h3>
                                @can('delete-category')
                                <button type="button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this category?')) document.getElementById('delete-cat-{{ $category->id }}').submit();">Delete</button>
                                @endcan
                            </div>

                            <div class="form-group">
                                <label>Category Name <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="name" value="{{ old('_method') === 'PUT' && old('category_id') == $category->id ? old('name') : $category->name }}" required @cannot('edit-category') disabled @endcannot />
                                <input type="hidden" name="category_id" value="{{ $category->id }}">
                            </div>

                            <div class="sub-categories-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <label style="margin: 0;">Sub-Categories</label>
                                    @can('edit-category')
                                    <button type="button" class="btn btn-ghost btn-sm btn-add-sub" data-target="edit-category-subs-{{ $category->id }}">
                                        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                                        Add Sub-Category
                                    </button>
                                    @endcan
                                </div>
                                
                                <div id="edit-category-subs-{{ $category->id }}" style="display: flex; flex-direction: column; gap: 10px; margin-left: 1.5rem;">
                                    @php
                                        // Use old data if validation failed for THIS specific category
                                        $subs = (old('_method') === 'PUT' && old('category_id') == $category->id && old('sub_categories')) 
                                                ? old('sub_categories') 
                                                : $category->subCategories->toArray();
                                    @endphp

                                    @foreach($subs as $index => $sub)
                                        <div class="sub-category-row" style="display: flex; gap: 10px;" data-id="{{ $sub['id'] ?? '' }}">
                                            @if(!empty($sub['id']))
                                                <input type="hidden" name="sub_categories[{{ $index }}][id]" value="{{ $sub['id'] }}">
                                            @endif
                                            <div style="flex-grow: 1;">
                                                <input type="text" name="sub_categories[{{ $index }}][name]" value="{{ $sub['name'] ?? '' }}" placeholder="Sub-category name" class="cat-inline-input" required @cannot('edit-category') disabled @endcannot />
                                                @error("sub_categories.{$index}.name")
                                                    @if(old('category_id') == $category->id)
                                                        <span class="field-error">{{ $message }}</span>
                                                    @endif
                                                @enderror
                                            </div>
                                            @can('edit-category')
                                            <button type="button" class="btn btn-danger btn-remove-sub" data-db-id="{{ $sub['id'] ?? '' }}" style="color: var(--danger); background: transparent; border: 1px solid var(--danger);">
                                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                            </button>
                                            @endcan
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </form>
                        
                        {{-- Hidden Delete Form --}}
                        <form id="delete-cat-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

{{-- Template for new sub-category row used by JS --}}
<template id="sub-category-template">
    <div class="sub-category-row" style="display: flex; gap: 10px;">
        <div style="flex-grow: 1;">
            <input type="text" name="sub_categories[__INDEX__][name]" placeholder="Sub-category name" class="cat-inline-input" required />
        </div>
        <button type="button" class="btn btn-danger btn-remove-sub" style="color: var(--danger); background: transparent; border: 1px solid var(--danger);">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
        </button>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    // ── Toggle New Category Form ────────────────────────────────────────
    var addBtn = document.getElementById('btn-add-main-category');
    var formContainer = document.getElementById('new-category-form-container');
    if (addBtn && formContainer) {
        addBtn.addEventListener('click', function () {
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
            if (formContainer.style.display !== 'none') {
                var nameInput = formContainer.querySelector('input[name="name"]');
                if (nameInput) nameInput.focus();
            }
        });
    }

    // ── Add Sub-Category Row ("+") ──────────────────────────────────────
    var template = document.getElementById('sub-category-template');

    document.querySelectorAll('.btn-add-sub').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var container = document.getElementById(targetId);
            if (!container || !template) return;

            // Calculate next index
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

            // Clone the template and replace __INDEX__
            var clone = template.content.cloneNode(true);
            var inputs = clone.querySelectorAll('input');
            inputs.forEach(function (inp) {
                inp.name = inp.name.replace('__INDEX__', nextIndex);
            });

            container.appendChild(clone);

            // Focus the new input
            var newRows = container.querySelectorAll('.sub-category-row');
            var lastRow = newRows[newRows.length - 1];
            if (lastRow) {
                var newInput = lastRow.querySelector('input[type="text"]');
                if (newInput) newInput.focus();
            }
        });
    });

    // ── Remove Sub-Category Row ("-") ───────────────────────────────────
    document.addEventListener('click', function (e) {
        var removeBtn = e.target.closest('.btn-remove-sub');
        if (!removeBtn) return;

        var row = removeBtn.closest('.sub-category-row');
        if (!row) return;

        var dbId = removeBtn.getAttribute('data-db-id');

        // If this sub-category exists in the database, soft-delete it via AJAX
        if (dbId) {
            removeBtn.disabled = true;

            fetch('/sub-categories/' + dbId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.success) {
                    row.remove();
                } else {
                    alert('Failed to remove sub-category.');
                    removeBtn.disabled = false;
                }
            }).catch(function () {
                alert('An error occurred. Please try again.');
                removeBtn.disabled = false;
            });
        } else {
            // If it's a new row (not yet saved), simply remove from the DOM
            row.remove();
        }
    });

    // ── Auto-Save on Input Change (Blur/Enter) ──────────────────────────
    document.addEventListener('change', function(e) {
        if (e.target.matches('.category-card input[type="text"]')) {
            var form = e.target.closest('form');
            if (form) {
                form.submit();
            }
        }
    });
});
</script>

@endif
