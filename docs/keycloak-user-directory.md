# Keycloak user directory

`/admin/users` is the existing dashboard menu for Super Admin, Admin Pusat and Admin. It now reads paginated/searchable users from Keycloak, including identities that have never signed in locally. It does not auto-link or create local accounts by email. Edits use Keycloak's immutable user ID; firstName, lastName, email and username are editable. Password changes use a separate form/endpoint with confirmation. Role and enabled state are not editable in this stage.

Hierarchy: Super Admin can edit others; Admin Pusat can edit Admin and non-admin roles; Admin can edit non-admin roles. Self editing is blocked. The higher privilege of the remote effective role or linked local role is used. Effective realm roles and effective roles of KEYCLOAK_CLIENT_ID are read; unrelated client roles are excluded. List access is available to all three admins. Failure to read roles fails closed.

## Setup

Use the existing services.keycloak configuration (KEYCLOAK_BASE_URL, KEYCLOAK_REALM, KEYCLOAK_CLIENT_ID, KEYCLOAK_CLIENT_SECRET). Enable service accounts for the confidential client. Configure realm-management permissions allowing user search/view/update and viewing the configured client and effective role mappings. Standard roles commonly used are query-users, view-users, manage-users and view-clients; use finer permissions if configured. Never put the secret or admin token in frontend code.

Run `php artisan migrate --force` before opening the page. No external user is changed by installation or listing. User edits are explicit admin actions. Existing linked local edit/delete endpoints are blocked or redirected to prevent bypass of the new workflow; creating and deleting remote identities are outside this stage.

## Synchronization

Persist a sync intent before calling Keycloak. A successful remote update is followed by a transaction updating the linked local user's name/email/verification status and marking the intent synced. Unlinked users stay unlinked. Changing email resets emailVerified remotely and email_verified_at locally. Passwords, roles, profile pictures, articles and forum relationships remain under their existing workflows. Keycloak 409/400 failures show validation messages, authorization/outage errors show generic notices; no response body or token is exposed.

Two systems cannot share one atomic transaction. Timeouts, server errors and local-write failures are recorded as reconciliation_required. The UI never reports full success for them. Run `php artisan keycloak:reconcile-users` after the underlying problem is resolved. It checks current remote identity fields before applying the local update; newer/conflicting remote data is marked requires_review instead of overwritten. Pending intents older than five minutes are also checked for interrupted processes. This command is manual in this stage. Inspect keycloak_user_syncs for record IDs/status; before and after snapshots contain identity data and require database access controls/retention policy.

AuditLog records actor, target Keycloak ID, before/after, outcome and sync ID. No password, credentials or admin token is recorded. Audit dashboard work remains separate.

API reference: https://www.keycloak.org/docs-api/latest/rest-api/index.html

Tests use Http::fake and temporary SQLite, never real Keycloak writes. A live smoke check requires a running Keycloak instance and the service-account permissions above.


## Username and password extension

Run migrations for the nullable unique local username and password sync tracking. Username edits are confirmed by reading the Keycloak representation back before local sync. The Keycloak realm must allow admin username edits; email-as-username realms may impose their own policy. Conflicts and policy rejection are shown as errors.

The table uses the linked local foto_profil and falls back to initials; it does not invent Keycloak avatars or link accounts by email.

Password reset calls the Keycloak reset-password endpoint with temporary=false. Plaintext passwords are neither stored nor audited. A hash is temporarily encrypted in the sync intent so a confirmed remote reset can be completed locally after a database failure. The encrypted hash is cleared on completion or rejection. Unknown/time-out remote results require manual review; recovery never assumes the password was changed. Older confirmed password intents cannot overwrite a newer confirmed reset. Successful local password sync revokes Sanctum tokens and database-backed local sessions. This does not claim to revoke every Keycloak SSO session.
