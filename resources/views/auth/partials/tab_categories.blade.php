@if($categoriesTabActive)
<div class="tab-pane active" id="tab-categories">
    <div class="panel">
        <div class="panel-header">
            <h2>Categories
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ count($categories) }}</span>
            </h2>
            <div class="panel-actions">
                {{-- Add Category --}}
                @can('create-category')
                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm" id="btn-add-main-category">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    New Category
                </a>
                @endcan
            </div>
        </div>

        <div class="table-wrap">
            @if(count($categories) > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Sub-Categories</th>
                        <th>Last Updated</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $c)
                    <tr id="category-row-{{ $c->id }}">
                        <td style="color:var(--text-muted); width:50px;">
                            {{ $index + 1 }}
                        </td>
                        <td>
                            <strong>{{ $c->name }}</strong>
                        </td>
                        <td>
                            @if($c->subCategories->count() > 0)
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    @foreach($c->subCategories as $sub)
                                        <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px;">{{ $sub->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">None</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $c->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                @can('edit-category')
                                <a
                                    href="{{ route('categories.edit', $c) }}"
                                    class="btn btn-edit btn-sm"
                                    title="Edit category"
                                >
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    <!-- Edit -->
                                </a>
                                @endcan

                                @can('delete-category')
                                <form method="POST" action="{{ route('categories.destroy', $c) }}" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($c->name) }}\'? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete category">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        <!-- Delete -->
                                    </button>
                                </form>
                                @endcan

                                @cannot('edit-category')
                                    @cannot('delete-category')
                                        <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                                    @endcannot
                                @endcannot
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <p>No categories found. Add the first one!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif
