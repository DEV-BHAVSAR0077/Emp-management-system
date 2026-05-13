{{-- Category & Sub-Category Selection with inline add/remove --}}
{{-- Variables expected: $categories (collection), $selectedCategory (id|null), $selectedSubCategory (id|null) --}}

<div class="cat-section" id="category-section">

    {{-- ── Category ──────────────────────────────────────────────── --}}
    <div class="form-group">
        <label for="expense_category_id">Category <span style="color:var(--danger);">*</span></label>
        <div class="cat-field-row">
            <select id="expense_category_id" name="expense_category_id" class="{{ $errors->has('expense_category_id') ? 'input-error' : '' }}" required>
                <option value="">Select a category…</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($selectedCategory ?? old('expense_category_id')) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="cat-btn cat-btn-add" id="btn-toggle-add-category" title="Add new category">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            </button>
            <button type="button" class="cat-btn cat-btn-remove" id="btn-remove-category" title="Delete selected category">
                <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        @error('expense_category_id')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Inline: Add New Category --}}
    <div class="cat-inline-form" id="form-add-category" style="display:none;">
        <div class="cat-inline-header">
            <span class="cat-inline-title">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                New Category
            </span>
        </div>
        <div class="cat-inline-body">
            <div class="cat-inline-fields">
                <input type="text" id="new-category-name" placeholder="Category name…" maxlength="100" class="cat-inline-input" />
            </div>
            <div class="cat-inline-actions">
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-category">Save</button>
                <button type="button" class="btn btn-ghost btn-sm" id="btn-cancel-category">Cancel</button>
            </div>
        </div>
        <div class="cat-inline-feedback" id="feedback-category"></div>
    </div>

    {{-- ── Sub-Category ──────────────────────────────────────────── --}}
    <div class="form-group" id="sub-category-group">
        <label for="expense_sub_category_id">Sub-Category</label>
        <div class="cat-field-row">
            <select id="expense_sub_category_id" name="expense_sub_category_id">
                <option value="">None (optional)</option>
                @if(!empty($selectedCategory))
                    @php 
                        // Find the selected category securely to retrieve its pre-loaded subCategories
                        $parentCat = $categories->firstWhere('id', $selectedCategory);
                    @endphp
                    @if($parentCat)
                        @foreach($parentCat->subCategories as $sub)
                            <option value="{{ $sub->id }}" {{ ($selectedSubCategory == $sub->id) ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    @endif
                @endif
            </select>
            <button type="button" class="cat-btn cat-btn-add" id="btn-toggle-add-subcategory" title="Add new sub-category">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            </button>
            <button type="button" class="cat-btn cat-btn-remove" id="btn-remove-subcategory" title="Delete selected sub-category">
                <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
            </button>
        </div>
        <span class="field-hint" id="subcategory-hint" style="{{ !empty($selectedCategory) ? 'display:none;' : '' }}">Select a category first to see sub-categories.</span>
        @error('expense_sub_category_id')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Inline: Add New Sub-Category --}}
    <div class="cat-inline-form cat-inline-child" id="form-add-subcategory" style="display:none;">
        <div class="cat-inline-header">
            <span class="cat-inline-title">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                New Sub-Category
            </span>
            <span class="cat-inline-parent-label" id="subcategory-parent-label">under: <strong>—</strong></span>
        </div>
        <div class="cat-inline-body">
            <div class="cat-inline-fields">
                <input type="text" id="new-subcategory-name" placeholder="Sub-category name…" maxlength="100" class="cat-inline-input" />
            </div>
            <div class="cat-inline-actions">
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-subcategory">Save</button>
                <button type="button" class="btn btn-ghost btn-sm" id="btn-cancel-subcategory">Cancel</button>
            </div>
        </div>
        <div class="cat-inline-feedback" id="feedback-subcategory"></div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var catSelect  = document.getElementById('expense_category_id');
    var subSelect  = document.getElementById('expense_sub_category_id');
    var hint       = document.getElementById('subcategory-hint');

    // ── Helpers ─────────────────────────────────────────────────
    function api(url, method, body) {
        var opts = {
            method: method,
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' }
        };
        if (body) opts.body = JSON.stringify(body);
        return fetch(url, opts).then(function (r) {
            return r.json().then(function (data) { return { ok: r.ok, status: r.status, data: data }; });
        });
    }

    function feedback(id, msg, type) {
        var el = document.getElementById(id);
        el.textContent = msg;
        el.className = 'cat-inline-feedback ' + (type || '');
        if (type === 'success') setTimeout(function () { el.textContent = ''; el.className = 'cat-inline-feedback'; }, 3000);
    }

    function toggleForm(id, show) {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    }

    function setBtnLoading(btn, isLoading, text) {
        if (isLoading) {
            if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = text || '...';
            btn.disabled = true;
        } else {
            if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
            btn.disabled = false;
        }
    }

    // ── Load sub-categories when category changes ───────────────
    function loadSubCategories(categoryId, selectValue) {
        subSelect.innerHTML = '<option value="">None (optional)</option>';
        if (!categoryId) {
            hint.style.display = '';
            return;
        }
        hint.style.display = 'none';
        
        var loadingOpt = document.createElement('option');
        loadingOpt.textContent = 'Loading...';
        subSelect.appendChild(loadingOpt);
        subSelect.disabled = true;

        api('/expense-categories/' + categoryId + '/sub-categories', 'GET').then(function (res) {
            subSelect.innerHTML = '<option value="">None (optional)</option>';
            subSelect.disabled = false;
            if (res.ok) {
                res.data.forEach(function (s) {
                    var opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    if (selectValue && selectValue == s.id) opt.selected = true;
                    subSelect.appendChild(opt);
                });
            }
        });
    }

    catSelect.addEventListener('change', function () {
        loadSubCategories(this.value);
        toggleForm('form-add-subcategory', false);
        var sel = catSelect.options[catSelect.selectedIndex];
        document.querySelector('#subcategory-parent-label strong').textContent = sel && sel.value ? sel.textContent.trim() : '—';
    });

    // ── Initial State (Edit mode or Validation failure) ─────────
    // Sub-categories are already rendered by Blade using old() values, 
    // so we skip the initial AJAX fetch to preserve UX and avoid flashing.
    if (catSelect.value) {
        hint.style.display = 'none';
        var initSel = catSelect.options[catSelect.selectedIndex];
        if (initSel) document.querySelector('#subcategory-parent-label strong').textContent = initSel.textContent.trim();
    }

    // ── Toggle inline forms ─────────────────────────────────────
    document.getElementById('btn-toggle-add-category').addEventListener('click', function () {
        var form = document.getElementById('form-add-category');
        toggleForm('form-add-category', form.style.display === 'none');
        if (form.style.display !== 'none') document.getElementById('new-category-name').focus();
    });
    document.getElementById('btn-cancel-category').addEventListener('click', function () {
        toggleForm('form-add-category', false);
        document.getElementById('new-category-name').value = '';
        feedback('feedback-category', '', '');
    });

    document.getElementById('btn-toggle-add-subcategory').addEventListener('click', function () {
        if (!catSelect.value) { alert('Please select a category first.'); return; }
        var form = document.getElementById('form-add-subcategory');
        toggleForm('form-add-subcategory', form.style.display === 'none');
        if (form.style.display !== 'none') document.getElementById('new-subcategory-name').focus();
    });
    document.getElementById('btn-cancel-subcategory').addEventListener('click', function () {
        toggleForm('form-add-subcategory', false);
        document.getElementById('new-subcategory-name').value = '';
        feedback('feedback-subcategory', '', '');
    });

    // ── Save Category ───────────────────────────────────────────
    document.getElementById('btn-save-category').addEventListener('click', function () {
        var btn = this;
        var nameInput = document.getElementById('new-category-name');
        var name = nameInput.value.trim();
        if (!name) { feedback('feedback-category', 'Category name is required.', 'error'); nameInput.focus(); return; }

        for (var i = 0; i < catSelect.options.length; i++) {
            if (catSelect.options[i].textContent.trim().toLowerCase() === name.toLowerCase()) {
                feedback('feedback-category', 'This category already exists.', 'error');
                return;
            }
        }

        setBtnLoading(btn, true, 'Saving...');
        nameInput.disabled = true;

        api('/expense-categories', 'POST', { name: name }).then(function (res) {
            setBtnLoading(btn, false);
            nameInput.disabled = false;
            
            if (res.ok) {
                var opt = document.createElement('option');
                opt.value = res.data.id;
                opt.textContent = res.data.name;
                opt.selected = true;
                catSelect.appendChild(opt);
                catSelect.dispatchEvent(new Event('change'));
                nameInput.value = '';
                feedback('feedback-category', 'Category "' + res.data.name + '" created.', 'success');
                toggleForm('form-add-category', false);
            } else {
                var msg = (res.data.errors && res.data.errors.name) ? res.data.errors.name[0] : (res.data.message || 'Failed to create.');
                feedback('feedback-category', msg, 'error');
            }
        });
    });

    // ── Delete Category ─────────────────────────────────────────
    document.getElementById('btn-remove-category').addEventListener('click', function () {
        var btn = this;
        var id = catSelect.value;
        if (!id) { alert('Please select a category to delete.'); return; }
        var name = catSelect.options[catSelect.selectedIndex].textContent.trim();
        if (!confirm('Delete category "' + name + '"? Its sub-categories will also be removed.')) return;

        setBtnLoading(btn, true);
        catSelect.disabled = true;

        api('/expense-categories/' + id, 'DELETE').then(function (res) {
            setBtnLoading(btn, false);
            catSelect.disabled = false;
            
            if (res.ok) {
                catSelect.querySelector('option[value="' + id + '"]').remove();
                catSelect.value = '';
                catSelect.dispatchEvent(new Event('change'));
            } else {
                alert(res.data.error || res.data.message || 'Failed to delete category.');
            }
        });
    });

    // ── Save Sub-Category ───────────────────────────────────────
    document.getElementById('btn-save-subcategory').addEventListener('click', function () {
        var btn = this;
        var nameInput = document.getElementById('new-subcategory-name');
        var name = nameInput.value.trim();
        if (!name) { feedback('feedback-subcategory', 'Sub-category name is required.', 'error'); nameInput.focus(); return; }
        if (!catSelect.value) { feedback('feedback-subcategory', 'Select a parent category first.', 'error'); return; }

        for (var i = 0; i < subSelect.options.length; i++) {
            if (subSelect.options[i].textContent.trim().toLowerCase() === name.toLowerCase()) {
                feedback('feedback-subcategory', 'This sub-category already exists.', 'error');
                return;
            }
        }

        setBtnLoading(btn, true, 'Saving...');
        nameInput.disabled = true;

        api('/expense-sub-categories', 'POST', { name: name, expense_category_id: catSelect.value }).then(function (res) {
            setBtnLoading(btn, false);
            nameInput.disabled = false;
            
            if (res.ok) {
                var opt = document.createElement('option');
                opt.value = res.data.id;
                opt.textContent = res.data.name;
                opt.selected = true;
                subSelect.appendChild(opt);
                nameInput.value = '';
                feedback('feedback-subcategory', 'Sub-category "' + res.data.name + '" created.', 'success');
                toggleForm('form-add-subcategory', false);
            } else {
                var msg = (res.data.errors && res.data.errors.name) ? res.data.errors.name[0] : (res.data.message || 'Failed to create.');
                feedback('feedback-subcategory', msg, 'error');
            }
        });
    });

    // ── Delete Sub-Category ─────────────────────────────────────
    document.getElementById('btn-remove-subcategory').addEventListener('click', function () {
        var btn = this;
        var id = subSelect.value;
        if (!id) { alert('Please select a sub-category to delete.'); return; }
        var name = subSelect.options[subSelect.selectedIndex].textContent.trim();
        if (!confirm('Delete sub-category "' + name + '"?')) return;

        setBtnLoading(btn, true);
        subSelect.disabled = true;

        api('/expense-sub-categories/' + id, 'DELETE').then(function (res) {
            setBtnLoading(btn, false);
            subSelect.disabled = false;
            
            if (res.ok) {
                subSelect.querySelector('option[value="' + id + '"]').remove();
                subSelect.value = '';
            } else {
                alert(res.data.error || res.data.message || 'Failed to delete sub-category.');
            }
        });
    });
});
</script>
