=== Simple Feature Requests Free ===
Contributors: jamesckemp
Donate link:
Tags: features, feature request, feature requests, uservoice, vote, votes, user voice, community, feedback
Requires at least: 4.0
Tested up to: 5.1
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Prioritise feature requests for your product through user generated content.

== Description ==

If you are a product creator then you'll know how important user feedback can be. Simple Feature Requests aims to make it easier for you to prioritise which features should be included.

The free version of the plugin provides the following features:

* Accept user-submitted feature requests.
* Users can vote on features they want to see included in your product.
* Users can comment on feature requests using the native WordPress commenting system.
* Admins can approve and manage feature requests.
* AJAX based search before a user submits a feature request.
* Sort/filter by "Latest", "Top", "My Requests", and "Status".
* Select status from "Pending", "Published", "Under Review", "Complete", "Started", "Planned", and "Declined".
* Add custom statuses using the `jck_sfr_statuses` filter.

= Pro Version =

The [Pro version](https://simplefeaturerequests.com) of the plugin provides the following *additional* features:

* Categorise feature requests.
* Bulk change request statuses.
* Enable email notifications when a status changes or a comment is added.

[Upgrade to Pro](https://simplefeaturerequests.com)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-feature-requests` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Once activated, go to Settings > Permalinks. Visiting this page will refresh your sites permalinks.
1. You will now need to add a link to the requests archive. This can be found at `https://yourwebsite.com/feature-requests/`.

== Frequently Asked Questions ==

= Is there a Pro version of the plugin? =

Yes, there is! You can get it from the [website](https://simplefeaturerequests.com). With the Pro version you can:

* Categorise feature requests.
* Bulk change request statuses.
* Enable email notifications when a status changes or comment is added.

= I'm getting a 404 error when loading a request or the requests archive. =

You need to refresh your permalinks. Go to Settings > Permalinks. Just visiting that settings page will refresh the permalinks and the feature requests should now load on the frontend.

== Screenshots ==

== Changelog ==

**v1.0.5** (2019-03-02)
[fix] Security Fix

**v1.0.4** (2019-01-04)
[new] Pro - Categories
[new] Pro - Bulk update statuses
[new] Pro - Email notifications
[new] Sidebar widgets
[update] Update dependencies
[fix] Fix single request visibility when completed or declined

**v1.0.3** (2018-07-05)
[update] Ignore "status" and "filter" when searching as they can't be changed anyway
[fix] Remove search when filtering or changing status
[fix] Keep status when filtering
[fix] Show all statuses when searching
[fix] Ajax search was returning all results

**v1.0.2** (2018-04-25)
[update] Add custom statuses for feature requests
[update] Update notices class to use transients
[update] Add notice on successful submission
[update] Filter by status
[update] Update Freemius
[update] Update settings framework
[fix] Fix feature request height when no content
[fix] Fix return value error for older PHP versions

**v1.0.1** (2017-12-14)
[fix] Add before/after hooks to single template.

**v1.0.0** (2017-12-13)
Initial release.

== Upgrade Notice ==