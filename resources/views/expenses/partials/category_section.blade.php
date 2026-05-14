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
        </div>
        @error('expense_category_id')<span class="field-error">{{ $message }}</span>@enderror
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
        </div>
        <span class="field-hint" id="subcategory-hint" style="{{ !empty($selectedCategory) ? 'display:none;' : '' }}">Select a category first to see sub-categories.</span>
        @error('expense_sub_category_id')<span class="field-error">{{ $message }}</span>@enderror
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

        api('/categories/' + categoryId + '/sub-categories', 'GET').then(function (res) {
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
    });

    // ── Initial State (Edit mode or Validation failure) ─────────
    // Sub-categories are already rendered by Blade using old() values, 
    // so we skip the initial AJAX fetch to preserve UX and avoid flashing.
    if (catSelect.value) {
        hint.style.display = 'none';
    }


});
</script>
