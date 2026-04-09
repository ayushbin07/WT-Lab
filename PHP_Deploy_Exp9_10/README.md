# PHP Deploy Folder (Exp9 + Exp10)

Upload all files from this folder to one InfinityFree hosting account.

## Files in this folder
- index.php: landing page with links to both experiments
- exp9.php: scientific calculator with history (session-based)
- exp10.php: form save/display using MySQL
- style.css: shared styles
- config.php: database credentials (edit this)
- database_setup.sql: SQL for table creation

## Setup
1. In InfinityFree, create a MySQL database from control panel.
2. Edit config.php with your DB host/user/password/dbname.
3. Open phpMyAdmin and run database_setup.sql.
4. Upload all files to public_html.

## Redirect pages in local project
Keep only redirect index.html in:
- Exp9/index.html
- Exp10/index.html

Set your live URLs there after deployment.
