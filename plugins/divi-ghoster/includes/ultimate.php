<?php
/*
Changes by Aspen Grove Studios:
2019-01-02	Created class, partially using code from other Ghoster files and AGS Layouts class template
2019-01-03	Add more copied Ghoster code; more work on transforming copied code into class (updating method calls, etc.); replace boolean returns with Exceptions
2019-01-04	Fixed/updated refactored code; fixed theme screenshot renaming logic; implemented child theme restoration; fix/implement changing child theme
2019-01-07	Add/move check for non-empty theme in onUpdateInstalled
2019-01-08	Add include antibot.php if not in the admin
2019-01-09	Fix update handling for ghosted child themes; add modification statement to ghosted stylesheets and update license statement
2019-01-10	Remove part of DiviGhosterUltimate::onThemeSetup method (moved to DiviGhoster class) and change associated after_setup_theme add_filter call to add_action
*/

class DiviGhosterUltimate {
	private static $themeDirectoryUri;
	
	public static function run() {
		add_action('permalink_structure_changed', array('DiviGhosterUltimate', 'onPermalinkStructureChange'), 10, 2);
		add_action('template_redirect', array('DiviGhosterUltimate', 'templateRedirect'));
		add_action('after_setup_theme', array('DiviGhosterUltimate', 'onThemeSetUp'), 9999);
		
		add_filter('template_directory_uri', array('DiviGhosterUltimate', 'filterThemeDirectoryUri'), 10, 3);
		add_filter('stylesheet_directory_uri', array('DiviGhosterUltimate', 'filterThemeDirectoryUri'), 10, 3);
		add_filter('script_loader_tag', array('DiviGhosterUltimate', 'filterScriptTag'), 10, 2);
		add_filter('style_loader_tag', array('DiviGhosterUltimate', 'filterScriptTag'), 10, 2);
		add_filter('plugins_api_result', array('DiviGhosterUltimate', 'filterPluginsApiResult'));
		add_filter('upgrader_post_install', array('DiviGhosterUltimate', 'onUpdateInstalled'), 10, 2);
		add_filter('wp_prepare_themes_for_js', array('DiviGhosterUltimate', 'filterThemesList'));
		add_filter('all_plugins', array('DiviGhosterUltimate', 'filterPluginsList'));
		
		global $pagenow;
		if ($pagenow != 'options.php' || empty($_POST['option_page']) || $_POST['option_page'] != 'agsdg_pluginPage') {
			// Add the Ultimate Ghoster rewrite rule, except when we're saving plugin options
			add_action('init', array('DiviGhosterUltimate', 'rewriteRule'));
		}
		
		if ($pagenow == 'plugin-editor.php') {
			add_action('init', array('DiviGhosterUltimate', 'modifyCachedPluginsInfoForPluginsPage'));
		} else if ($pagenow == 'update-core.php') {
			add_action('init', array('DiviGhosterUltimate', 'modifyCachedPluginsInfoForUpdatesPage'));
		} else if ($pagenow == 'update.php' && isset($_REQUEST['action']) && ($_REQUEST['action'] == 'update-selected' || $_REQUEST['action'] == 'upgrade-plugin')) {
			ob_start(array('DiviGhosterUltimate', 'processPluginUpdaterOutput'), 2, false);
			set_error_handler(array('DiviGhosterUltimate', 'processPluginUpdaterOutputErrors'), E_NOTICE);
		}
		
		// Set up theme global variable based on Ultimate Ghoster theme (copied from DiviGhoster::run function)
		DiviGhoster::$targetTheme = get_option('adsdg_ultimate_theme');
		DiviGhoster::$targetThemeSlug = strtolower(DiviGhoster::$targetTheme);
		
		// Enable Divi 100
		// TODO: Fix this (was uncommented in last release version)
		/*function et_divi_100_is_active() {
			global DiviGhoster::$targetTheme;
			return (DiviGhoster::$targetTheme == 'Divi');
		}*/
		
		if (!is_admin()) {
			include_once(__DIR__.'/antibot.php');
		}
		
	}
	
