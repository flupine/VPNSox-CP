<img src="https://i.imgur.com/XPcaiW5.jpg" width="200" />

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c03de63aedd540d0b1ea4266e27e8ba2)](https://www.codacy.com/app/faurest.lupine/VPNSox-CP?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=flupine/VPNSox-CP&amp;utm_campaign=Badge_Grade)

VPNSox-CP The OpenSource VPN Panel
=====================================

What is VPNSox-CP ?
----------------

This is the official sources of vpnsox.org
I know the code is not very optimized (Controllers as example) but i work on a better version of it.
You can contribute to the project and purpose your pull requests.
I provide a complete documentation about the code so if you want, you can easily contribute to the project.

Screenshots : https://photos.app.goo.gl/ceahSKDpmGtzhDVPA

License
-------

VPNSox-CP is released under the terms of the MIT license. See [LICENSE](LICENSE) for more
information or see https://opensource.org/licenses/MIT.

Development Process
-------------------

The `master` branch is regularly built and tested, but is not guaranteed to be
completely stable. [Tags](https://github.com/flupine/JSCOIN/tags) are created
regularly to indicate new official, stable release versions of JSCOIN.

The contribution workflow is described in [CONTRIBUTING.md](CONTRIBUTING.md).

How to use
-----------

Requirements:
- APACHE2
- At least php 5.6
- Mysql/MariaDB

Steps:
1) Create database 
2) Import sql file to your database
3) Clone files
```
git clone https://github.com/flupine/VPNSox-CP.git
cd VPNSox-CP
composer install
```
4) Configure your apache2 to point to public/ folder
5) fill your credentials in /app/container.php
6) configure a 2nd database for openvpn clients auth and import openvpn.sql inside it.
7) Connect your vpn servers slaves to your openvpn remote database.
8) Thats it !


