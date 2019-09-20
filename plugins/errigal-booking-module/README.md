# Errigal Booking Module #
**Contributors:**      Chris Wieber  
**Donate link:**       http://www.errigal.media  
**Tags:**  
**Requires at least:** 4.4  
**Tested up to:**      5.2.3
**Stable tag:**        0.0.0  
**License:**           GPL v2  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

 I was diligently trying to find an off-the-shelf
solution that I could white-label for student-teacher booking, but I didn't find anything that would 
meet my requirements. So I decided to the best approach would be to write my own.

### Manual Installation ###

1. Upload the entire `/errigal-booking-module` directory to the `/wp-content/plugins/` directory.
2. Activate Errigal Booking Module through the 'Plugins' menu in WordPress.

### Foundations
The module is built on WebDevStudio's excellent [Yeoman WordPress Plugin Generator](https://github.com/WebDevStudios/generator-plugin-wp),
which I used extensively when I was with the company. It scaffolds out just about everything you need to get going 
using a singleton and service container-type architecture, including activation/deactivation hooks,
an autoloader (composer or vanilla PSR-4), and a number of subgenerators to generate common boilerplate.

The new classes generated show up in the `includes` directory within the plugin root directory.

### Modularity
For this whole project, I'm taking a modular approach so that I can reuse elements in other projects.
This module is no exception, and so at lower-level orders of function (models, database, etc), you'll see
many things genericized, such as "appointment" instead of "lesson". As we move up to controllers, UI,
etc., we get more specific in calling things "lessons."

### Database and Schema
This plugin adds two tables to the WordPress database: `appointments` and `appointment_types`. `appointments`
holds all of the information about appointments themselves, and `appointment_types` is a reference table
that holds an appointment type ("regular", "blocked_time", "business_hours", etc) and ties it to a unique ID.


Not wanting to lean too heavily on the $wpdb global, and wanting additional functionality for complex queries
and CRUD actions, I decided to try out Tareq88's great implementation of the Eloquent ORM for WordPress. I'm
a fan of the Laravel framework, so this has been an absolute pleasure to work with.

## Changelog ##

### 0.0.0 ###
* First release

## Upgrade Notice ##

### 0.0.0 ###
First Release
