=== Content Freeze ===
Contributors: wholegraindigital
Requires at least: 3.9.2
Tested up to: 4.2
Stable tag: 0.1
Tags: content freeze, content lock, code lock, maintenance, migration
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
The Content Freeze plugin allows an admin to 'lock' all content on the site to prevent the site being edited.

== Description ==

Need to stop your clients and users from editing a site while you do maintenance or a server migration?  

The content freeze plugin solves the problem of telling the client "Please don't edit anything on the site today because your changes may be lost" and then finding out after the event that they spent the whole day making changes to the site. You want to keep your clients happy, and restricting access during maintenance and migrations helps avoid confusion and inconvenience for all involved. 

The content freeze plugin allows an admin to 'lock' the site to prevent the anything being edited.  Users can still visit the site, but no one can make changes behind the scenes without your permission.  

It is very simple to use.  When installed, an admin can switch the content freeze on.  At that point, only the admin who activated the content freeze can log in and all other users (including other admins) are locked out temporarily.  When they try to log in they will see a message teling them that the site is currently frozen and they therefore cannot login at this time.
We plan to add some more features in the near future, including scheduling and also optimising it for use on multisite networks.  Contributions welcome.

== Installation ==

The content freeze plugin is really plug and play. Just upload the plugin and activate.
1. Upload the contents of the package to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
To start/stop a content freeze, go to Settings > Content Freeze and click the Freeze Content / Unfreeze Content button.


== Frequently Asked Questions ==

= Will it work with all themes? =

Yes, it should work fine with all themes.  If you find a problem, let us know.

= Does it work on multisite installations? =

Yes it does, but you have to activate the freeze on each individual site within your network. Once activated, you not to visit the wp-admin url to access any individual sub-sites that are not locked (wp-login.php urls won't work during the freeze).  

In the future we hope to add a network freeze option.

= Can I schedule a content freeze? =

Not at this time, but we hope to add that feature soon.

= Does it guarantee that no changes can be made to my site during a content freeze? =

Not 100%.  The only person that could make any changes to the site from inside the CMS is the person who is in control of the content freeze.  It is generally safe to assume that this person will respect the content freeze and not make any changes.  It is also possible for people to leave comments on your site, but we will soon add a comment lock to prevent this.



== Screenshots ==


== Changelog ==

= 0.1 =

* Initial version
