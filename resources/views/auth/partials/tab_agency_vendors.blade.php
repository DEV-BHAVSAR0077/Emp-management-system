{{-- Agency & Vendors Tab --}}
<div id="agency-vendors-tab" class="tab-content {{ $agencyVendorsTabActive ? 'active' : '' }}">
    <div class="panel">
        <div class="panel-header">
            <h2>Agency & Vendors
                <span class="badge" style="margin-left:.5rem; font-size:.72rem;">{{ $agencyVendors->total() }}</span>
            </h2>
            <div class="panel-actions">
                {{-- Search --}}
                <form method="GET" action="{{ route('agency_vendors.index') }}" class="search-form" id="form-search-av">
                    <input
                        type="text"
                        name="av_search"
                        class="search-input"
                        placeholder="Search name or email…"
                        value="{{ $avSearch }}"
                        autocomplete="off"
                    />
                    @if ($avSearch)
                        <a href="{{ route('agency_vendors.index') }}" class="btn btn-ghost btn-sm" title="Clear search">✕</a>
                    @endif
                    <button type="submit" class="btn btn-ghost btn-sm">Search</button>
                </form>

                {{-- Add Agency/Vendor --}}
                @can('create-agency-vendor')
                <a href="{{ route('agency_vendors.create') }}" class="btn btn-primary btn-sm">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    Add New
                </a>
                @endcan
            </div>
        </div>

        @if($avSearch)
            <div style="padding:.6rem 1.75rem; font-size:.82rem; color:var(--text-muted); background:var(--info-bg); border-bottom:1px solid #bfdbfe;">
                Showing results for <strong>"{{ $avSearch }}"</strong> — {{ $agencyVendors->total() }} found.
            </div>
        @endif

        <div class="table-wrap">
            @if($agencyVendors->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Total Expenses</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agencyVendors as $index => $av)
                    <tr>
                        <td style="color:var(--text-muted); width:50px;">
                            {{ $agencyVendors->firstItem() + $index }}
                        </td>
                        <td>
                            <div style="font-weight: 500; color:var(--text-color);">{{ $av->name }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:#e5e7eb; color:#374151;">{{ App\Models\AgencyVendor::TYPES[$av->type]  }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                @if($av->email)<div>{{ $av->email }}</div>@endif
                                @if($av->phone_number)<div style="color:var(--text-muted);">{{ $av->phone_number }}</div>@endif
                                @if($av->contact_person)<div style="color:var(--text-muted);">Contact Person: {{ $av->contact_person }}</div>@endif
                            </div>
                        </td>
                        <td>
                            @php
                                $rowTotal = $av->expenses_sum_amount ?? 0;
                            @endphp
                            <strong style="color: {{ $rowTotal > 0 ? 'var(--danger)' : 'var(--text-muted)' }}">
                                ₹{{ number_format((float)$rowTotal, 2) }}
                            </strong>
                        </td>
                        <td>
                            <div class="actions-cell" style="justify-content:center;">
                                @can('edit-agency-vendor')
                                <a href="{{ route('agency_vendors.edit', $av) }}" class="btn btn-edit btn-sm" title="Edit">
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/></svg>
                                    Edit
                                </a>
                                @endcan

                                @can('delete-agency-vendor')
                                <form method="POST" action="{{ route('agency_vendors.destroy', $av) }}" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($av->name) }}\'? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--border); background:var(--surface-alt, #f9fafb);">
                        <td colspan="4" style="padding:.75rem 1rem; font-weight:600; color:var(--text-muted); font-size:.82rem;">Page Total</td>
                        <td style="padding:.75rem 1rem; font-weight:700;">
                            ₹{{ number_format($agencyVendors->sum('expenses_sum_amount'), 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M3 21h18"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h2"/></svg>
                    <p>
                        @if($avSearch)
                            No records match your search. <a href="{{ route('agency_vendors.index') }}" style="color:var(--info);">Clear search</a>
                        @else
                            No agencies or vendors found. Add the first one!
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($agencyVendors->hasPages())
        <div class="pagination-wrap">
            <div>
                Showing {{ $agencyVendors->firstItem() }}–{{ $agencyVendors->lastItem() }} of {{ $agencyVendors->total() }} records
            </div>
            <div class="pagination-links">
                @if($agencyVendors->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $agencyVendors->previousPageUrl() }}">‹</a>
                @endif

                @foreach(range(1, $agencyVendors->lastPage()) as $page)
                    @if($page == $agencyVendors->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $agencyVendors->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($agencyVendors->hasMorePages())
                    <a href="{{ $agencyVendors->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
