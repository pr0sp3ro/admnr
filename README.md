# admnr

Adminer 6.1.2 with a custom theme and plugins for browsing, editing, and exporting database content.

Serve `index.php` with PHP and the database extensions you need. Create a private `config.local.php` returning an array with `permanent_login_key` (a stable secret for saved logins) and `file_upload_path` (a writable upload directory). This file is excluded from Git.

## Plugins

| Plugin | Purpose |
| --- | --- |
| `AdminerLocalLogin` | Uses the configured key for permanent logins. Defined in `adminer-plugins.php`. |
| `ai-md-export` | Copies query results and execution details as Markdown. |
| `backward-keys` | Adds navigation through incoming foreign key references. |
| `column-comment` | Displays column comments in result headers. |
| `column-header-button` | Adds shortcuts for distinct column values. |
| `column-hider` | Toggles column visibility and remembers it per table in the browser. |
| `copy` | Copies cell values with a right click. |
| `copy-structure-export` | Copies or exports table structures as Markdown, JSON, text, CSV, or SQL. |
| `db-structure` | Shows database structure on the SQL command page. |
| `dump-alter` | Exports schema changes for synchronizing databases. |
| `dump-date` | Adds the current date and time to export filenames. |
| `dump-json` | Exports data as JSON. |
| `dump-markdown` | Exports database structure and data as Markdown. |
| `dump-zip` | Compresses exports as ZIP files. |
| `dynamic-sidebar` | Resizes the sidebar and remembers its width. |
| `edit-foreign-search` | Searches referenced records when editing foreign keys. |
| `edit-textarea` | Uses textareas for character fields. |
| `editor-views` | Includes views in Adminer Editor. |
| `elastic` | Adds the Elasticsearch driver. |
| `enum-option` | Uses dropdowns for existing enum fields. |
| `favorite-query` | Saves reusable SQL queries in browser storage by default. |
| `file-upload` | Uploads files through fields ending in `_path`. |
| `foreign-system` | Links related system tables. |
| `foreign-tooltip` | Previews referenced records on hover. |
| `format-sql` | Formats SQL using the bundled sql-formatter 15.8.2. |
| `highlight-codemirror` | Adds CodeMirror highlighting and SQL completion. Loads assets from a CDN. |
| `human-readable-size` | Displays storage sizes in readable units. |
| `input-uuid-generator` | Adds UUID generation to matching fields. |
| `json-preview` | Opens formatted JSON in a modal without expanding table rows. |
| `login-password-less` | Allows passwordless database login through a configured password hash. |
| `pretty-json-column` | Formats JSON for editing and validates it before form submission. |
| `readable-dates` | Displays selected UNIX timestamps as local dates. Click to toggle the raw value. |
| `redis` | Adds the Redis driver. |
| `remote-color` | Marks remote connections with a colored strip. |
| `search-autocomplete` | Suggests filter values using the other active filters. |
| `select-foreign` | Displays referenced text values instead of foreign key IDs. |
| `sql-log` | Logs SQL queries to a file, using the temporary directory by default. |
| `table-groups` | Organizes sidebar tables into custom groups saved in the browser. |
| `table-indexes-structure` | Expands index information in the structure view. |
| `table-structure` | Expands table structure information. |
| `tables-filter` | Filters the sidebar table list. |
| `tinymce` | Edits text fields containing `_html` with the bundled TinyMCE editor. |
| `tree-view` | Browses records through foreign key relationships as a tree. |
| `version-github` | Checks GitHub for Adminer updates. |

## Local customizations

- Filter autocomplete includes a JavaScript syntax fix, CSRF validation, column checks, and a 1,000-value limit.
- Timestamp conversion requires an integer column named `at`, `time`, or `timestamp`, or ending in `_at`, `_time`, or `_timestamp`, with a ten-digit value. Linked values are left unchanged.
- JSON editing supports empty objects and arrays, `jsonb`, and leading whitespace. Formatting preserves object structure and falls back to the original text if number spelling or precision would change. Browser validation reports invalid JSON before submission.
- JSON previews use a compact button and a scrollable modal, preserving large integers without rounding. The redundant `json-column` plugin has been removed.
- SQL formatting uses local assets.

Column visibility, table groups, sidebar width, and default query favorites are stored in the current browser. Hiding columns changes presentation only.

## License

Original configuration, documentation, and modifications in this project are licensed under the [MIT License](LICENSE), copyright 2026 [Synframe Labs](https://synframe.eu).

Adminer 6.1.2 is available under Apache-2.0 or GPL-2.0, as stated in its source header. Bundled third-party plugins, libraries, and theme code retain their respective licenses and copyright notices. The MIT License above applies only to this project's original contributions and does not replace third-party licenses. The bundled sql-formatter license is included in [adminer-plugins/assets/sql-formatter.LICENSE](adminer-plugins/assets/sql-formatter.LICENSE).
