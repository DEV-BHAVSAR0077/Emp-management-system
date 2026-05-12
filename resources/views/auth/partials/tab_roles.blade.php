{{-- Roles Tab --}}

@can('view-role')
<div id="roles-tab" class="tab-content {{ $rolesTabActive ? 'active' : '' }}">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="roles-header">
        <h2>
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="color:#2563eb;">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Roles Management
            <span class="badge" style="font-size:.72rem;">{{ $roles->count() }}</span>
        </h2>
        @can('create-role')
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm" id="btn-create-role">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            New Role
        </a>
        @endcan
    </div>

    {{-- ── Role Cards Grid ─────────────────────────────────────────── --}}
    @if($roles->count() > 0)
    <div class="roles-grid" id="roles-grid">
        @php
            $roleIcons   = ['🛡️','👑','🔑','⚡','🌟','🎯','💼','🔒','🌐','🎖️','🚀','💡'];
            $idx = 0;
        @endphp

        @foreach($roles as $role)
        @php
            $icon        = $roleIcons[$idx % count($roleIcons)];
            $idx++;
            $cardColor   = $role->color ?: '#374151';
            $isProtected = strtolower($role->name) === 'admin';
        @endphp
        <div class="role-card" id="role-card-{{ $role->id }}">
            <div class="role-card-accent" style="background: {{ $cardColor }};"></div>
            <div class="role-card-body">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:.85rem;">
                    <div class="role-card-icon" style="background: {{ $cardColor }}22; color: {{ $cardColor }}; margin-bottom:0;">
                        {{ $icon }}
                    </div>
                </div>
                <div class="role-card-name">{{ $role->name }}</div>
                <div class="role-card-desc">{{ $role->description ?: 'No description provided.' }}</div>
            </div>
            <div class="role-card-footer">
                @if($isProtected)
                <span style="font-size:.72rem; color:var(--text-muted); padding:.3rem .5rem;">🔒 System</span>
                @else
                {{-- Edit button → goes to edit page --}}
                @can('edit-role')
                <a
                    href="{{ route('roles.edit', $role) }}"
                    class="btn btn-edit btn-sm"
                    id="btn-edit-role-{{ $role->id }}"
                    title="Edit role"
                >
                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                    Edit
                </a>
                @endcan

                @can('delete-role')
                {{-- Delete form (inline, no modal needed here — confirm on edit page) --}}
                <form method="POST" action="{{ route('roles.destroy', $role) }}" id="form-del-role-{{ $role->id }}"
                      onsubmit="return confirm('Delete role \'{{ addslashes($role->name) }}\'? All assigned users will be reset to User.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" id="btn-delete-role-{{ $role->id }}" title="Delete role">
                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                        Delete
                    </button>
                </form>
                @endcan
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="roles-empty">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
            <path d="M12 2L3.5 6.5v5c0 5.25 3.75 10.15 8.5 11.5C16.75 21.65 20.5 16.75 20.5 11.5v-5L12 2z"/>
        </svg>
        <h3>No roles yet</h3>
        @can('create-role')
        <p>Click <strong>New Role</strong> to create your first role.</p>
        @endcan
    </div>
    @endif
</div>{{-- /.roles-tab --}}
@endcan
