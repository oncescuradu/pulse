<?php
/**
 * Plugin Name: Pulse
 * Description: De pe internet culese.
 * Version: 1.6.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: Radu Oncescu
 * Technical Description: Built by Radu Oncescu with ChatGPT Codex
 * License: GPL-2.0-or-later
 * Text Domain: ro-socialbed
 */

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/providers.php';

final class RO_SocialBed {
 const VERSION = '1.6.0';
 const LIMIT = 20;

 public static function init() {
  add_action( 'init', array( __CLASS__, 'register' ) );
  add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'editor' ) );
  add_action( 'wp_enqueue_scripts', array( __CLASS__, 'styles' ) );
  add_action( 'admin_enqueue_scripts', array( __CLASS__, 'classic_assets' ) );
  add_action( 'media_buttons', array( __CLASS__, 'classic_button' ) );
  add_action( 'admin_menu', array( __CLASS__, 'settings_menu' ) );
  add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
  add_action( 'admin_init', array( __CLASS__, 'migrate_brand_logo' ), 5 );
  add_action( 'admin_init', array( __CLASS__, 'settings_register' ) );
  add_action( 'admin_init', array( __CLASS__, 'remove_legacy_footer_settings' ) );
  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'settings_link' ) );
  add_shortcode( 'ro_pulse', array( __CLASS__, 'shortcode' ) );
  add_shortcode( 'ro_socialbed', array( __CLASS__, 'shortcode' ) );
 }

 public static function register() {
  register_block_type( 'ro-socialbed/posts', array(
   'api_version' => 2,
   'attributes' => array( 'urls' => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'string' ) ) ),
   'render_callback' => array( __CLASS__, 'render' ),
  ) );
 }

 public static function styles() {
  // Available in the head, including when content is rendered after wp_head.
  wp_enqueue_style( 'ro-socialbed', plugins_url( 'assets/style.css', __FILE__ ), array(), self::VERSION );
  wp_enqueue_script( 'pulse-source-cleanup', plugins_url( 'assets/source-cleanup.js', __FILE__ ), array(), self::VERSION, true );
 }

 public static function editor() {
  wp_enqueue_script( 'ro-pulse-providers', plugins_url( 'assets/providers.js', __FILE__ ), array(), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-links', plugins_url( 'assets/links.js', __FILE__ ), array( 'ro-pulse-providers' ), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-x-render', plugins_url( 'assets/x.js', __FILE__ ), array(), self::VERSION, true );
  wp_enqueue_script( 'ro-pulse-instagram-render', plugins_url( 'assets/instagram.js', __FILE__ ), array(), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-editor', plugins_url( 'assets/editor.js', __FILE__ ), array( 'ro-socialbed-links', 'ro-socialbed-x-render', 'ro-pulse-instagram-render', 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ), self::VERSION, true );
  $display = self::display();
  wp_localize_script( 'ro-socialbed-editor', 'roSocialBed', array( 'logo' => self::logo(), 'theme' => $display['theme'] ) );
  wp_enqueue_style( 'ro-socialbed-editor', plugins_url( 'assets/editor.css', __FILE__ ), array(), self::VERSION );
 }

 public static function classic_assets( $hook ) {
  wp_enqueue_style( 'pulse-menu-icon', plugins_url( 'assets/menu.css', __FILE__ ), array(), self::VERSION );
  if ( in_array( $hook, array( 'settings_page_ro-socialbed', 'toplevel_page_pulse' ), true ) ) {
   wp_enqueue_media();
   wp_enqueue_style( 'pulse-admin', plugins_url( 'assets/admin.css', __FILE__ ), array(), self::VERSION );
   wp_enqueue_script( 'pulse-admin', plugins_url( 'assets/admin.js', __FILE__ ), array(), self::VERSION, true );
   wp_enqueue_script( 'ro-socialbed-settings', plugins_url( 'assets/settings.js', __FILE__ ), array( 'media-views' ), self::VERSION, true );
   wp_localize_script( 'ro-socialbed-settings', 'roSocialBedSettings', array( 'defaultLogo' => self::default_logo() ) );
   return;
  }
  if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) { return; }
  wp_enqueue_script( 'ro-pulse-providers', plugins_url( 'assets/providers.js', __FILE__ ), array(), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-links', plugins_url( 'assets/links.js', __FILE__ ), array( 'ro-pulse-providers' ), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-insert', plugins_url( 'assets/insert.js', __FILE__ ), array(), self::VERSION, true );
  wp_enqueue_script( 'ro-socialbed-classic', plugins_url( 'assets/classic.js', __FILE__ ), array( 'ro-socialbed-links', 'ro-socialbed-insert' ), self::VERSION, true );
  wp_localize_script( 'ro-socialbed-classic', 'roSocialBedClassic', array( 'logo' => self::logo() ) );
  wp_enqueue_style( 'ro-socialbed-editor', plugins_url( 'assets/editor.css', __FILE__ ), array(), self::VERSION );
 }

 public static function classic_button( $editor_id ) {
  if ( 'content' === $editor_id ) {
   echo '<button type="button" class="button ro-socialbed-open"><img class="rop-composer-logo" src="' . esc_url( self::logo() ) . '" alt="" aria-hidden="true"><span>Pulse</span></button>';
  }
 }

 public static function logo() {
  $id = absint( get_option( 'ro_pulse_logo', 0 ) );
  $url = $id && wp_attachment_is_image( $id ) ? wp_get_attachment_image_url( $id, 'full' ) : false;
  return $url ? $url : self::default_logo();
 }

 public static function default_logo() {
  return add_query_arg( 'ver', self::VERSION, plugins_url( 'assets/pulse-icon-256.png', __FILE__ ) );
 }

 public static function migrate_brand_logo() {
  if ( ! current_user_can( 'manage_options' ) || get_option( 'ro_pulse_brand_logo_141', false ) ) { return; }
  // Apply the supplied Pulse brand once; later custom selections remain editable.
  $previous = absint( get_option( 'ro_pulse_logo', 0 ) );
  if ( $previous ) { update_option( 'ro_pulse_logo_before_141', $previous, false ); }
  update_option( 'ro_pulse_logo', 0, false );
  update_option( 'ro_pulse_brand_logo_141', 1, false );
 }

 public static function admin_body_class( $classes ) {
  $screen = get_current_screen();
  return $screen && in_array( $screen->id, array( 'toplevel_page_pulse', 'settings_page_ro-socialbed' ), true ) ? $classes . ' pulse-admin' : $classes;
 }

 public static function settings_menu() {
  add_menu_page( 'Pulse', 'Pulse', 'manage_options', 'pulse', array( __CLASS__, 'settings_page' ), plugins_url( 'assets/pulse-icon-256.png', __FILE__ ), 58 );
  add_options_page( 'Pulse', 'Pulse', 'manage_options', 'ro-socialbed', array( __CLASS__, 'settings_page' ) );
  remove_submenu_page( 'options-general.php', 'ro-socialbed' ); // Keep the legacy URL registered without a duplicate menu entry.
 }

 public static function settings_link( $links ) {
  array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=pulse' ) ) . '">Setări</a>' );
  return $links;
 }

 public static function remove_legacy_footer_settings() {
  if ( current_user_can( 'manage_options' ) && false !== get_option( 'ro_pulse_footer', false ) ) {
   delete_option( 'ro_pulse_footer' );
  }
 }

 public static function settings_register() {
  register_setting( 'ro_socialbed_settings', 'ro_pulse_display', array(
   'type' => 'array', 'default' => array(), 'sanitize_callback' => array( __CLASS__, 'sanitize_display' ),
  ) );
  register_setting( 'ro_socialbed_settings', 'ro_pulse_logo', array(
   'type' => 'integer', 'default' => 0, 'sanitize_callback' => array( __CLASS__, 'sanitize_logo' ),
  ) );
 }

 public static function display() {
  $saved = get_option( 'ro_pulse_display', array() );
  return self::sanitize_display( is_array( $saved ) ? $saved : array() );
 }

 public static function sanitize_display( $value ) {
  $v = is_array( $value ) ? $value : array();
  $result = array();
  foreach ( array( 'width' => array( 320, 800, 550 ), 'gap' => array( 0, 100, 32 ), 'facebook_height' => array( 300, 1600, 680 ) ) as $key => $bounds ) {
   $number = isset( $v[$key] ) && is_scalar( $v[$key] ) && is_numeric( $v[$key] ) ? (int) $v[$key] : $bounds[2];
   $result[$key] = max( $bounds[0], min( $bounds[1], $number ) );
  }
  $result['align'] = isset( $v['align'] ) && in_array( $v['align'], array( 'left', 'center', 'right' ), true ) ? $v['align'] : 'center';
  $result['theme'] = isset( $v['theme'] ) && in_array( $v['theme'], array( 'light', 'dark' ), true ) ? $v['theme'] : 'light';
  $result['source'] = 0;
  return $result;
 }

 public static function sanitize_logo( $value ) {
  if ( ! is_scalar( $value ) ) { $value = -1; }
  if ( '0' === (string) $value || '' === (string) $value ) { return 0; }
  $id = ctype_digit( (string) $value ) ? absint( $value ) : 0;
  if ( $id && wp_attachment_is_image( $id ) && in_array( get_post_mime_type( $id ), array( 'image/png', 'image/jpeg', 'image/webp', 'image/gif', 'image/avif' ), true ) ) { return $id; }
  add_settings_error( 'ro_pulse_logo', 'invalid_logo', 'Alege o imagine PNG, JPG, WebP, GIF sau AVIF din Biblioteca Media.' );
  return absint( get_option( 'ro_pulse_logo', 0 ) );
 }

 public static function settings_page() {
  if ( ! current_user_can( 'manage_options' ) ) { return; }
  echo '<div class="wrap pulse-admin-wrap"><header class="pulse-brand"><img src="' . esc_url( self::logo() ) . '" alt=""><div><h1>Pulse</h1><p>De pe internet culese.</p></div><small>Built by Radu Oncescu<br>with ChatGPT Codex</small></header>';
  echo '<nav class="pulse-tabs" aria-label="Setări Pulse"><a href="#pulse-overview" id="pulse-tab-overview" data-pulse-tab="overview">Prezentare</a><a href="#pulse-display" id="pulse-tab-display" data-pulse-tab="display">Afișare</a><a href="#pulse-identity" id="pulse-tab-identity" data-pulse-tab="identity">Identitate</a></nav>';
  settings_errors();
  echo '<form method="post" action="options.php">';
  settings_fields( 'ro_socialbed_settings' );
  echo '<section id="pulse-overview" class="pulse-panel" data-pulse-panel="overview"><div class="pulse-page-heading"><div><h2>Internetul, într-un singur loc.</h2><p>Gestionează aspectul postărilor integrate în articole.</p></div></div><div class="pulse-stats">';
  foreach ( array( 'Platforme acceptate' => 13, 'Limită linkuri per bloc' => 20 ) as $label => $value ) {
   echo '<div class="pulse-stat"><span>' . esc_html( $label ) . '</span><strong>' . (int) $value . '</strong></div>';
  }
  echo '</div><div class="pulse-card"><h3>Adaugă linkurile. Pulse se ocupă de afișare.</h3><p>În articole, folosește blocul Pulse sau butonul din editorul clasic.</p><p class="description">X · Facebook · Instagram · TikTok · YouTube · Threads · Bluesky · Substack · Reddit · Pinterest · LinkedIn · Mastodon · Telegram</p><p class="description">Sunt necesare postări publice care permit integrarea. YouTube Community Posts nu se afișează. Pulse nu adaugă butoane sau casete de rezervă.</p></div></section>';
  echo '<section id="pulse-identity" class="pulse-panel pulse-card" data-pulse-panel="identity">';
  echo '<h2>Logo</h2><p>Logo-ul apare în blocul Gutenberg și în fereastra de inserare din editorul clasic.</p>';
  echo '<div class="pulse-logo-tile"><img id="rosb-logo-preview" src="' . esc_url( self::logo() ) . '" alt="Logo Pulse" style="display:block;width:100%;height:auto"></div>';
  echo '<input type="hidden" id="rosb-logo-id" name="ro_pulse_logo" value="' . esc_attr( get_option( 'ro_pulse_logo', 0 ) ) . '">';
  echo '<p><button type="button" class="button button-primary" id="rosb-logo-choose">Alege / încarcă logo</button> <button type="button" class="button" id="rosb-logo-reset">Revino la logo-ul implicit</button></p>';
  echo '<p class="description">Recomandat: PNG cu fundal transparent. Imaginea rămâne în Biblioteca Media când revii la logo-ul implicit.</p><p id="rosb-logo-status" role="status" aria-live="polite"></p>';
  echo '</section><section id="pulse-display" class="pulse-panel pulse-card" data-pulse-panel="display">';
  $display = self::display();
  echo '<h2>Afișarea postărilor</h2><table class="form-table" role="presentation">';
  foreach ( array( 'width' => array( 'Lățime maximă', 320, 800 ), 'gap' => array( 'Spațiu între postări', 0, 100 ), 'facebook_height' => array( 'Înălțime Facebook', 300, 1600 ) ) as $key => $field ) {
   echo '<tr><th><label for="rop-' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label></th><td><input type="number" id="rop-' . esc_attr( $key ) . '" name="ro_pulse_display[' . esc_attr( $key ) . ']" min="' . (int) $field[1] . '" max="' . (int) $field[2] . '" value="' . (int) $display[$key] . '" class="small-text"> px</td></tr>';
  }
  echo '<tr><th><label for="rop-align">Aliniere</label></th><td><select id="rop-align" name="ro_pulse_display[align]">';
  foreach ( array( 'left' => 'Stânga', 'center' => 'Centru', 'right' => 'Dreapta' ) as $value => $label ) {
   echo '<option value="' . esc_attr( $value ) . '" ' . selected( $display['align'], $value, false ) . '>' . esc_html( $label ) . '</option>';
  }
  echo '</select></td></tr><tr><th><label for="rop-theme">Tema postărilor X</label></th><td><select id="rop-theme" name="ro_pulse_display[theme]">';
  foreach ( array( 'light' => 'Luminoasă', 'dark' => 'Întunecată' ) as $value => $label ) {
   echo '<option value="' . esc_attr( $value ) . '" ' . selected( $display['theme'], $value, false ) . '>' . esc_html( $label ) . '</option>';
  }
  echo '</select></td></tr></table>';
  echo '<p>Setările de afișare se aplică și postărilor existente pe site. Platformele pot limita lățimea proprie. Limita de inserare rămâne 20 de linkuri per bloc.</p>';
  echo '</section><div class="pulse-savebar"><span class="pulse-save-status" role="status" aria-live="polite">Setările se salvează împreună pentru toate taburile.</span>';
  submit_button( 'Salvează setările', 'primary', 'submit', false );
  echo '</div>';
  echo '</form></div>';
 }

 /** Strict host/path allowlist: no redirects, short links, arbitrary URLs or credentials. */
 public static function normalize( $raw ) {
  if ( ! is_string( $raw ) || strlen( $raw ) > 2048 ) { return false; }
  $parts = wp_parse_url( html_entity_decode( trim( $raw ), ENT_QUOTES, 'UTF-8' ) );
  if ( ! is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['scheme'] ) || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || isset( $parts['port'] ) ) { return false; }
  $host = strtolower( $parts['host'] );
  $path = isset( $parts['path'] ) ? $parts['path'] : '/';
  $query = array();
  if ( isset( $parts['query'] ) ) { parse_str( $parts['query'], $query ); }
  $extra = RO_Pulse_Providers::normalize( $host, $path, $query );
  if ( $extra ) { return $extra; }
  if ( in_array( $host, array( 'x.com', 'www.x.com', 'twitter.com', 'www.twitter.com', 'mobile.twitter.com' ), true ) && preg_match( '~^/(?:[A-Za-z0-9_]+/status|i/web/status)/([0-9]+)(?:/.*)?$~', $path, $m ) ) {
   return array( 'platform' => 'X', 'url' => 'https://twitter.com/i/web/status/' . $m[1] );
  }
  if ( in_array( $host, array( 'reddit.com', 'www.reddit.com', 'old.reddit.com', 'new.reddit.com' ), true ) && preg_match( '~^/r/[A-Za-z0-9_]+/comments/[a-z0-9]+(?:/[^\s<>"\x27]*)?/?$~i', $path ) ) {
   return array( 'platform' => 'Reddit', 'url' => 'https://www.reddit.com' . rtrim( $path, '/' ) . '/' );
  }
  if ( in_array( $host, array( 'threads.net', 'www.threads.net', 'threads.com', 'www.threads.com' ), true ) && preg_match( '~^/(?:@[A-Za-z0-9_.]+/post|t)/[A-Za-z0-9_-]+/?$~', $path ) ) {
   return array( 'platform' => 'Threads', 'url' => 'https://www.threads.com' . rtrim( $path, '/' ) . '/' );
  }
  if ( in_array( $host, array( 'bsky.app', 'www.bsky.app' ), true ) && preg_match( '~^/profile/[A-Za-z0-9.:%_-]+/post/[a-z0-9]+/?$~i', $path ) ) {
   return array( 'platform' => 'Bluesky', 'url' => 'https://bsky.app' . rtrim( $path, '/' ) );
  }
  if ( in_array( $host, array( 'facebook.com', 'www.facebook.com', 'm.facebook.com', 'web.facebook.com' ), true ) ) {
   $valid_path = preg_match( '~^/(?:[A-Za-z0-9._-]+/(?:posts|videos)/[A-Za-z0-9._-]+|(?:reel|share/p|share/v|share/r)/[A-Za-z0-9._-]+)/?$~', $path );
   $extra = array();
   if ( '/permalink.php' === $path && isset( $query['story_fbid'], $query['id'] ) && is_string( $query['story_fbid'] ) && is_string( $query['id'] ) && preg_match( '/^[A-Za-z0-9]+$/', $query['story_fbid'] ) && ctype_digit( $query['id'] ) ) {
    $valid_path = true; $extra = array( 'story_fbid' => $query['story_fbid'], 'id' => $query['id'] );
   }
   if ( in_array( $path, array( '/photo.php', '/photo', '/watch', '/watch/' ), true ) ) {
    $key = 0 === strpos( $path, '/photo' ) ? 'fbid' : 'v';
    if ( isset( $query[$key] ) && is_string( $query[$key] ) && ctype_digit( $query[$key] ) ) { $valid_path = true; $extra = array( $key => $query[$key] ); }
   }
   if ( $valid_path ) {
    $url = 'https://www.facebook.com' . $path;
    if ( $extra ) { $url = add_query_arg( $extra, $url ); }
    return array( 'platform' => 'Facebook', 'url' => $url );
   }
  }
  return false;
 }

 public static function shortcode( $attrs, $content = '' ) {
  $attrs = shortcode_atts( array( 'urls' => '' ), $attrs, 'ro_socialbed' );
  $raw = $attrs['urls'] ? $attrs['urls'] : wp_strip_all_tags( (string) $content );
  return self::render( array( 'urls' => preg_split( '/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY ) ) );
 }

 public static function render( $attrs ) {
  if ( empty( $attrs['urls'] ) || ! is_array( $attrs['urls'] ) ) { return ''; }
  $cards = array(); $seen = array(); $display = self::display();
  foreach ( array_slice( $attrs['urls'], 0, self::LIMIT ) as $raw ) {
   $item = self::normalize( $raw );
   if ( ! $item || isset( $seen[$item['url']] ) ) { continue; }
   $seen[$item['url']] = true;
   $url = esc_url( $item['url'] );
   $embed = '';
   switch ( $item['platform'] ) {
    default:
     $embed = RO_Pulse_Providers::render( $item, $display );
     break;
    case 'Facebook':
     $video = preg_match( '~/(videos|reel|watch|share/v|share/r)(/|\?)~', $item['url'] );
     $src = add_query_arg( array( 'href' => $item['url'], 'show_text' => 'true', 'width' => min( 500, $display['width'] ) ), 'https://www.facebook.com/plugins/' . ( $video ? 'video.php' : 'post.php' ) );
     $embed = '<iframe class="ro-socialbed-facebook" src="' . esc_url( $src ) . '" width="500" height="' . (int) $display['facebook_height'] . '" style="height:' . (int) $display['facebook_height'] . 'px" title="Postare Facebook" loading="lazy" allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>';
     break;
    case 'X':
     preg_match( '~/status/([0-9]+)~', $item['url'], $match );
     $embed = '<div class="ro-socialbed-x-preview" data-rosb-theme="' . esc_attr( $display['theme'] ) . '" data-rosb-tweet-id="' . esc_attr( $match[1] ) . '"></div>';
     wp_enqueue_script( 'ro-socialbed-x-render', plugins_url( 'assets/x.js', __FILE__ ), array(), self::VERSION, true );
     break;
    case 'Reddit':
     $embed = '<blockquote class="reddit-embed-bq" data-embed-showtitle="true"><a class="rop-provider-placeholder" hidden href="' . $url . '"></a></blockquote>';
     wp_enqueue_script( 'ro-socialbed-reddit', 'https://embed.reddit.com/widgets.js', array(), null, true );
     break;
    case 'Threads':
     $embed = '<blockquote class="text-post-media" data-text-post-permalink="' . $url . '" data-text-post-version="0"><a class="rop-provider-placeholder" hidden href="' . $url . '"></a></blockquote>';
     wp_enqueue_script( 'ro-socialbed-threads', 'https://www.threads.com/embed.js', array(), null, true );
     break;
    case 'Bluesky':
     $embed = self::bluesky( $item['url'] );
     if ( ! $embed ) { $embed = RO_Pulse_Providers::fallback( $item ); }
     if ( false !== strpos( $embed, 'blockquote' ) ) { wp_enqueue_script( 'ro-socialbed-bluesky', 'https://embed.bsky.app/static/embed.js', array(), null, true ); }
     break;
   }
   if ( ! $embed ) { continue; }
   $caption = '';
   $cards[] = '<figure class="ro-socialbed-card ro-socialbed-' . esc_attr( strtolower( $item['platform'] ) ) . '"><div class="ro-socialbed-embed">' . $embed . '</div>' . $caption . '</figure>';
  }
  $style = '--rop-width:' . (int) $display['width'] . 'px;--rop-gap:' . (int) $display['gap'] . 'px;';
  return $cards ? '<div class="ro-socialbed-group rop-align-' . esc_attr( $display['align'] ) . '" style="' . esc_attr( $style ) . '">' . implode( '', $cards ) . '</div>' : '';
 }

 private static function bluesky( $url ) {
  $key = 'rosb_' . md5( $url );
  $html = get_transient( $key );
  if ( false !== $html ) { return $html; }
  // Only this fixed endpoint is fetched; URL is a validated query value, never a request host.
  $response = wp_safe_remote_get( add_query_arg( array( 'url' => $url, 'maxwidth' => 550 ), 'https://embed.bsky.app/oembed' ), array( 'timeout' => 3, 'redirection' => 0, 'limit_response_size' => 100000 ) );
  $html = '';
  if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
   $data = json_decode( wp_remote_retrieve_body( $response ), true );
   if ( is_array( $data ) && isset( $data['html'] ) && is_string( $data['html'] ) ) {
    // Scripts and event handlers from provider responses are never accepted.
    $raw = preg_replace( '~<script\b[^>]*>.*?</script>~is', '', $data['html'] );
    $html = wp_kses( $raw, array(
     'blockquote' => array( 'class' => true, 'data-bluesky-uri' => true, 'data-bluesky-cid' => true, 'data-bluesky-embed-color-mode' => true ),
     'p' => array( 'lang' => true ), 'a' => array( 'href' => true, 'rel' => true ), 'br' => array(),
    ) );
   }
  }
  set_transient( $key, $html, $html ? DAY_IN_SECONDS : 5 * MINUTE_IN_SECONDS );
  return $html;
 }
}
RO_SocialBed::init();
