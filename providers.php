<?php
defined( 'ABSPATH' ) || exit;
final class RO_Pulse_Providers {
 private static function public_host( $host ) {
  return strlen( $host ) <= 253 && preg_match( '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $host ) && ! preg_match( '/\.(?:local|localhost|internal|test|invalid)$/', $host );
 }
 public static function normalize( $host, $path, $query ) {
  $id = null;
  if ( in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtu.be', 'www.youtube-nocookie.com' ), true ) ) {
   if ( in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com' ), true ) ) {
    if ( preg_match( '~^/post/(Ug[A-Za-z0-9_-]{8,198})/?$~', $path, $m ) ) { return array( 'platform' => 'YouTube', 'url' => 'https://www.youtube.com/post/' . $m[1] ); }
    if ( isset( $query['lb'] ) && is_string( $query['lb'] ) && preg_match( '/^Ug[A-Za-z0-9_-]{8,198}$/', $query['lb'] ) && preg_match( '~^/(?:@[A-Za-z0-9_.-]+|channel/[A-Za-z0-9_-]+)/(?:community|posts)/?$~', $path ) ) { return array( 'platform' => 'YouTube', 'url' => 'https://www.youtube.com/post/' . $query['lb'] ); }
   }
   if ( in_array( $host, array( 'youtu.be', 'www.youtu.be' ), true ) ) { $id = substr( $path, 1 ); }
   elseif ( '/watch' === $path && isset( $query['v'] ) && is_string( $query['v'] ) ) { $id = $query['v']; }
   elseif ( preg_match( '~^/(?:shorts|embed|live)/([A-Za-z0-9_-]{11})/?$~', $path, $m ) ) { $id = $m[1]; }
   if ( is_string( $id ) && preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ) { return array( 'platform' => 'YouTube', 'url' => 'https://www.youtube.com/watch?v=' . $id ); }
  }
  if ( in_array( $host, array( 'tiktok.com', 'www.tiktok.com', 'm.tiktok.com' ), true ) && preg_match( '~^/@[A-Za-z0-9_.]+/(?:video|photo)/(\d+)/?$~', $path ) ) { return array( 'platform' => 'TikTok', 'url' => 'https://www.tiktok.com' . rtrim( $path, '/' ) ); }
  if ( in_array( $host, array( 'instagram.com', 'www.instagram.com', 'm.instagram.com' ), true ) && preg_match( '~^/(?:(?!share/)[A-Za-z0-9_.]+/)?(p|reel|reels|tv)/([A-Za-z0-9_-]+)/?$~', $path, $m ) ) { return array( 'platform' => 'Instagram', 'url' => 'https://www.instagram.com/' . ( 'reels' === $m[1] ? 'reel' : $m[1] ) . '/' . $m[2] . '/' ); }
  if ( in_array( $host, array( 'pinterest.com', 'www.pinterest.com' ), true ) && preg_match( '~^/pin/(\d+)/?$~', $path, $m ) ) { return array( 'platform' => 'Pinterest', 'url' => 'https://www.pinterest.com/pin/' . $m[1] . '/' ); }
  if ( in_array( $host, array( 'linkedin.com', 'www.linkedin.com' ), true ) ) {
   if ( preg_match( '~^/(?:embed/)?feed/update/urn:li:(activity|share|ugcPost):(\d+)/?$~', $path, $m ) ) { return array( 'platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/feed/update/urn:li:' . $m[1] . ':' . $m[2] ); }
   if ( preg_match( '~^/posts/[^/]+-(activity|share|ugcPost)-(\d+)-[A-Za-z0-9_-]+/?$~', $path, $m ) ) { return array( 'platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/feed/update/urn:li:' . $m[1] . ':' . $m[2] ); }
  }
  if ( in_array( $host, array( 't.me', 'telegram.me', 'www.t.me' ), true ) && preg_match( '~^/(?:s/)?([A-Za-z][A-Za-z0-9_]{3,})/(\d+)/?$~', $path, $m ) ) { return array( 'platform' => 'Telegram', 'url' => 'https://t.me/' . $m[1] . '/' . $m[2] ); }
  if ( in_array( $host, array( 'substack.com', 'www.substack.com', 'open.substack.com' ), true ) ) {
   if ( preg_match( '~^/(?:home/post/p-|@[A-Za-z0-9_.-]+/p-|inbox/post/)(\d+)/?$~', $path, $m ) ) { return array( 'platform' => 'Substack', 'url' => 'https://substack.com/home/post/p-' . $m[1] ); }
   if ( preg_match( '~^/pub/([A-Za-z0-9-]+)/p/([A-Za-z0-9_-]+)/?$~', $path, $m ) ) { return array( 'platform' => 'Substack', 'url' => 'https://' . strtolower( $m[1] ) . '.substack.com/p/' . $m[2] ); }
   if ( preg_match( '~^/(?:(?:@[A-Za-z0-9_.-]+|profile/[A-Za-z0-9_-]+)/)?note/c-\d+/?$~', $path ) ) { return array( 'platform' => 'Substack', 'url' => 'https://substack.com' . rtrim( $path, '/' ) ); }
  }
  if ( preg_match( '/^(?:(?:www|m|mobile|old|new)\.)?(?:x\.com|twitter\.com|facebook\.com|reddit\.com|threads\.(?:com|net)|instagram\.com|tiktok\.com|linkedin\.com|pinterest\.com|youtube\.com|bsky\.app)$/', $host ) ) { return false; }
  if ( self::public_host( $host ) && preg_match( '~^/p/[A-Za-z0-9_-]+/?$~', $path ) ) { return array( 'platform' => 'Substack', 'url' => 'https://' . $host . rtrim( $path, '/' ) ); }
  if ( self::public_host( $host ) && preg_match( '~^/@[A-Za-z0-9_.-]+/\d+/?$~', $path ) ) { return array( 'platform' => 'Mastodon', 'url' => 'https://' . $host . rtrim( $path, '/' ) ); }
  return false;
 }
 private static function frame( $src, $platform, $class, $height = 600 ) {
  return '<iframe class="rop-frame ' . esc_attr( $class ) . '" src="' . esc_url( $src ) . '" width="550" height="' . (int) $height . '" title="Postare ' . esc_attr( $platform ) . '" loading="lazy" allow="fullscreen; picture-in-picture; encrypted-media" allowfullscreen></iframe>';
 }
 public static function fallback( $item ) { return ''; }
 public static function render( $item, $display ) {
  $url = $item['url']; $safe = esc_url( $url );
  switch ( $item['platform'] ) {
   case 'YouTube':
    if ( false !== strpos( $url, '/post/' ) ) { return self::fallback( array( 'platform' => 'YouTube Posts', 'url' => $url ) ); }
    parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $q );
    return self::frame( 'https://www.youtube-nocookie.com/embed/' . $q['v'] . '?autoplay=0&rel=0', 'YouTube', 'rop-youtube', 310 );
   case 'TikTok':
    preg_match( '~/(\d+)$~', $url, $m );
    return self::frame( 'https://www.tiktok.com/player/v1/' . $m[1] . '?autoplay=0&description=1&music_info=1&rel=0', 'TikTok', 'rop-tiktok', 700 );
   case 'Instagram':
    wp_enqueue_script( 'ro-pulse-instagram-render', plugins_url( 'assets/instagram.js', __FILE__ ), array(), RO_SocialBed::VERSION, true );
    return '<div class="rop-instagram-host" data-rop-instagram-url="' . $safe . '">' . self::fallback( $item ) . '</div>';
   case 'Pinterest':
    wp_enqueue_script( 'ro-pulse-pinterest', 'https://assets.pinterest.com/js/pinit.js', array(), null, true );
    return '<a data-pin-do="embedPin" data-pin-width="large" href="' . $safe . '">Pinterest</a>';
   case 'LinkedIn':
    return self::frame( str_replace( '/feed/update/', '/embed/feed/update/', $url ) . '?collapsed=1', 'LinkedIn', 'rop-linkedin', 600 );
   case 'Substack':
    $attr = false !== strpos( $url, '/note/' ) ? 'data-comment-link' : 'data-post-link';
    wp_enqueue_script( 'ro-pulse-substack', 'https://substack.com/embedjs/embed.js', array(), null, true );
    return '<div class="substack-post-embed"><a class="rop-provider-placeholder" hidden ' . $attr . ' href="' . $safe . '"></a></div>';
   case 'Telegram':
    wp_enqueue_script( 'ro-pulse-widgets', plugins_url( 'assets/widgets.js', __FILE__ ), array(), RO_SocialBed::VERSION, true );
    return '<div class="rop-telegram" data-rop-telegram="' . esc_attr( ltrim( wp_parse_url( $url, PHP_URL_PATH ), '/' ) ) . '">' . self::fallback( $item ) . '</div>';
   case 'Mastodon':
    wp_enqueue_script( 'ro-pulse-widgets', plugins_url( 'assets/widgets.js', __FILE__ ), array(), RO_SocialBed::VERSION, true );
    $html = self::frame( $url . '/embed', 'Mastodon', 'rop-mastodon', 450 );
    return str_replace( ' loading="lazy"', ' sandbox="allow-scripts allow-same-origin allow-popups" loading="lazy"', $html );
  }
  return '';
 }
}
