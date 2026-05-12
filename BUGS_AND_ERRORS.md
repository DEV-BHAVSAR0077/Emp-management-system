# Bugs and Errors Report

| File Name | Error in Line | Error Explanation | Solution of Error |
|---|---:|---|---|
| `database/seeders/DatabaseSeeder.php` | 25 | The main seeder calls only `PermissionSeeder`. `DefaultRolesSeeder` exists but is not called, so fresh database seeding may create permissions without creating the default roles. | Add `DefaultRolesSeeder::class` to the `$this->call([...])` list before or along with `PermissionSeeder::class`. |
| `database/seeders/DatabaseSeeder.php` | 25 | No seeder assigns permissions to the default roles. Authorization depends on role permissions through `Gate::before`, so seeded users can be unable to access user/role management features. | After creating roles and permissions, attach permissions to roles. For example, give `Admin` all permissions, give `HR` user-management permissions, and give `User` only basic permissions. |
| `app/Providers/AppServiceProvider.php` | 24-25 | All authorization checks rely on `$user->hasPermission($ability)`. If a user's role does not exist or has no attached permissions, every `@can` and `Gate::authorize()` check fails. | Ensure seeded and created users always receive a role that exists in the `roles` table, and ensure each role has correct permission records attached. |
| `tests/Feature/ExampleTest.php` | 6 | The feature test expects `/` to return HTTP `200`, but the route intentionally redirects guests to login. The actual response is HTTP `302`, so `php artisan test` fails. | Change the test to assert a redirect, for example `$response->assertRedirect(route('login'));`, or test `/login` for HTTP `200` instead. |
| `routes/web.php` | 16 | This route redirects `/` to the login route. This is valid app behavior, but it conflicts with the default Laravel feature test. | Keep the route if this is intended behavior, and update the test expectation to match the redirect. |
| `package.json` | 6 | `npm run build` runs `vite build`, but `node_modules` is missing, so the command fails with `'vite' is not recognized`. | Run `npm install` in the project root, then run `npm run build` again. |
| `database/migrations/2026_04_22_030949_create_roles_table.php` | 20 | The `roles` table has an `is_protected` column, but the controller and views do not consistently use it. | Use `is_protected` as the single source of truth for protected roles instead of hard-coded role names. |
| `app/Http/Controllers/RoleController.php` | 16 | Protected roles are hard-coded as `['admin']`. This duplicates the `is_protected` database field and makes future protected roles harder to manage. | Replace `PROTECTED_ROLES` checks with `$role->is_protected`, and seed `Admin` with `is_protected => true`. |
| `app/Http/Controllers/RoleController.php` | 61-64 | Editing a protected role immediately redirects back to the dashboard, but `resources/views/roles/edit_role.blade.php` contains UI that says protected roles can update color and description. The controller behavior and UI behavior disagree. | Decide the desired behavior. If protected roles should be editable except for name/deletion, allow the edit page and restrict only name/deletion changes. If not editable, remove the unused protected-role edit UI. |
| `app/Http/Controllers/RoleController.php` | 78-82 | Updating a protected role is completely blocked, which conflicts with the edit view message that says color and description can be updated. | Allow partial updates for protected roles or update the UI text to say protected roles cannot be edited. |
| `app/Http/Controllers/RoleController.php` | 114-118 | Deleting a protected role is blocked using a hard-coded name check instead of the `is_protected` database field. | Replace the hard-coded name check with `$role->is_protected`, and validate this behavior with a feature test. |

## Commands Used

| Command | Result |
|---|---|
| `php artisan route:list` | Passed. Routes loaded successfully. |
| `php artisan config:clear` | Passed. Configuration cache cleared. |
| `php artisan view:clear` | Passed. Compiled Blade views cleared. |
| `php artisan test` | Failed. `tests/Feature/ExampleTest.php` expected `200`, received `302`. |
| `npm run build` | Failed. `vite` was not found because `node_modules` is missing. |
| PHP syntax scan on project files | App files parsed cleanly before the scan timed out while continuing through `vendor`. |