	public static function enable($ghostSlug, $ghostName) {
		if (self::isEnabled()) {
			throw new Exception('There is an existing Ultimate Ghoster instance.');
		}
		
		self::changeThemeMeta($ghostName);
		self::changeAllChildThemes($ghostSlug);
		self::rewriteRule($ghostSlug);
		
		// Make symlink - will fail silently if it already exists
		$themesRoot = get_theme_root();
		@symlink($themesRoot.'/'.DiviGhoster::$targetTheme, $themesRoot.'/'.$ghostSlug);
		
		// Implement fallback if needed
		self::maybeAddFallback($ghostSlug);
		
		flush_rewrite_rules();
	}
	
	public static function disable($ghostSlug) {
		self::restoreAllChildThemes();
		self::restoreThemeMeta();
		self::removeFallback();
		self::removeThemeSymlink($ghostSlug);
		flush_rewrite_rules();
		
		if (self::isEnabled()) {
			throw new Exception();
		}
	}
	
	// Checks whether Ultimate appears to be enabled on the site, regardless of the setting value
	public static function isEnabled() {
		return file_exists(get_theme_root().'/'.DiviGhoster::$targetTheme.'/style.pre_agsdg.css');
	}
	
	public static function filterPluginsList($plugins, $invert=false) {
		foreach ($plugins as $plugin => $v) {
			$pluginLc = strtolower($plugin);
			if ((substr($pluginLc, 0, 5) == 'divi-' || strpos($pluginLc, '/divi-') !== false) xor $invert) {
				unset($plugins[$plugin]);
			}
		}
		return $plugins;
	}
	
	public static function rewriteRule($slug=null) {
		if (empty($slug)) {
			add_rewrite_rule('wp-content/themes/'.DiviGhoster::$settings['theme_slug'].'/(.*)$', 'wp-content/themes/'.DiviGhoster::$targetTheme.'/$1');
		} else {
			add_rewrite_rule('wp-content/themes/'.$slug.'/(.*)$', 'wp-content/themes/'.DiviGhoster::$targetTheme.'/$1');
		}
	}
	
	// If $childTheme, then $newThemeName is the theme slug, not the branding name
	public static function changeThemeMeta($newThemeName, $childTheme=false) {
		$themeRoot = (empty($childTheme) ? get_theme_root().'/'.DiviGhoster::$targetTheme.'/' : $childTheme->get_stylesheet_directory().'/');
		$stylesheetFile = $themeRoot.'style.css';
		
		// Backup original stylesheet
		if (file_exists($themeRoot.'style.pre_agsdg.css') || !@copy($stylesheetFile, $themeRoot.'style.pre_agsdg.css')) {
			throw new Exception('Unable to back up original theme stylesheet.');
		}
		
		$stylesheetContents = @file_get_contents($themeRoot.'style.pre_agsdg.css');
		if ($stylesheetContents === false)
			throw new Exception('Unable to retrieve theme stylesheet contents.');
		
		// Normalize line endings in stylesheet
		$stylesheetContents = str_replace(array("\r\n", "\r"), "\n", $stylesheetContents);
			
		$commentStartPos = strpos($stylesheetContents, '/*');
		$commentEndPos = strpos($stylesheetContents, '*/');
		if ($commentStartPos === false || $commentEndPos === false)
			throw new Exception('Theme stylesheet header seems to be missing.');
		
		$comment = trim(substr($stylesheetContents, $commentStartPos + 2, ($commentEndPos - $commentStartPos)));
		
		$newComment = '';
		//$newComment2 = '';
		
		
		foreach (explode("\n", $comment) as $commentLine) {
			$commentLine = trim($commentLine);
			if (empty($commentLine)) {
				continue;
			}
			if ($commentLine[0] == '*' || $commentLine[0] == '#')
				$commentLine = trim(substr($commentLine, 1));
		
			$colonPos = strpos($commentLine, ':');
			if ($colonPos !== false) {
				$beforeColon = substr($commentLine, 0, $colonPos);
				if (empty($childTheme)) {
					if ($beforeColon == 'Theme Name') {
						$newComment .= 'Theme Name: '.$newThemeName."\n";
					} else if ($beforeColon == 'Version' || $beforeColon == 'License' || $beforeColon == 'License URI') {
						$newComment .= $commentLine."\n";
					} /*else if ($beforeColon != 'Description' && $beforeColon != 'Tags') {
						$newComment2 .= 'Original '.$beforeColon.' - '.substr($commentLine, $colonPos + 1)."\n";
					}*/
				} else {
					if ($beforeColon == 'Template') {
						$newComment .= 'Template: '.$newThemeName."\n";
					} else {
						$newComment .= $commentLine."\n";
					}
				}
			}
		}
		
		$stylesheetContents = 
			($commentStartPos > 0 ? substr($stylesheetContents, 0, $commentStartPos) : '')
			."/*\n".$newComment."*/\n"
			.(empty($childTheme)
				? '/* For copyright information, see the ./LICENSE.md file (in this directory). This file was modified '.date('Y-m-d').' by Aspen Grove Studios to customize metadata in header comment and add this line. */'
				: '/* This file was modified '.date('Y-m-d').' by Aspen Grove Studios to customize metadata in header comment */')
			.substr($stylesheetContents, $commentEndPos + 2);
			//.(empty($newComment2) ? '' : "\n/*\n".$newComment2.'*/');

		// Add new header, save stylesheet
		if (!file_put_contents($stylesheetFile, $stylesheetContents))
			throw new Exception('Unable to save updated theme stylesheet.');
		
		// Remove theme screenshot
		if (
			empty($childTheme) &&
			(
				(file_exists($themeRoot.'screenshot.jpg') && !@rename($themeRoot.'screenshot.jpg', $themeRoot.'_screenshot.jpg')) ||
				(file_exists($themeRoot.'screenshot.png') && !@rename($themeRoot.'screenshot.png', $themeRoot.'_screenshot.png'))
			)
		) {
			throw new Exception('Unable to rename theme screenshot.');
		}
	}
	
	public static function changeAllChildThemes($newThemeSlug) {
		foreach (wp_get_themes() as $theme) {
			if ($theme->parent() == DiviGhoster::$targetTheme) {
				self::changeThemeMeta($newThemeSlug, $theme);
			}
		}
	}
	
	public static function restoreAllChildThemes() {
		foreach (wp_get_themes() as $theme) {
			if ($theme->parent() == DiviGhoster::$settings['branding_name']) {
				self::restoreThemeMeta($theme);
			}
		}
	}

	public static function restoreThemeMeta($childTheme=null) {
		$themeRoot = (empty($childTheme) ? get_theme_root().'/'.DiviGhoster::$targetTheme.'/' : $childTheme->get_stylesheet_directory().'/');
		if (!@rename($themeRoot.'style.pre_agsdg.css', $themeRoot.'style.css') ||
			!(!file_exists($themeRoot.'_screenshot.jpg') || @rename($themeRoot.'_screenshot.jpg', $themeRoot.'screenshot.jpg')) ||
			!(!file_exists($themeRoot.'_screenshot.png') || @rename($themeRoot.'_screenshot.png', $themeRoot.'screenshot.png'))
		) {
			throw new Exception('Unable to restore original theme stylesheet and screenshot.');
		}
	}
	
	public static function onUpdateInstalled($return, $args) {
		if (empty($args['theme'])) {
			return;
		}
	
		try {
			if (strcasecmp($args['theme'], DiviGhoster::$targetTheme) == 0) {
				if (empty(DiviGhoster::$settings['branding_name'])) {
					throw new Exception();
				}
				self::changeThemeMeta(DiviGhoster::$settings['branding_name'], false);
			} else {
				$updatedTheme = wp_get_theme($args['theme']);
				if ($updatedTheme->parent() == DiviGhoster::$settings['branding_name']) {
					if (empty(DiviGhoster::$settings['theme_slug'])) {
						throw new Exception();
					}
					self::changeThemeMeta(DiviGhoster::$settings['theme_slug'], $updatedTheme);
				}
			}
		} catch (Exception $ex) {
			return new WP_Error('AGSDG_ERROR');
		}
		
		
		try {
			self::removeFallback();
			self::maybeAddFallback();
		} catch (Exception $ex) {
			return new WP_Error('AGSDG_FALLBACK_ERROR');
		}
	}
	
	public static function filterThemeDirectoryUri($uri, $template, $themeRootUri) {
		if (strcasecmp($template, DiviGhoster::$targetTheme) == 0) {
			if (!isset(self::$themeDirectoryUri)) {
				self::$themeDirectoryUri = $themeRootUri.'/'.DiviGhoster::$settings['theme_slug'];
			}
			return self::$themeDirectoryUri;
		}
		return $uri;
	}
	
	public static function onPermalinkStructureChange($oldStructure, $newStructure) {
		// Prevent the user from changing the permalink structure to Plain while Ultimate Ghoster is enabled
		if (empty($newStructure)) {
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure($oldStructure);
		}
	}

	public static function removeThemeSymlink($slug) {
		$themesRoot = get_theme_root();
		$linkTarget = @readlink($themesRoot.'/'.$slug);
		
		return (!($linkTarget && realpath($linkTarget) == realpath($themesRoot.'/'.DiviGhoster::$targetTheme)) || @unlink($themesRoot.'/'.$slug));
	}
	
	public static function templateRedirect() {
		// Intercept 404 and determine whether it is a theme file
		if (is_404()) {
			$themeRoot = parse_url(get_template_directory_uri(), PHP_URL_PATH);
			$themeRootLen = strlen($themeRoot);
			if (strlen($_SERVER['REQUEST_URI']) >= $themeRootLen
					&& substr($_SERVER['REQUEST_URI'], 0, $themeRootLen) == $themeRoot
					&& strpos($_SERVER['REQUEST_URI'], '..') === false) {
				
				$qPos = strpos($_SERVER['REQUEST_URI'], '?');
				$themeFile = get_template_directory().($qPos === false ? substr($_SERVER['REQUEST_URI'], $themeRootLen) : substr($_SERVER['REQUEST_URI'], $themeRootLen, $qPos - $themeRootLen));
				
				if (strpos($themeFile, -4) != '.php') {
				
					if (function_exists('mime_content_type')) {
						$mimeType = mime_content_type($themeFile);
					}
					if (empty($mimeType)) {
						$dotPos = strrpos($themeFile, '.');
						if ($dotPos === false) {
							$mimeType = 'application/octet-stream';
						} else {
							switch (strtolower(substr($themeFile, $dotPos + 1))) {
								case 'htm':
								case 'html':
									$mimeType = 'text/html';
									break;
								case 'txt':
									$mimeType = 'text/plain';
									break;
								case 'js':
									$mimeType = 'application/javascript';
									break;
								case 'css':
									$mimeType = 'text/css';
									break;
								case 'xml':
									$mimeType = 'text/xml';
									break;
								case 'png':
									$mimeType = 'image/png';
									break;
								case 'jpg':
								case 'jpeg':
									$mimeType = 'image/jpeg';
									break;
								case 'gif':
									$mimeType = 'image/gif';
									break;
								default:
									$mimeType = 'application/octet-stream';
							}
						}
					}
					
					header('HTTP/1.0 200 OK');
					header('Content-Type: '.$mimeType);
					readfile($themeFile);
					exit;
				}
			}
		}
	}

	public static function maybeAddFallback($themeSlug=null) {
		if ($themeSlug === null) {
			$themeSlug = DiviGhoster::$settings['theme_slug'];
		}
		
		// Check if style.css can be retrieved successfully using the new theme slug
		$result = @file_get_contents(get_theme_root_uri().'/'.$themeSlug.'/style.css', false, null, 0, 1);
		if ($result !== false) {
			return;
		}
		
		// Fetching the stylesheet failed, so we need to implement the fallback
		WP_Filesystem();
		$newDir = get_theme_root().'/'.$themeSlug;
		if (is_link($newDir))
			unlink($newDir);
		if (!@mkdir($newDir) || !file_put_contents($newDir.'/agsdg_fallback.txt', 'agsdg_fallback') || !copy_dir(get_template_directory(), $newDir))
			throw new Exception('Unable to create copy of theme.');
	}
	
	/* Remove fallback if it was implemented */
	public static function removeFallback() {
		global $wp_filesystem;
		$newDir = get_theme_root().'/'.DiviGhoster::$settings['theme_slug'];
		WP_Filesystem();
		if (file_exists($newDir.'/agsdg_fallback.txt') && !$wp_filesystem->rmdir($newDir, true)) {
			throw new Exception('Unable to remove copy of theme.');
		}
	}
	
	// Hide custom slug from themes list
	public static function filterThemesList($themes) {
		if (isset($themes[DiviGhoster::$settings['theme_slug']])) {
			unset($themes[DiviGhoster::$settings['theme_slug']]);
		}
		return $themes;
	}
	
	public static function modifyCachedPluginsInfoForPluginsPage() {
		// Remove theme-related plugins from the cache so they don't show up in the plugin editor list
		get_plugins();
		$plugins = wp_cache_get('plugins', 'plugins');
		$plugins[''] = self::filterPluginsList($plugins['']);
		wp_cache_set('plugins', $plugins, 'plugins');
	}
	
