# Noticeboard

The noticeboard app for COMP2245 Tutorial 9. It has five deliberate security
holes. Your job in the tutorial's "Try it" section is to find and fix them,
checking each fix by re-running the attack.

## Setup

1. Clone this repository and `cd` into it.
2. Load the database as root:

   ```sh
   mysql -u root -p < noticeboard.sql
   ```

3. Make the config (`config.php` is gitignored):

   ```sh
   cp config.example.php config.php
   ```

4. Start a server from this folder:

   ```sh
   php -S localhost:8000
   ```

5. Open <http://localhost:8000/> and log in as `ama` / `sunflower` (or
   `devon` / `trinidad99`).

## The holes to find

Search the code for `TODO (security)`.

1. SQL injection in the login query (`index.php`).
2. Passwords stored and compared in plain text (`noticeboard.sql`, `index.php`).
3. Cross-site scripting: notice titles and bodies printed without escaping
   (`index.php`, `notice.php`).
4. Cross-site request forgery: posting and deleting have no token, and delete
   happens on a GET (`index.php`, `delete.php`).
5. Broken access control: `notice.php` shows any notice by id, including
   private ones you don't own, and `delete.php` deletes by id without checking
   the owner.

Reset at any time by running `noticeboard.sql` again. Run this on localhost
only.
