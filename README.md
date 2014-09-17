EnjoyInstagram
==============

WordPress plugin used to retrieve and store Instagram posts

###How to use

Install the plugin by copying 'instagram.php' to wp-content/plugins.


###Setup the plugin

Login to the WordPress admin, enable the plugin and go to settings -> Enjoy Instagram.

Here you will need an access code, instructions: http://jelled.com/instagram/access-token

The User ID is the ID of the person whose feed you want to use.

###Include the shortcode

```
[enjoy-instagram]
```

Or you can use it directly in the theme

```php
<?=do_shortcode('[enjoy-instagram]')?>
```
