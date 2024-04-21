<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_options;
# code ads click
function foxtool_adsclick_footer(){
	global $foxtool_options;
	if (isset($foxtool_options['ads-click1']) && !empty($foxtool_options['ads-click11']) && !current_user_can('administrator')){	
	$mini = isset($foxtool_options['ads-click-c1']) ? '"left=2000,top=2000,width=200,height=100,location=no,toolbar=no,menubar=no,scrollbars=no,resizable=no"' : NULL;
	$hau = !empty($foxtool_options['ads-click-c2']) ? $foxtool_options['ads-click-c2'] : 24;
	?>
	<script>
	(function() {
		var links = [<?php 
			$lislink = $foxtool_options['ads-click11'];
			$lislink = explode("\n", str_replace("\r", "",  $lislink));
			foreach ($lislink as $link){
				echo "'". $link ."',";
			}
		?>];
		function setCookie(domain) {
			var expires = new Date();
			expires.setTime(expires.getTime() + (<?php echo $hau; ?> * 60 * 60 * 1000)); 
			document.cookie = "adsclick_" + domain + "=1; expires=" + expires.toUTCString() + "; path=/";
		}
		function isDomainInCookies(domain) {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i].trim();
				if (cookie.startsWith("adsclick_" + domain + "=")) {
					return true;
				}
			}
			return false;
		}
		function isCookieExpired(cookie) {
			var parts = cookie.split('=');
			var cookieName = parts[0].trim();
			var cookieValue = parts[1];
			var expiryDate = new Date(cookieValue);
			return expiryDate < new Date(); 
		}
		function AffClickHandler(event) {
			for (var i = 0; i < links.length; i++) {
				var newLink = links[i];
				var parser = document.createElement('a');
				parser.href = newLink;
				var domain = parser.hostname + parser.pathname; 
				if (!isDomainInCookies(domain) || isCookieExpired("adsclick_" + domain)) {
					setCookie(domain);
					var newWindow = window.open(newLink, '_blank', <?php echo $mini; ?>);
					break; 
				}
			}
			<?php if(isset($foxtool_options['ads-click-c3']) && $foxtool_options['ads-click-c3'] == 'Link'){ ?>
			document.querySelectorAll('a, button').forEach(function(element) {
				element.removeEventListener('click', AffClickHandler);
			});
			<?php } else { ?>
			document.removeEventListener('click', AffClickHandler);
			<?php } ?>
		}
		setTimeout(function() {
			<?php if(isset($foxtool_options['ads-click-c3']) && $foxtool_options['ads-click-c3'] == 'Link'){ ?>
			document.querySelectorAll('a, button').forEach(function(element) {
				element.addEventListener('click', AffClickHandler);
			});
			<?php } else { ?>
			document.addEventListener('click', AffClickHandler);
			<?php } ?>
			
		}, 1000); 
	})();
	</script>
	<?php
	}
}
add_action('wp_footer', 'foxtool_adsclick_footer');