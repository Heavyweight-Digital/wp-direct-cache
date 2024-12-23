# WP Direct Cache

**Author:** [byronjacobs](https://byronjacobs.co.za/)
  
**Tags:** cache, performance, caching, speed, optimization
  
**Requires at least:** 4.8
  
**Tested up to:** 6.0
  
**Stable tag:** 1.0
  
**Requires PHP:** 7.4
  
**License:** GPL v2 or later
  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html
  
## Description

WP Direct Cache is a WordPress plugin that allows for the direct serving of cached HTML files, bypassing PHP and WordPress processing. This feature enhances the website's performance by reducing load times and server resource usage. The plugin includes a tool to check cache status for pages, providing users with information on which pages are cached and served efficiently.

## Features

- **Direct HTML Cache Serving:** Serve static cached HTML files directly if they exist.
- **Bypass WordPress Processing:** Skip PHP and WordPress processing for faster load times.
- **Admin Cache Status Page:** Check the cache status of each page from the WordPress admin.
- **Automatic .htaccess Configuration:** Automatically generates and updates .htaccess rules for cache handling.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wp-direct-cache` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to the 'Cache Status' page under the WordPress admin menu to view and manage your cache settings.

## Usage

Once activated, the plugin will automatically configure your `.htaccess` file to serve cached HTML files directly. You can visit the 'Cache Status' page in the WordPress admin to see which pages are cached and being served efficiently.

## Frequently Asked Questions

**Q: What happens if my `.htaccess` file is not writable?**

A: If the `.htaccess` file is not writable, the plugin will notify you through the WordPress admin. You will need to update the file's permissions to enable direct cache serving.

**Q: How do I know if a page is cached?**

A: You can check if a page is cached by visiting the 'Cache Status' page in the admin menu. The plugin will list all the pages and their cache status.

## Changelog

### 1.0
- Initial release of WP Direct Cache.
