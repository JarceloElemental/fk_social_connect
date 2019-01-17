## Social Connect
free plugin for [osclass](https://osclass.org/)  php based platform which allows you to quickly create and manage your own free classifieds site.
___
#### Description
Social connect plugin allows users to easily login to your osclass web page using their Facebook or Google accounts.
So they don't have to sign up, go through the process of filling up forms.

##### Examples: 
Facebook and Google login buttons

![screenshot](https://www.dropbox.com/s/ho3pjzqcayj7ac6/social-connect-1.jpg?raw=1)
___
Google OAuth 

![screenshot](https://www.dropbox.com/s/sdfw9uhpd6falrn/social-connect-2.jpg?raw=1)
___
Facebook authentication

![screenshot](https://www.dropbox.com/s/741t7okvcw0glw4/social-connect-3.jpg?raw=1)

___
#### Installation
Simply clone the repo, copy fk_social_connect folder to your osclass  plugins folder
```
Path:
{your-path}/oc-content/plugins
```
go to admin panel -> manage plugins -> there your find Social Connect -> click on install.
___
#### Configuration
To use Social connect, you need to get your `APP ID` and `APP Secret` keys
which are needed to use Facebook or Google login functionality,
follow the instructions on these links:

Facebook
https://developers.facebook.com/docs/apps/

Google
https://console.developers.google.com/apis/credentials/

```
http://{Your Domian}/index.php?page=custom&route=google-redirect
```
once you have the `APP ID` and `APP SECRET` keys, go to Social connect config page add the keys click save, and enjoy ;)

![screenshot](https://www.dropbox.com/s/b738cu96kjoutk8/social-connect-4.jpg?raw=1)
___
#### Usage
Simply add the following code to your web page where you want login buttons to appear. e.g
in `user-login.php`

```
 // To include facebook and google login buttons
 <?php sc_facebook_login_button(); ?>
<?php sc_google_login_button(); ?>

//or just the urls
<?php sc_facebook_login_url(); ?>
<?php sc_google_login_url(); ?>
```