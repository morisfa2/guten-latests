<?php
/**
 * Plugin Name: Latest Gutenberg Posts
 * Plugin URI: https://morisfa.com/
 * Description: Show Latest Posts For Word-press Gutenberg Editor
 * Version: 0.0.1
 * Author: Morteza Faraji
 * Author URI: https://morisfa.com
 * Requires at least: < 7.4
 * Requires PHP: < 7.4
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package latest-guten-block
 */
require_once __DIR__ . '/includes/LatestPosts-Block.php';
// Load block.
use Morisfa\GutenLatest\Block\GutenLatestBlock;




/**
 * Refister The First Block - Latest Posts Gutenberg
 */
new GutenLatestBlock();