<?php
global $epanelMainTabs, $themename, $shortname, $options;

$epanelMainTabs = array('general','navigation','layout','ad','seo','integration','support','updates');

$cats_array = get_categories('hide_empty=0');
$pages_array = get_pages('hide_empty=0');
$pages_number = count($pages_array);

$site_pages = array();
$site_cats = array();
$pages_ids = array();
$cats_ids = array();

foreach ($pages_array as $pagg) {
	$site_pages[$pagg->ID] = htmlspecialchars($pagg->post_title);
	$pages_ids[] = $pagg->ID;
}

foreach ($cats_array as $categs) {
	$site_cats[$categs->cat_ID] = $categs->cat_name;
	$cats_ids[] = $categs->cat_ID;
}

$shortname 	= esc_html( $shortname );
$pages_ids 	= array_map( 'intval', $pages_ids );
$cats_ids 	= array_map( 'intval', $cats_ids );

$options = array (

	array( "name" => "wrap-general",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "general-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("General",$themename)),

			array( "name" => "general-2",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Homepage",$themename)),

			array( "name" => "general-3",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Featured Slider",$themename)),

		array( "type" => "subnavtab-end",),

		array( "name" => "general-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Logo",$themename),
				   "id" => $shortname."_logo",
				   "type" => "upload",
				   "std" => "",
				   "desc" => esc_html__("If you would like to use your own custom logo image click the Upload Image button.",$themename)
			),

			array( "name" => esc_html__("Favicon",$themename),
				   "id" => $shortname."_favicon",
				   "type" => "upload",
				   "std" => "",
				   "desc" => esc_html__("If you would like to use your own custom favicon image click the Upload Image button.",$themename)
			),

			array( "name" => esc_html__("Header Section Custom Background",$themename),
				   "id" => $shortname."_header_bg_image",
				   "type" => "upload",
				   "std" => "",
				   "desc" => esc_html__("If you would like to use your own background image for the Header section click the Upload Image button.",$themename)
			),

			array( "name" => esc_html__("Grab the first post image",$themename),
				   "id" => $shortname."_grab_image",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default thumbnail images are created using custom fields. However, if you would rather use the images that are already in your post for your thumbnail (and bypass using custom fields) you can activate this option. Once activcated thumbnail images will be generated automatically using the first image in your post. The image must be hosted on your own server.",$themename)
			),

			array( "name" => esc_html__("Use site name instead of the logo image",$themename),
				   "id" => $shortname."_use_site_name",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("Site name is used for the logo by default, if you want to use the Logo Image set above, you need to disable this option.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Blog Style Mode",$themename),
				   "id" => $shortname."_blog_style",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default the theme truncates your posts on index/homepages automatically to create post previews. If you would rather show your posts in full on index pages like a traditional blog then you can activate this feature.",$themename),
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Call To Action Button Text",$themename),
				   "id" => $shortname."_action_button_text",
				   "std" => "",
				   "type" => "text",
				   "desc" => esc_html__("Here you can define the Call To Action Button Text.",$themename),
				   "validation_type" => "nohtml"
			),

			array( "name" => esc_html__("Call To Action Button Url",$themename),
				   "id" => $shortname."_action_button_url",
				   "std" => "",
				   "type" => "text",
				   "desc" => esc_html__("Here you can define the Call To Action Button Url.",$themename),
				   "validation_type" => "url"
			),

			array( "name" => esc_html__("Number of Posts displayed on Category page",$themename),
				   "id" => $shortname."_catnum_posts",
				   "std" => "6",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many recent articles are displayed on the Category page. This option works independently from the Settings > Reading options in wp-admin.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Number of Posts displayed on Archive pages",$themename),
				   "id" => $shortname."_archivenum_posts",
				   "std" => "5",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many recent articles are displayed on the Archive pages. This option works independently from the Settings > Reading options in wp-admin.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Number of Posts displayed on Search pages",$themename),
				   "id" => $shortname."_searchnum_posts",
				   "std" => "5",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many recent articles are displayed on the Search results pages. This option works independently from the Settings > Reading options in wp-admin.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Number of Posts displayed on Tag pages",$themename),
				   "id" => $shortname."_tagnum_posts",
				   "std" => "5",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many recent articles are displayed on the Tag pages. This option works independently from the Settings > Reading options in wp-admin.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Date format",$themename),
				   "id" => $shortname."_date_format",
				   "std" => "M j, Y",
				   "type" => "text",
				   "desc" => __("This option allows you to change how your dates are displayed. For more information please refer to the WordPress codex here:<a href='http://codex.wordpress.org/Formatting_Date_and_Time' target='_blank'>Formatting Date and Time</a>",$themename),
				   "validation_type" => "nohtml"
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Use excerpts when defined",$themename),
				   "id" => $shortname."_use_excerpt",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("This will enable the use of excerpts in posts or pages.",$themename)
			),

			array( "name" => esc_html__("Responsive shortcodes",$themename),
				   "id" => $shortname."_responsive_shortcodes",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("Enable this option to make shortcodes respond to various screen sizes",$themename)
			),

			array( "type" => "clearfix",),

			array(
				"name"              => esc_html__( "Use Google Fonts", $themename ),
				"id"                => "et_use_google_fonts",
				"main_setting_name" => "et_google_api_settings",
				"sub_setting_name"  => 'use_google_fonts',
				'is_global'         => true,
				"type"              => "checkbox",
				"std"               => "on",
				"desc"              => esc_html__( "Disable this option to remove the Google Fonts from your Divi Builder Pages.", $themename ),
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Google Fonts subsets",$themename),
				   "id" => $shortname."_gf_enable_all_character_sets",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("This will enable Google Fonts for Non-English languages.",$themename)
			),

			array( "name" => esc_html__("Animate elements on scroll",$themename),
				   "id" => $shortname."_animations_on_scroll",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("This will enable animations that happen as you scroll the page.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__( "Custom CSS", $themename ),
				   "id" => $shortname . "_custom_css",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__( "Here you can add custom css to override or extend default styles.", $themename ),
					"validation_type" => "nohtml"
			),

		array( "name" => "general-1",
			   "type" => "subcontent-end",),

		array( "name" => "general-2",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Display Projects Section",$themename),
				   "id" => $shortname."_show_projects",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("Here you can choose to display the Projects section on the homepage. ",$themename)
			),

			array( "name" => esc_html__("Display Testimonials Section",$themename),
				   "id" => $shortname."_show_testimonials",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("Here you can choose to display the Testimonials Section on the homepage. ",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Display Team Section",$themename),
				   "id" => $shortname."_show_team",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("Here you can choose to display the Team Section on the homepage. ",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Projects section title",$themename),
				   "id" => $shortname."_home_projects_title",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Projects section title on homepage.",$themename)
			),

			array( "name" => esc_html__("Projects section description",$themename),
				   "id" => $shortname."_home_projects_description",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Projects section description on homepage.",$themename)
			),

			array( "name" => esc_html__("Featured slider section title",$themename),
				   "id" => $shortname."_home_featured_title",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Featured slider section title on homepage.",$themename)
			),

			array( "name" => esc_html__("Featured slider section description",$themename),
				   "id" => $shortname."_home_featured_description",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Featured slider section description on homepage.",$themename)
			),

			array( "name" => esc_html__("Testimonials section title",$themename),
				   "id" => $shortname."_home_testimonials_title",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Testimonials section title on homepage.",$themename)
			),

			array( "name" => esc_html__("Testimonials section description",$themename),
				   "id" => $shortname."_home_testimonials_description",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Testimonials section description on homepage.",$themename)
			),

			array( "name" => esc_html__("Team section title",$themename),
				   "id" => $shortname."_home_team_title",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Team section title on homepage.",$themename)
			),

			array( "name" => esc_html__("Team section description",$themename),
				   "id" => $shortname."_home_team_description",
				   "std" => '',
				   "type" => "text",
				   "validation_type" => "nohtml",
				   "desc" => esc_html__("Here you can define Team section description on homepage.",$themename)
			),

			array( "name" => esc_html__("Number of Projects displayed on homepage",$themename),
				   "id" => $shortname."_home_projects_num",
				   "std" => "8",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many projects are displayed on the homepage.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Number of Posts displayed on homepage in Blog Style Mode", $themename),
				   "id" => $shortname."_homepage_posts",
				   "std" => "8",
				   "type" => "text",
				   "desc" => esc_html__("Here you can designate how many recent articles are displayed on the homepage, if the Blog Style Mode option is activated. This option works independently from the Settings > Reading options in wp-admin.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Exclude categories from homepage recent posts in Blog Style Mode",$themename),
				   "id" => $shortname."_exlcats_recent",
				   "type" => "checkboxes",
				   "std" => "",
				   "desc" => esc_html__("By default the homepage displays a list of all of your most recent posts, if the Blog Style Mode option is activated. However, if you would like to exlcude certain category from the list you can do so here. ",$themename),
				   "usefor" => "categories",
				   "options" => $cats_ids
			),

		array( "name" => "general-2",
			   "type" => "subcontent-end",),

		array( "name" => "general-3",
				   "type" => "subcontent-start",),

			array( "name" => esc_html__("Display Featured Slider",$themename),
				   "id" => $shortname."_featured",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("You can choose whether or not to display the Featured Articles section on the homepge. If you don't want to utilize this feature simply disable this option.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Number of Featured Slides",$themename),
				   "id" => $shortname."_featured_num",
				   "std" => "3",
				   "type" => "text",
				   "desc" => esc_html__("This setting controls how menu tabs are added to the Featured Articles slider on the homepage.",$themename),
				   "validation_type" => "number"
			),

			array( "name" => esc_html__("Featured Posts Category",$themename),
				   "id" => $shortname."_feat_posts_cat",
				   "type" => "select",
				   "options" => $site_cats,
				   "desc" => esc_html__("Here you can choose which category to be used to populate the Featured Slider on the homepage. This only applies if you have disabled the Use Pages options below.",$themename),
				   "et_array_for" => "categories"
			),

			array( "name" => esc_html__("Use pages",$themename),
				   "id" => $shortname."_use_pages",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("The homepage Featured Slider can be set up using two methods. You can populate the slider using Posts, or you can populate it using Pages. If you would like to use Pages in the Featured Slider then enable this option.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Include pages in the Featured Slider (if Use Pages enabled)",$themename),
				   "id" => $shortname."_feat_pages",
				   "type" => "checkboxes",
				   "std" => '',
				   "desc" => esc_html__("If you selected Use Pages above, then use the checkboxes below to choose which pages are displayed in the Featured Slider.",$themename),
				   "usefor" => "pages",
				   "excludeDefault" => "true",
				   "options" => $pages_ids),

			array( "name" => esc_html__("Automatic Slider Animation",$themename),
				   "id" => $shortname."_slider_auto",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("If you would like the Featured Articles slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Automatic Animation Speed (in ms)",$themename),
				   "id" => $shortname."_slider_autospeed",
				   "type" => "text",
				   "std" => "7000",
				   "desc" => esc_html__("Here you can designate how fast the slider fades between each article. The higher the number the longer the pause between each rotation.",$themename),
				   "validation_type" => "number"
			),

		array( "name" => "general-3",
			   "type" => "subcontent-end",),

	array(  "name" => "wrap-general",
			"type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "wrap-navigation",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "navigation-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Pages",$themename)
			),

			array( "name" => "navigation-2",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Categories",$themename)
			),

			array( "name" => "navigation-3",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("General Settings",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "navigation-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Exclude pages from the navigation bar",$themename),
				   "id" => $shortname."_menupages",
				   "type" => "checkboxes",
				   "std" => "",
				   "desc" => esc_html__("Here you can choose to remove certain pages from the navigation menu. All pages marked with an X will not appear in your navigation bar. ",$themename),
				   "usefor" => "pages",
				   "options" => $pages_ids),

			array( "name" => esc_html__("Show dropdown menus",$themename),
			"id" => $shortname."_enable_dropdowns",
			"type" => "checkbox",
			"std" => "on",
			"desc" => esc_html__("If you would like to remove the dropdown menus from the pages navigation bar disable this feature.",$themename)
			),

			array( "name" => esc_html__("Display Home link",$themename),
			"id" => $shortname."_home_link",
			"type" => "checkbox2",
			"std" => "on",
			"desc" => esc_html__("By default the theme creates a Home link that, when clicked, leads back to your blog's homepage. If, however, you are using a static homepage and have already created a page called Home to use, this will result in a duplicate link. In this case you should disable this feature to remove the link.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Sort Pages Links",$themename),
				   "id" => $shortname."_sort_pages",
				   "type" => "select",
				   "std" => "post_title",
				   "desc" => esc_html__("Here you can choose to sort your pages links.",$themename),
				   "options" => array("post_title", "menu_order","post_date","post_modified","ID","post_author","post_name")),

			array( "name" => esc_html__("Order Pages Links by Ascending/Descending",$themename),
				   "id" => $shortname."_order_page",
				   "type" => "select",
				   "std" => "asc",
				   "desc" => esc_html__("Here you can choose to reverse the order that your pages links are displayed. You can choose between ascending and descending.",$themename),
				   "options" => array("asc", "desc")),

			array( "name" => esc_html__("Number of dropdown tiers shown",$themename),
					"id" => $shortname."_tiers_shown_pages",
					"type" => "text",
					"std" => "3",
					"desc" => esc_html__("This options allows you to control how many teirs your pages dropdown menu has. Increasing the number allows for additional menu items to be shown.",$themename),
					"validation_type" => "number"
			),

			array( "type" => "clearfix",),


		array( "name" => "navigation-1",
			   "type" => "subcontent-end",),

		array( "name" => "navigation-2",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Exclude categories from the navigation bar",$themename),
				   "id" => $shortname."_menucats",
				   "type" => "checkboxes",
				   "std" => "",
				   "desc" => esc_html__("Here you can choose to remove certain categories from the navigation menu. All categories marked with an X will not appear in your navigation bar. ",$themename),
				   "usefor" => "categories",
				   "options" => $cats_ids),

			array( "name" => esc_html__("Show dropdown menus",$themename),
			"id" => $shortname."_enable_dropdowns_categories",
			"type" => "checkbox",
			"std" => "on",
			"desc" => esc_html__("If you would like to remove the dropdown menus from the categories navigation bar disable this feature.",$themename)
			),

			array( "name" => esc_html__("Hide empty categories",$themename),
			"id" => $shortname."_categories_empty",
			"type" => "checkbox",
			"std" => "on",
			"desc" => esc_html__("If you would like categories to be displayed in your navigationbar that don't have any posts in them then disable this option. By default empty categories are hidden",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Number of dropdown tiers shown",$themename),
					"id" => $shortname."_tiers_shown_categories",
					"type" => "text",
					"std" => "3",
					"desc" => esc_html__("This options allows you to control how many teirs your pages dropdown menu has. Increasing the number allows for additional menu items to be shown.",$themename),
					"validation_type" => "number"
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Sort Categories Links by Name/ID/Slug/Count/Term Group",$themename),
				   "id" => $shortname."_sort_cat",
				   "type" => "select",
				   "std" => "name",
				   "desc" => esc_html__("By default pages are sorted by name. However if you would rather have them sorted by ID you can adjust this setting.",$themename),
				   "options" => array("name", "ID", "slug", "count", "term_group")),

			array( "name" => esc_html__("Order Category Links by Ascending/Descending",$themename),
				   "id" => $shortname."_order_cat",
				   "type" => "select",
				   "std" => "asc",
				   "desc" => esc_html__("Here you can choose to reverse the order that your categories links are displayed. You can choose between ascending and descending.",$themename),
				   "options" => array("asc", "desc")),

		array( "name" => "navigation-2",
			   "type" => "subcontent-end",),

		array( "name" => "navigation-3",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Disable top tier dropdown menu links",$themename),
			"id" => $shortname."_disable_toptier",
			"type" => "checkbox2",
			"std" => "false",
			"desc" => esc_html__("In some cases users will want to create parent categories or links as placeholders to hold a list of child links or categories. In this case it is not desirable to have the parent links lead anywhere, but instead merely serve an organizational function. Enabling this options will remove the links from all parent pages/categories so that they don't lead anywhere when clicked.",$themename)
			),

			array( "type" => "clearfix",),

		array( "name" => "navigation-3",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-navigation",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "wrap-layout",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "layout-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Single Post Layout",$themename)
			),

			array( "name" => "layout-2",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Single Page Layout",$themename)
			),

			array( "name" => "layout-3",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("General Settings",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "layout-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Choose which items to display in the postinfo section",$themename),
				   "id" => $shortname."_postinfo2",
				   "type" => "different_checkboxes",
				   "std" => array("author","date","comments"),
				   "desc" => esc_html__("Here you can choose which items appear in the postinfo section on single post pages. This is the area, usually below the post title, which displays basic information about your post. The highlighted itmes shown below will appear. ",$themename),
				   "options" => array("author","date","categories","comments")),

			array(
				"name" => esc_html__("Show comments on posts",$themename),
				"id" => $shortname."_show_postcomments",
				"type" => "checkbox",
				"std" => "on",
				"desc" => esc_html__("You can disable this option if you want to remove the comments and comment form from single post pages. ",$themename)
			),

			array( "type" => "clearfix",),

		array( "name" => "layout-1",
			   "type" => "subcontent-end",),

		array( "name" => "layout-2",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Show comments on pages",$themename),
			"id" => $shortname."_show_pagescomments",
			"type" => "checkbox",
			"std" => "false",
			"desc" => esc_html__("By default comments are not placed on pages, however, if you would like to allow people to comment on your pages simply enable this option. ",$themename)
			),

			array( "type" => "clearfix",),

		array( "name" => "layout-2",
			   "type" => "subcontent-end",),

		array( "name" => "layout-3",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Post info section",$themename),
				   "id" => $shortname."_postinfo1",
				   "type" => "different_checkboxes",
				   "std" => array("author","date"),
				   "desc" => esc_html__("Here you can choose which items appear in the postinfo section on pages. This is the area, usually below the post title, which displays basic information about your post. The highlighted itmes shown below will appear. ",$themename),
				   "options" => array("author","date","categories","comments")),

			array( "name" => esc_html__("Show Author Avatar",$themename),
				   "id" => $shortname."_show_avatar_on_posts",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("Enable this option to show the post author avatar on index pages.",$themename)
			),

			array( "type" => "clearfix",),

		array( "name" => "layout-3",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-layout",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//
	array( "name" => "wrap-seo",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "seo-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Homepage SEO",$themename)
			),

			array( "name" => "seo-2",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Single Post Page SEO",$themename)
			),

			array( "name" => "seo-3",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Index Page SEO",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "seo-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__(" Enable custom title ",$themename),
				   "id" => $shortname."_seo_home_title",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default the theme uses a combination of your blog name and your blog description, as defined when you created your blog, to create your homepage titles. However if you want to create a custom title then simply enable this option and fill in the custom title field below. ",$themename)
			),

			array( "name" => esc_html__(" Enable meta description",$themename),
				   "id" => $shortname."_seo_home_description",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default the theme uses your blog description, as defined when you created your blog, to fill in the meta description field. If you would like to use a different description then enable this option and fill in the custom description field below. ",$themename)
			),

			array( "name" => esc_html__(" Enable meta keywords",$themename),
				   "id" => $shortname."_seo_home_keywords",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default the theme does not add keywords to your header. Most search engines don't use keywords to rank your site anymore, but some people define them anyway just in case. If you want to add meta keywords to your header then enable this option and fill in the custom keywords field below. ",$themename)
			),

			array( "name" => esc_html__(" Enable canonical URL's",$themename),
				   "id" => $shortname."_seo_home_canonical",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URLs all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Homepage custom title (if enabled)",$themename),
				   "id" => $shortname."_seo_home_titletext",
				   "type" => "text",
				   "std" => "",
				   "desc" => esc_html__("If you have enabled custom titles you can add your custom title here. Whatever you type here will be placed between the < title >< /title > tags in header.php",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("Homepage meta description (if enabled)",$themename),
				   "id" => $shortname."_seo_home_descriptiontext",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("If you have enabled meta descriptions you can add your custom description here.",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("Homepage meta keywords (if enabled)",$themename),
				   "id" => $shortname."_seo_home_keywordstext",
				   "type" => "text",
				   "std" => "",
				   "desc" => esc_html__("If you have enabled meta keywords you can add your custom keywords here. Keywords should be separated by comas. For example: wordpress,themes,templates,elegant",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("If custom titles are disabled, choose autogeneration method",$themename),
				   "id" => $shortname."_seo_home_type",
				   "type" => "select",
				   "std" => "BlogName | Blog description",
				   "options" => array("BlogName | Blog description", "Blog description | BlogName", "BlogName only"),
				   "desc" => esc_html__("If you are not using cutsom post titles you can still have control over how your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.",$themename)
			),

			array( "name" => esc_html__("Define a character to separate BlogName and Post title",$themename),
				   "id" => $shortname."_seo_home_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => esc_html__("Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -",$themename),
					"validation_type" => "nohtml"
			),

		array( "name" => "seo-1",
			   "type" => "subcontent-end",),

		array( "name" => "seo-2",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Enable custom titles",$themename),
				   "id" => $shortname."_seo_single_title",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("By default the theme creates post titles based on the title of your post and your blog name. If you would like to make your meta title different than your actual post title you can define a custom title for each post using custom fields. This option must be enabled for custom titles to work, and you must choose a custom field name for your title below.",$themename)
			),

			array( "name" => esc_html__("Enable custom description",$themename),
				   "id" => $shortname."_seo_single_description",
				   "type" => "checkbox2",
				   "std" => "false",
				   "desc" => esc_html__("If you would like to add a meta description to your post you can do so using custom fields. This option must be enabled for descriptions to be displayed on post pages. You can add your meta description using custom fields based off the custom field name you define below.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Enable custom keywords",$themename),
				   "id" => $shortname."_seo_single_keywords",
					"type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("If you would like to add meta keywords to your post you can do so using custom fields. This option must be enabled for keywords to be displayed on post pages. You can add your meta keywords using custom fields based off the custom field name you define below.",$themename)
			),

			array( "name" => esc_html__("Enable canonical URL's",$themename),
				   "id" => $shortname."_seo_single_canonical",
				   "type" => "checkbox2",
				   "std" => "false",
				   "desc" => esc_html__("Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Custom field Name to be used for title",$themename),
				   "id" => $shortname."_seo_single_field_title",
				   "type" => "text",
				   "std" => "seo_title",
				   "desc" => esc_html__("When you define your title using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom title you would like to use.",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("Custom field Name to be used for description",$themename),
				   "id" => $shortname."_seo_single_field_description",
				   "type" => "text",
				   "std" => "seo_description",
				   "desc" => esc_html__("When you define your meta description using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom description you would like to use.",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("Custom field Name to be used for keywords",$themename),
				   "id" => $shortname."_seo_single_field_keywords",
				   "type" => "text",
				   "std" => "seo_keywords",
				   "desc" => esc_html__("When you define your keywords using custom fields you should use this value for the custom field Name. The Value of your custom field should be the meta keywords you would like to use, separated by comas.",$themename),
					"validation_type" => "nohtml"
			),

			array( "name" => esc_html__("If custom titles are disabled, choose autogeneration method",$themename),
				   "id" => $shortname."_seo_single_type",
				   "type" => "select",
				   "std" => "Post title | BlogName",
				   "options" => array("Post title | BlogName", "BlogName | Post title", "Post title only"),
				   "desc" => esc_html__("If you are not using cutsom post titles you can still have control over hw your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.",$themename)
			),

			array( "name" => esc_html__("Define a character to separate BlogName and Post title",$themename),
				   "id" => $shortname."_seo_single_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => esc_html__("Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -",$themename),
					"validation_type" => "nohtml"
			),

		array( "name" => "seo-2",
			   "type" => "subcontent-end",),

		array( "name" => "seo-3",
				   "type" => "subcontent-start",),

			array( "name" => esc_html__(" Enable canonical URL's",$themename),
				   "id" => $shortname."_seo_index_canonical",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",$themename)
			),

			array( "name" => esc_html__("Enable meta descriptions",$themename),
				   "id" => $shortname."_seo_index_description",
					"type" => "checkbox2",
				   "std" => "false",
				   "desc" => esc_html__("Check this box if you want to display meta descriptions on category/archive pages. The description is based off the category description you choose when creating/edit your category in wp-admin.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Choose title autogeneration method",$themename),
				   "id" => $shortname."_seo_index_type",
				   "type" => "select",
				   "std" => "Category name | BlogName",
				   "options" => array("Category name | BlogName", "BlogName | Category name", "Category name only"),
				   "desc" => esc_html__("Here you can choose how your titles on index pages are generated. You can change which order your blog name and index title are displayed, or you can remove the blog name from the title completely.",$themename)
			),

			array( "name" => esc_html__("Define a character to separate BlogName and Post title",$themename),
				   "id" => $shortname."_seo_index_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => esc_html__("Here you can change which character separates your blog title and index page name when using autogenerated post titles. Common values are | or -",$themename),
					"validation_type" => "nohtml"
			),

			array( "type" => "clearfix",),

		array( "name" => "seo-3",
				   "type" => "subcontent-end",),

	array(  "name" => "wrap-seo",
			"type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "wrap-integration",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "integration-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Code Integration",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "integration-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Enable header code",$themename),
				   "id" => $shortname."_integrate_header_enable",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("Disabling this option will remove the header code below from your blog. This allows you to remove the code while saving it for later use.",$themename)
			),

			array( "name" => esc_html__("Enable body code",$themename),
				   "id" => $shortname."_integrate_body_enable",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("Disabling this option will remove the body code below from your blog. This allows you to remove the code while saving it for later use.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Enable single top code",$themename),
				   "id" => $shortname."_integrate_singletop_enable",
				   "type" => "checkbox",
				   "std" => "on",
				   "desc" => esc_html__("Disabling this option will remove the single top code below from your blog. This allows you to remove the code while saving it for later use.",$themename)
			),

			array( "name" => esc_html__("Enable single bottom code",$themename),
				   "id" => $shortname."_integrate_singlebottom_enable",
				   "type" => "checkbox2",
				   "std" => "on",
				   "desc" => esc_html__("Disabling this option will remove the single bottom code below from your blog. This allows you to remove the code while saving it for later use.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Add code to the < head > of your blog",$themename),
				   "id" => $shortname."_integration_head",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("Any code you place here will appear in the head section of every page of your blog. This is useful when you need to add javascript or css to all pages.",$themename)
			),

			array( "name" => esc_html__("Add code to the < body > (good for tracking codes such as google analytics)",$themename),
				   "id" => $shortname."_integration_body",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("Any code you place here will appear in body section of all pages of your blog. This is usefull if you need to input a tracking pixel for a state counter such as Google Analytics.",$themename)
			),

			array( "name" => esc_html__("Add code to the top of your posts",$themename),
				   "id" => $shortname."_integration_single_top",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.",$themename)
			),

			array( "name" => esc_html__("Add code to the bottom of your posts, before the comments",$themename),
				   "id" => $shortname."_integration_single_bottom",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.",$themename)
			),

		array( "name" => "integration-1",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-integration",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "wrap-support",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "support-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Documentation",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "support-1",
			   "type" => "subcontent-start",),

			array( "name" => "installation",
				   "type" => "support",),

		array( "name" => "support-1",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-support",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "wrap-updates",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "updates-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("General",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "updates-1",
			   "type" => "subcontent-start",),

			array(
				'name'              => esc_html__( 'Username', $themename ),
				'id'                => 'et_automatic_updates_options_username',
				'std'               => '',
				'type'              => 'password',
				'validation_type'   => 'nohtml',
				'desc'              => et_get_safe_localization( __( 'Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key. Your username is the same username you use when logging in to <a href="http://elegantthemes.com/" target="_blank">ElegantThemes.com</a>', $themename ) ),
				'is_global'         => true,
				'main_setting_name' => 'et_automatic_updates_options',
				'sub_setting_name'  => 'username',
			),

			array(
				'name'            => esc_html__( 'API Key', $themename ),
				'id'              => 'et_automatic_updates_options_api_key',
				'std'             => '',
				'type'            => 'password',
				'validation_type' => 'nohtml',
				'desc'            => et_get_safe_localization( __( 'Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key. To locate your API Key, <a href="https://www.elegantthemes.com/members-area/" target="_blank">log in</a> to your Elegant Themes account and navigate to the <strong>Account > API Key</strong> page.', $themename ) ),
				'is_global'         => true,
				'main_setting_name' => 'et_automatic_updates_options',
				'sub_setting_name'  => 'api_key',
			),

		array( "name" => "updates-1",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-updates",
		   "type" => "contenttab-wrapend",),

	array( "name" => "wrap-advertisements",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "advertisements-1",
				   "type" => "subnav-tab",
				   "desc" => esc_html__("Manage Un-widgetized Advertisements",$themename)
			),

		array( "type" => "subnavtab-end",),

		array( "name" => "advertisements-1",
			   "type" => "subcontent-start",),

			array( "name" => esc_html__("Enable Single Post 468x60 banner",$themename),
				   "id" => $shortname."_468_enable",
					"type" => "checkbox",
				   "std" => "false",
				   "desc" => esc_html__("Enabling this option will display a 468x60 banner ad on the bottom of your post pages below the single post content. If enabled you must fill in the banner image and destination url below.",$themename)
			),

			array( "type" => "clearfix",),

			array( "name" => esc_html__("Input 468x60 advertisement banner image",$themename),
				   "id" => $shortname."_468_image",
				   "type" => "text",
				   "std" => "",
				   "desc" => esc_html__("Here you can provide 468x60 banner image url",$themename),
					"validation_type" => "url"
			),

			array( "name" => esc_html__("Input 468x60 advertisement destination url",$themename),
				   "id" => $shortname."_468_url",
				   "type" => "text",
				   "std" => "",
				   "desc" => esc_html__("Here you can provide 468x60 banner destination url",$themename),
					"validation_type" => "url"
			),

			array( "name" => esc_html__("Input 468x60 adsense code",$themename),
				   "id" => $shortname."_468_adsense",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => esc_html__("Place your adsense code here.",$themename)
			),

		array( "name" => "advertisements-1",
			   "type" => "subcontent-end",),

	array( "name" => "wrap-support",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

);