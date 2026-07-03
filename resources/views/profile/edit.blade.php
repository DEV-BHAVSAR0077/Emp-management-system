@extends('layouts.app')

@section('title', 'My Profile — ' . config('app.name'))
@section('main-class', 'main-narrow')

@section('content')
    <div class="panel">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">My Profile</h2>
            <div class="panel-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Back</a>
            </div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                    <div style="position: relative; display: inline-block;">
                        @if($user->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
                        @else
                            <div class="avatar" style="width: 100px; height: 100px; font-size: 2rem;">
                                {{ mb_substr($user->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    <div style="margin-top: 1rem;">
                        <label for="profile_photo" class="btn btn-ghost btn-sm" style="cursor: pointer;">
                            Change Photo
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" style="display: none;" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : ''">
                    </div>
                    <div id="file-name" class="field-hint" style="margin-top: 0.5rem;"></div>
                    @error('profile_photo')<span class="field-error" style="justify-content: center;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="{{ $errors->has('name') ? 'input-error' : '' }}" required />
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $errors->has('email') ? 'input-error' : '' }}" required />
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="report_frequency">Financial Report Frequency</label>
                    <select id="report_frequency" name="report_frequency" class="{{ $errors->has('report_frequency') ? 'input-error' : '' }}">
                        <option value="" {{ old('report_frequency', $user->report_frequency) === null ? 'selected' : '' }}>None (Do not send reports)</option>
                        <option value="daily" {{ old('report_frequency', $user->report_frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('report_frequency', $user->report_frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('report_frequency', $user->report_frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    <span class="field-hint">Receive an automated email with your financial summary.</span>
                    @error('report_frequency')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current" class="{{ $errors->has('password') ? 'input-error' : '' }}" />
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password" />
                    </div>
                </div>

                <div class="form-actions">
                    {{-- <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a> --}}
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection
