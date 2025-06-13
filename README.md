
# Librebook
Librebook the free, secure, not selling your data social media solution made by the therandomspoon because they were bored.

***Privacy matters. So use Librebook***

[![License](https://img.shields.io/github/license/therandomspoon/librebook?label=License&color=brightgreen&cacheSeconds=3600)](./LICENSE)
[![Release](https://img.shields.io/github/v/release/therandomspoon/librebook?label=Release&color=brightgreen&cacheSeconds=3600)](https://github.com/therandomspoon/librebook/releases/latest)
[![Commits](https://img.shields.io/github/commit-activity/y/therandomspoon/librebook?color=red&label=commits)](https://github.com/therandomspoon/librebook/commits)
[![Issues](https://img.shields.io/github/issues/therandomspoon/librebook?color=important)](https://github.com/therandomspoon/librebook/issues)
[![Pull Requests](https://img.shields.io/github/issues-pr/therandomspoon/librebook?color=blueviolet)](https://github.com/therandomspoon/librebook/pulls)

![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/bannerbook.png)

## Developers
- Lead developer: Therandomspoon

- Bing AI: Contributed a lot of the login code as i have no idea what im doing. Also made the code SQL and HTML injection proof
## Major Contributors
Golddominik893 - i intended to steal a lot of code from Golddominik893 for the login system but i got confused cos im a bit stupid.
<br>
<br>
Bing AI - Surprisingly good and free image generation. Contributed Leo the lion (mascot of librebook)
First version (genesis) finished on the 22/12/2023 and released on github.

# Components required
- Mysql server
- Web server
- PHP

## Run these commands to be able to host it (debian linux tested)
- sudo apt-get install php-mysql
- sudo apt install apache2 / sudo apt install nginx
- sudo apt-get install php
- (we recommend mariaDB) - sudo apt install mariadb-server
- sudo apt install git

## SETUP COMMANDS FOR LINUX (DEBIAN/UBUNTU)
1. cd /var/www/html/ (for apache)
2. sudo git clone https://github.com/therandomspoon/librebook.git
3. cd /var/www/html/librebook
4. sudo mv * /var/www/html/
5. sudo mariadb (or whatever command to open your sql server)
6. CREATE DATABASE dbnamehere;
7. USE dbnamehere;
8. SOURCE /var/www/html/tables.sql;
9. sudo nano /var/www/html/config.php (or whatever text editor you use)
10. Enter your sql server details
11. Done!

> [!NOTE]
> Please note that some features from the version we host will not be on the github immediately as it is a pre-alpha build

# Features
- Global messaging system
- custom profiles (pfp, bio, not user as of this time)
- looking up others profiles
- secure login system
- messages only sent by user shows on profile inspection
- protection against sql and html injection
- photo posts 422 × 296 px
- folders for the files organisation cos Golddominik893 asked
- Instance hosted by me (http://librebook.co.uk/)
- Deleting accounts
- friending system
- p2p messaging systen (direct messages)
- 
# Upcoming features
- reactions 

# Screenshots (genesis ver)
## landing page
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/indexpage.png)

## messaging page
(yours will not have these specific images because they are all on my sql server)
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/mainpageexample.png)

## editing profiles
(this is my one not everyones)
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/customprofilesexample.png)

## viewing others profiles
(even shows the messages that only they sent!)
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/searchingupothersprofiles.png)

# A photo of Leo the lion the mascot of Librebook created by Bing AI
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/leo.png)

# Librebook is coconutified!
![image](https://github.com/therandomspoon/librebook/blob/main/screenshots/coconutted.png)

<hr>

# Welcome to Librebook !
Librebook is the free, intended to be private social media made by the solo developer Therandomspoon. Librebook is intended to be open source and to exist for others to expand on and host while enjoying their data not being sold.</p>
## Developers
- Lead developer: Therandomspoon
- Bing AI: Contributed a lot of the login code as i have no idea what im doing. Also made the code SQL and HTML injection proof
## Major Contributors
Golddominik893 - i intended to steal a lot of code from Golddominik893 for the login system but i got confused cos im a bit stupid.
Bing AI - Surprisingly good and free image generation. Contributed Leo the lion (mascot of librebook)
First version (genesis) finished on the 22/12/2023 and released on github.