	public static function modifyCachedPluginsInfoForUpdatesPage() {
		// Rebrand plugins in updates page
		get_plugins();
		$plugins = wp_cache_get('plugins', 'plugins');
		
		foreach (self::filterPluginsList($plugins[''], true) as $pluginFile => $pluginData) {
			if ($pluginData['Name'] == 'Divi Ghoster') {
				$newName = DiviGhoster::$settings['branding_name'].' Addon';
			} else {
				$newName = preg_replace('/(Divi'.(DiviGhoster::$targetTheme == 'Divi' ? '' : '|'.preg_quote(DiviGhoster::$targetTheme, '/')).')\b/i', DiviGhoster::$settings['branding_name'], $pluginData['Name']);
			}
			if ($newName != $plugins[''][$pluginFile]['Name']) {
				$plugins[''][$pluginFile]['Name'] = $newName;
			}
		}
		wp_cache_set('plugins', $plugins, 'plugins');
	}
	
	public static function filterPluginsApiResult($result) {
		/*$pluginDir = $result->slug.'/';
		$pluginDirLen = strlen($pluginDir);
		$pluginFile = $result->slug.'.php';
		
		foreach (agsdg_hidden_plugins() as $hiddenPlugin => $t) {
			if ($hiddenPlugin == $pluginFile || substr($hiddenPlugin, 0, $pluginDirLen) == $pluginDir) {
				$isHidden = true;
				break;
			}
		}*/
		
		// TODO: Following line throws a warning on add plugin page
		
		$pluginLc = strtolower($result->slug);
		if (substr($pluginLc, 0, 5) == 'divi-' || strpos($pluginLc, '/divi-') !== false) { // Plugin is hidden
			if ($result->name == 'Divi Ghoster') {
				$result->name = DiviGhoster::$settings['branding_name'].' Addon';
			} else {
				$result->name = preg_replace('/(Divi'.(DiviGhoster::$targetTheme == 'Divi' ? '' : '|'.preg_quote(DiviGhoster::$targetTheme, '/')).')\b/i', DiviGhoster::$settings['branding_name'], $result->name);
			}
			$result->author = '';
			$result->author_profile = '';
			$result->donate_link = '';
			$result->banners = array();
			$result->homepage = '';
			$result->contributors = array();
			$result->sections = array('description' => 'See version and compatibility details to the right, if applicable.');
		}
		
		return $result;
	}

	public static function processPluginUpdaterOutput($outputBuffer) {
		if (substr(trim($outputBuffer), 0, 8) == '<iframe ') {
			return $outputBuffer;
		}
		if (strpos($outputBuffer, 'Downloading update from') !== false)
			$outputBuffer = preg_replace('/Downloading update from(.*)&#8230;/U', 'Downloading update&#8230;', $outputBuffer);
		return preg_replace('/(Divi'.(DiviGhoster::$targetTheme == 'Divi' ? '' : '|'.preg_quote(DiviGhoster::$targetTheme, '/')).')\b/i', DiviGhoster::$settings['branding_name'], str_replace(DiviGhoster::$settings['branding_name'].' Ghoster', DiviGhoster::$settings['branding_name'].' Addon', $outputBuffer));
	}
	
	public static function processPluginUpdaterOutputErrors($level, $message) {
		// Suppress notices related to output buffering
		if (strpos($message, 'processPluginUpdaterOutput') === false)
			return false;
	}
	
	public static function onThemeSetUp() {
		global $l10n;
		// Copy the theme's text domain to the new theme name
		if (!empty($l10n[DiviGhoster::$targetTheme])) {
			$l10n[DiviGhoster::$settings['branding_name']] = &$l10n[DiviGhoster::$targetTheme];
		}
	}
	
	public static function filterScriptTag($tag, $handle) {
		if (stripos($handle, DiviGhoster::$targetThemeSlug) !== false) {
			$newHandle = str_ireplace(array(DiviGhoster::$targetThemeSlug.'-', '-'.DiviGhoster::$targetThemeSlug), array(DiviGhoster::$settings['theme_slug'].'-', '-'.DiviGhoster::$settings['theme_slug']), $handle);
			if ($newHandle != $handle) {
				$tag = str_ireplace(array('id=\''.$handle, 'id="'.$handle), array('id=\''.$newHandle, 'id="'.$newHandle), $tag);
			}
		}
		return $tag;
	}
}