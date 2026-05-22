{{-- Employees Tab --}}

<div id="emp-tab" class="tab-content {{ $empTabActive ? 'active' : '' }}">
    <div class="panel">
        <div class="panel-header">
            <h2>All Users
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $users->total() }}</span>
            </h2>
            <div class="panel-actions">
                {{-- Search --}}
                <form method="GET" action="{{ route('users.index') }}" class="search-form" id="form-search">
                    <input
                        type="text"
                        name="search"
                        id="input-search"
                        class="search-input"
                        placeholder="Search name or email…"
                        value="{{ $search }}"
                        autocomplete="off"
                    />
                    @if ($search)
                        <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm" id="btn-clear-search" title="Clear search">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm" id="btn-search">Search</button>
                </form>

                {{-- Add User --}}
                @can('create-user')
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm" id="btn-open-create">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Add User
                </a>
                @endcan
            </div>
        </div>

        @if($search)
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results for <strong>"{{ $search }}"</strong> — {{ $users->total() }} {{ Str::plural('user', $users->total()) }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($users->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Last Updated</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr id="user-row-{{ $u->id }}">
                        <td style="color:var(--text-muted); width:50px;">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ mb_substr($u->name, 0, 2) }}</div>
                                <div>
                                    <div class="user-name">
                                        {{ $u->name }}
                                        @if($u->id === Auth::id())
                                            <span class="badge badge-you">You</span>
                                        @endif
                                    </div>
                                    <div class="user-id">#{{ $u->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php
                                $roleObj   = $rolesMap->get($u->role);
                                $roleColor = $roleObj?->color ?? '#374151';
                            @endphp
                            <span class="badge" style="background:{{ $roleColor }}18; color:{{ $roleColor }}; border:1px solid {{ $roleColor }}35;">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted);">{{ $u->created_at->format('d M Y') }}</td>
                        <td style="color:var(--text-muted);">{{ $u->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                @if($user->hasPermission('edit-user') || $u->id === Auth::id())
                                <a
                                    href="{{ route('users.edit', $u) }}"
                                    class="btn btn-edit btn-sm"
                                    id="btn-edit-{{ $u->id }}"
                                    title="Edit user"
                                >
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    @if($u->id === Auth::id() && !$user->hasPermission('edit-user'))
                                        <!-- Edit Profile -->
                                    @else
                                        <!-- Edit -->
                                    @endif
                                </a>
                                @endif

                                @if($user->hasPermission('delete-user') && $u->id !== Auth::id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}" id="form-del-user-{{ $u->id }}" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete user \'{{ addslashes($u->name) }}\'? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" id="btn-delete-{{ $u->id }}" title="Delete user">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        <!-- Delete -->
                                    </button>
                                </form>
                                @endif

                                @if(!$user->hasPermission('edit-user') && !$user->hasPermission('delete-user') && $u->id !== Auth::id())
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
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p>
                        @if($search)
                            No users match your search. <a href="{{ route('users.index') }}" style="color:var(--info);">Clear search</a>
                        @else
                            No users found. Add the first one!
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="pagination-wrap">
            <div>
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            <div class="pagination-links">
                @if($users->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" id="btn-prev-page">‹</a>
                @endif

                @foreach(range(1, $users->lastPage()) as $page)
                    @if($page == $users->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $users->url($page) }}" id="btn-page-{{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" id="btn-next-page">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>{{-- /.panel --}}
</div>{{-- /.emp-tab --}}


