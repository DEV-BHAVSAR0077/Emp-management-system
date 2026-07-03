@extends('layouts.app')

@section('title', 'Edit User — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header">
            <h2>Edit User: {{ $editUser->name }}</h2>
            <!-- <div class="panel-actions">
                <a href="{{ route('dashboard', ['tab' => 'emp']) }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
            </div> -->
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('users.update', $editUser) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" value="{{ old('name', $editUser->name) }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email', $editUser->email) }}" class="{{ $errors->has('email') ? 'input-error' : '' }}" required />
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep" class="{{ $errors->has('password') ? 'input-error' : '' }}" />
                        <span class="field-hint">Leave empty to keep current password.</span>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password" />
                    </div>
                </div>

                @can('edit-role')
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select id="role_id" name="role_id">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}"
                                {{ old('role_id', $editUser->role_id) == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')<span class="field-error">{{ $message }}</span>@enderror
                    <span class="field-hint">Changing role grants or revokes management permissions.</span>
                </div>
                @endcan

                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection