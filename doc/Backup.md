Backup
======

Storage Directory
-----------------

The `data/storage`` directory contains use file uploads.

Create the local directory to store backup fils:

    $ mkdir -p ~/backup/cost-splitting

Run a backup using alpine:

    $ docker run --rm -v ~/backup/cost-splitting:/backup \
      --volumes-from=ddev-cost-splitting-web \
      alpine \
      tar zcvf /backup/csp.tar.gz /var/www/html/data/storage

Database
--------

    $ ddev exec mysqldump \
      --single-transaction \
      --skip-lock-tables \
      db > ~/backup/cost-splitting/csp-db.sql