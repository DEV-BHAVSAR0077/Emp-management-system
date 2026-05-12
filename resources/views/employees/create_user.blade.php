@extends('layouts.app')

@section('title', 'Create User — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header">
            <h2>Add New User</h2>
            <div class="panel-actions">
                <a href="{{ route('dashboard', ['tab' => 'emp']) }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
            </div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('users.store') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'input-error' : '' }}" required />
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters" class="{{ $errors->has('password') ? 'input-error' : '' }}" required />
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required />
                    </div>
                </div>

                @can('edit-role')
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}"
                                {{ old('role', 'User') === $r->name ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                @endcan

                <div class="form-actions">
                    <a href="{{ route('dashboard', ['tab' => 'emp']) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
@endsection