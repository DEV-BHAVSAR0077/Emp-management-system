@extends('layouts.app')

@section('title', 'Create Role — ' . config('app.name'))
@section('main-class', '')

@section('content')
    <div class="panel" style="max-width: 620px; margin: 0 auto;">
        <div class="panel-header">
            <h2 style="display: flex; align-items: center; gap: .6rem;">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="color:#2563eb;">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Create New Role
            </h2>
            <div class="panel-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-ghost btn-sm">Back to Roles</a>
            </div>
        </div>

        <div class="panel-body">
            {{-- Live Preview --}}
            <div class="preview-wrap" style="margin-bottom: 1.5rem;">
                <div class="preview-label" style="font-size: .78rem; font-weight: 500; color: var(--text-muted); margin-bottom: .5rem;">Live Preview</div>
                <div class="role-preview" style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden; max-width: 240px; box-shadow: 0 2px 8px rgba(0,0,0,.07);">
                    <div class="preview-accent" id="preview-accent" style="height: 6px; transition: background .2s; background:#2563eb;"></div>
                    <div class="preview-body" style="padding: 1rem 1.2rem .8rem;">
                        <div class="preview-icon" id="preview-icon" style="width: 40px; height: 40px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: .6rem; transition: background .2s, color .2s; background:#2563eb22; color:#2563eb;">🛡️</div>
                        <div class="preview-name" id="preview-name" style="font-size: .95rem; font-weight: 600; margin-bottom: .15rem;">Role Name</div>
                        <div class="preview-desc" id="preview-desc" style="font-size: .78rem; color: var(--text-muted);">Role description…</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('roles.store') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name">Role Name <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="e.g. Manager, Developer, Support…"
                        maxlength="50"
                        value="{{ old('name') }}"
                        class="{{ $errors->has('name') ? 'input-error' : '' }}"
                        required
                        oninput="document.getElementById('preview-name').textContent = this.value || 'Role Name'"
                    />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="What does this role do?"
                        maxlength="255"
                        oninput="document.getElementById('preview-desc').textContent = this.value || 'Role description…'"
                    >{{ old('description') }}</textarea>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Permissions</label>
                    <div class="permissions-scroll" style="max-height: 220px; overflow-y: auto; border: 1px solid var(--border); border-radius: 6px; padding: .8rem 1rem; background: var(--bg);">
                        @foreach($permissions as $permission)
                            <label style="display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem; font-weight: 400; font-size: .88rem; cursor: pointer; color: var(--text); padding: .2rem 0;">
                                <input type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    data-name="{{ $permission->name }}" style="width: 1.1rem; height: 1.1rem; accent-color: #2563eb; cursor: pointer; margin: 0;">
                                {{ $permission->display_name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>Card Color <span style="color:var(--danger);">*</span></label>
                    <input type="hidden" id="color" name="color" value="{{ old('color', '#2563eb') }}" />
                    <div class="color-swatches" id="color-swatches"></div>
                    <span class="field-hint">Choose a color for this role's card accent.</span>
                    @error('color')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('roles.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="btn-create-role">
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const PAGE_ROLE_COLORS = [
            '#2563eb','#7c3aed','#db2777','#dc2626','#ea580c',
            '#d97706','#16a34a','#0891b2','#0d9488','#4f46e5',
            '#9333ea','#be185d','#374151',
        ];

        let selectedColor = document.getElementById('color').value || PAGE_ROLE_COLORS[0];

        function renderPageSwatches() {
            const container = document.getElementById('color-swatches');
            container.innerHTML = '';
            PAGE_ROLE_COLORS.forEach(function(color) {
                const s = document.createElement('div');
                s.className = 'color-swatch' + (color === selectedColor ? ' selected' : '');
                s.style.background = color;
                s.title = color;
                s.onclick = function() {
                    selectedColor = color;
                    document.getElementById('color').value = color;
                    container.querySelectorAll('.color-swatch').forEach(function(el){ el.classList.remove('selected'); });
                    s.classList.add('selected');
                    updatePreview(color);
                };
                container.appendChild(s);
            });
        }

        function updatePreview(color) {
            document.getElementById('preview-accent').style.background = color;
            document.getElementById('preview-icon').style.background   = color + '22';
            document.getElementById('preview-icon').style.color        = color;
        }

        renderPageSwatches();
        updatePreview(selectedColor);

        // Permission constraints logic
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
            const getCheckbox = (nameVal) => Array.from(checkboxes).find(c => c.dataset.name === nameVal);

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const pName = this.dataset.name;

                    // Checking create/edit/delete automatically checks 'view'
                    if (this.checked) {
                        if (['create-user', 'edit-user', 'delete-user'].includes(pName)) {
                            const v = getCheckbox('view-user');
                            if(v) v.checked = true;
                        }
                        if (['create-role', 'edit-role', 'delete-role'].includes(pName)) {
                            const v = getCheckbox('view-role');
                            if(v) v.checked = true;
                        }
                        if (['create-category', 'edit-category', 'delete-category'].includes(pName)) {
                            const v = getCheckbox('view-category');
                            if(v) v.checked = true;
                        }
                        if (['create-expense', 'edit-expense', 'delete-expense'].includes(pName)) {
                            const v = getCheckbox('view-expense');
                            if(v) v.checked = true;
                        }
                        if (['create-agency-vendor', 'edit-agency-vendor', 'delete-agency-vendor'].includes(pName)) {
                            const v = getCheckbox('view-agency-vendor');
                            if(v) v.checked = true;
                        }
                        if (['create-payment', 'edit-payment', 'delete-payment'].includes(pName)) {
                            const v = getCheckbox('view-payment');
                            if(v) v.checked = true;
                        }
                    }
 
                    // Unchecking 'view' automatically unchecks create/edit/delete
                    if (!this.checked) {
                        if (pName === 'view-user') {
                            ['create-user', 'edit-user', 'delete-user'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                        if (pName === 'view-role') {
                            ['create-role', 'edit-role', 'delete-role'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                        if (pName === 'view-category') {
                            ['create-category', 'edit-category', 'delete-category'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                        if (pName === 'view-expense') {
                            ['create-expense', 'edit-expense', 'delete-expense'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                        if (pName === 'view-agency-vendor') {
                            ['create-agency-vendor', 'edit-agency-vendor', 'delete-agency-vendor'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                        if (pName === 'view-payment') {
                            ['create-payment', 'edit-payment', 'delete-payment'].forEach(n => {
                                const c = getCheckbox(n);
                                if(c) c.checked = false;
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
