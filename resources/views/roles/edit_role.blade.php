@extends('layouts.app')

@section('title', 'Edit Role — ' . config('app.name'))
@section('main-class', '')

@section('content')
    <div class="panel" style="max-width: 620px; margin: 0 auto;">
        <div class="panel-header">
            <h2 style="display: flex; align-items: center; gap: .6rem;">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="color:#1d4ed8;">
                    <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                </svg>
                Edit Role: {{ $role->name }}
            </h2>
            <div class="panel-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-ghost btn-sm">Back to Roles</a>
            </div>
        </div>

        <div class="panel-body">
            @php $isProtected = strtolower($role->name) === 'admin'; @endphp

            @if($isProtected)
            <div class="notice">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
                This is a system role. You can update its color and description, but the name is locked.
            </div>
            @endif

            {{-- Live Preview --}}
            <div class="preview-wrap" style="margin-bottom: 1.5rem;">
                <div class="preview-label" style="font-size: .78rem; font-weight: 500; color: var(--text-muted); margin-bottom: .5rem;">Live Preview</div>
                <div class="role-preview" style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden; max-width: 240px; box-shadow: 0 2px 8px rgba(0,0,0,.07);">
                    <div class="preview-accent" id="preview-accent" style="height: 6px; transition: background .2s; background:{{ $role->color }};"></div>
                    <div class="preview-body" style="padding: 1rem 1.2rem .8rem;">
                        <div class="preview-icon" id="preview-icon" style="width: 40px; height: 40px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: .6rem; transition: background .2s, color .2s; background:{{ $role->color }}22; color:{{ $role->color }};">🛡️</div>
                        <div class="preview-name" id="preview-name" style="font-size: .95rem; font-weight: 600; margin-bottom: .15rem;">{{ $role->name }}</div>
                        <div class="preview-desc" id="preview-desc" style="font-size: .78rem; color: var(--text-muted);">{{ $role->description ?: 'Role description…' }}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('roles.update', $role) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Role Name <span style="color:var(--danger);">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="50"
                        value="{{ old('name', $role->name) }}"
                        class="{{ $errors->has('name') ? 'input-error' : '' }}"
                        {{ $isProtected ? 'readonly' : 'required' }}
                        oninput="document.getElementById('preview-name').textContent = this.value || 'Role Name'"
                        style="{{ $isProtected ? 'background:var(--bg); color:var(--text-muted); cursor:not-allowed;' : '' }}"
                    />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        maxlength="255"
                        placeholder="What does this role do?"
                        oninput="document.getElementById('preview-desc').textContent = this.value || 'Role description…'"
                    >{{ old('description', $role->description) }}</textarea>
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
                                    data-name="{{ $permission->name }}"
                                    {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                    style="width: 1.1rem; height: 1.1rem; accent-color: #2563eb; cursor: pointer; margin: 0;">
                                {{ $permission->display_name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>Card Color <span style="color:var(--danger);">*</span></label>
                    <input type="hidden" id="color" name="color" value="{{ old('color', $role->color) }}" />
                    <div class="color-swatches" id="color-swatches"></div>
                    @error('color')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; gap: .6rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                    {{-- Delete button (left side) — hidden for protected roles --}}
                    @if(!$isProtected)
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-delete').classList.add('open')">
                        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                        Delete Role
                    </button>
                    @else
                    <span></span>
                    @endif

                    <div style="display: flex; gap: .6rem;">
                        <a href="{{ route('roles.index') }}" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="btn-save-role">
                            <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    @if(!$isProtected)
    <div class="modal-backdrop" id="modal-delete">
        <div class="modal">
            <div class="confirm-icon">
                <svg width="22" height="22" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
            </div>
            <h3 style="font-size:1rem; font-weight:600; margin-bottom:.4rem;">Delete Role</h3>
            <p style="font-size:.86rem; color:var(--text-muted);">
                Are you sure you want to delete <strong style="color:var(--text);">{{ $role->name }}</strong>?<br>
                All users with this role will be reset to <strong style="color:var(--text);">User</strong>.
            </p>
            <form method="POST" action="{{ route('roles.destroy', $role) }}">
                @csrf
                @method('DELETE')
                <div class="confirm-footer">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-delete').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
    @endif

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

        // Close modal on backdrop click
        document.getElementById('modal-delete')?.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.getElementById('modal-delete')?.classList.remove('open');
        });

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
                    }
                });
            });
        });
    </script>
@endsection
