# Read Me

[![buddy pipeline](https://app.buddy.works/errigal/twocoda/pipelines/pipeline/207832/badge.svg?token=89fd8ff745c68da4c6eec1076cb49523425e6948907cb85a711961313124807d "buddy pipeline")](https://app.buddy.works/errigal/twocoda/pipelines/pipeline/207832)

## Overview
"TwoCoda" is a passion project of mine that I've been kicking around for a bit, and I've now
been able to start on it. Being fairly involved in a music scene, I've come to know a lot of 
people who teach. I noticed a lot of really common complaints and pain points that they all 
shared. I figured that I could a) offer to individually help them out with their web presences
or b) make a "one size fits all solution" that they could just sign up for and go nuts. 

What I wound up doing was setting up a WordPress multisite network with WP Ultimo to allow
registration and subscription services. Essentially, music teachers would get a turnkey
solution for their website with everything they needed. A lot of this was achievable with
off-the-shelf software, with the notable exceptions of bookings and payments.

TwoCoda consists of:

- **A Booking Module.** This is the most mature part of the project so far. Students can be 
added and lessons can be booked. This is the major part of the project that's being written
from scratch. See: `plugins/errigal-booking-module`
- **A Payments Module.** I'm ready to go via Stripe Connect Express, so onboarding will be 
super simple. The integration is probably the next coding challenge I'm going to tackle after 
booking is complete.
- **Student Management.** This is mostly some re-arranging of native WordPress functionality
to suit my purposes. I created two new roles: `teacher` and `student`, and applied capabilities
accordingly.
- **Business Policy Management** A major problem with music teachers is setting and enforcing
scheduling, cancellation policies, and things like that. This was quickly achievable by setting up an Advanced Custom Fields
options page, which also allows them to specify lesson lengths, costs, etc.

## The Booking Module
The booking module is a discrete plugin located in `plugins/errigal-booking-module`. (So-called because
my little freelance gig is called Errigal Media.) I was diligently trying to find an off-the-shelf
solution that I could white-label for student-teacher booking, but I didn't find anything that would 
meet my requirements. So I decided to the best approach would be to write my own.

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
a fan of the Laravel framework, so this has been an absolutely pleasure to work with.


## Task and Issue Tracker

I'm keeping track of epics, module development, and issues here:

https://trello.com/b/sMXEhPOG/twocoda-build

## Live Demo
There is a live demo that's on the upstream `master` branch. 

**URL** - https://demo.twocoda.com/login

**User** - demo 

**Password:** 2CodaD3m0