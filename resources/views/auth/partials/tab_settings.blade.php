@if($settingsTabActive ?? false)
<div class="tab-pane active" id="tab-settings">
    <div class="panel">
        <div class="panel-header">
            <h2>Application Settings</h2>
            <div class="panel-actions">
                <button type="submit" form="form-settings" class="btn btn-primary">Save Settings</button>
            </div>
        </div>

        <div style="padding: 20px;">
            <form action="{{ route('settings.store') }}" method="POST" id="form-settings">
                @csrf
                
                @php
                    $selectedRoles = [];
                    if (isset($settings['weekly_report_roles']) && !empty($settings['weekly_report_roles'])) {
                        $selectedRoles = json_decode($settings['weekly_report_roles'], true) ?? [];
                    }
                @endphp

                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0 0 5px 0; font-size: 16px;">Financial Report</h3>
                            <p style="margin: 0; color: var(--text-muted, #6b7280); font-size: 14px;">Enable or disable the financial report (Expenses, Payments, Final Amount) sent via email.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="weekly_report_enabled" {{ isset($settings['weekly_report_enabled']) && $settings['weekly_report_enabled'] == '1' ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 5px;">Report Frequency</label>
                        <select name="financial_report_frequency" class="form-control" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid var(--border-color, #e5e7eb);">
                            <option value="daily" {{ (isset($settings['financial_report_frequency']) && $settings['financial_report_frequency'] == 'daily') ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ (isset($settings['financial_report_frequency']) && $settings['financial_report_frequency'] == 'weekly') || !isset($settings['financial_report_frequency']) ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ (isset($settings['financial_report_frequency']) && $settings['financial_report_frequency'] == 'monthly') ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    
                    <div style="border: 1px solid var(--border-color, #e5e7eb); border-radius: 6px; background-color: #f8fafc; overflow: hidden;">
                        <div style="padding: 12px 15px; font-weight: 600; border-bottom: 1px solid var(--border-color, #e5e7eb); background-color: #ffffff;">
                            Roles
                        </div>
                        <div style="max-height: 250px; overflow-y: auto; padding: 10px;">
                            @forelse($roles as $role)
                                <label style="display: flex; align-items: center; padding: 8px; cursor: pointer; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="weekly_report_roles[]" value="{{ $role->name }}" {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }} style="margin-right: 12px; width: 16px; height: 16px; cursor: pointer;">
                                    <span style="font-size: 14px; color: var(--text-color, #374151);">{{ $role->name }}</span>
                                </label>
                            @empty
                                <div style="padding: 10px; color: var(--text-muted, #6b7280); font-size: 14px;">No roles available.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endif

<style>
/* Toggle Switch Styles */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
input:checked + .slider {
  background-color: #4f46e5;
}
input:focus + .slider {
  box-shadow: 0 0 1px #4f46e5;
}
input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
.slider.round {
  border-radius: 34px;
}
.slider.round:before {
  border-radius: 50%;
}
</style>
