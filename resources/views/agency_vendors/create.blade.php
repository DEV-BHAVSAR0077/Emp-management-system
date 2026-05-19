@extends('layouts.app')

@section('title', 'Add Agency/Vendor — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header">
            <h2>Add Agency/Vendor</h2>
            <!-- <div class="panel-actions">
                <a href="{{ route('agency_vendors.index') }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
            </div> -->
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('agency_vendors.store') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="ABC Corp" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    @php
                        $types = App\Models\AgencyVendor::TYPES;
                    @endphp
                    <label for="type">Type</label>
                    <select id="type" name="type" class="{{ $errors->has('type') ? 'input-error' : '' }}">
                        @foreach ($types as $key => $value)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('type')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="contact@abccorp.com" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'input-error' : '' }}" />
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" placeholder="+1 234 567 8900" value="{{ old('phone_number') }}" class="{{ $errors->has('phone_number') ? 'input-error' : '' }}" />
                        @error('phone_number')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" placeholder="Jane Doe" value="{{ old('contact_person') }}" class="{{ $errors->has('contact_person') ? 'input-error' : '' }}" />
                        @error('contact_person')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('agency_vendors.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
