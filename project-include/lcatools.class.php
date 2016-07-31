<?php
/**
 * Classes used specifically within the lcaTools system as a library for common functions 
 *
 *
 *
 * @file
 * @author James Alexander
 * @copyright © 2016 James Ryan Alexander unless otherwise noted in comments
 * @license MIT at http://opensource.org/licenses/MIT and LICENSE.txt which should be in the root folder of this software
 * @version 1.0 - 2016-07-30
 */

date_default_timezone_set( 'UTC' );

class lcatools {

	/**
	 * function to return a wmfurl
	 *
	 * Function requires a project (and will only accept a wmf project) and will accept a language (if needed, assumes English if it needs one and doesn't have one passed) and page (more specifically 'content' the content that should come after the first /)
	 *
	 * @param string $project the wmf project you want a url for.
	 * @param string $lang (optional) the language (if needed) also defaults itself to be used as $page if the project can't take a language.
	 * @param string $page (optional) content to go after the first / in the url
	 *
	 */

	public static function wmfurl( $project,  $lang = null,  $page = null) {

		$url = null;

		if ( strtolower( $project ) == 'wikipedia' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikipedia.org/wiki/';

			} else {

				$url = 'https://en.wikipedia.org/wiki/';

			}

		} elseif ( strtolower( $project ) == 'commons' || strtolower( $project ) == 'wikimedia commons' ) {

			$url = 'https://commons.wikimedia.org/wiki/';

			if ( $lang ) {

				// fall 2nd variable back to page since project doesn't take language
				$page = $lang;

			}
		} elseif ( strtolower( $project ) == 'wikidata' ) {

			$url = 'https://www.wikidata.org/wiki/';

			if ( $lang ) {

				// fall 2nd variable back to page since project doesn't take language
				$page = $lang;

			}
		} elseif ( strtolower( $project ) == 'wikibooks' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikibooks.org/wiki/';

			} else {

				$url = 'https://en.wikibooks.org/wiki/';

			}
		} elseif ( strtolower( $project ) == 'meta' ) {

			$url = 'https://meta.wikimedia.org/wiki/';

			if ( $lang ) {

				// fall 2nd variable back to page since project doesn't take language
				$page = $lang;

			}
		} elseif ( strtolower( $project ) == 'wikiquote' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikiquote.org/wiki/';

			} else {

				$url = 'https://en.wikiquote.org/wiki/';

			}
		} elseif ( strtolower( $project ) == 'wikispecies' ) {

			$url = 'https://species.wikimedia.org/wiki/';

			if ( $lang ) {

				// fall 2nd variable back to page since project doesn't take language
				$page = $lang;

			}
		} elseif ( strtolower( $project ) == 'wikivoyage' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikivoyage.org/wiki/';

			} else {

				$url = 'https://en.wikivoyage.org/wiki/';

			}
		} elseif ( strtolower( $project ) == 'wiktionary' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wiktionary.org/wiki/';

			} else {

				$url = 'https://en.wiktionary.org/wiki/';

			}
		} elseif ( strtolower( $project ) == 'wikiversity' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikiversity.org/wiki/';

			} else {

				$url = 'https://en.wikiversity.org/wiki/';

			}
		} elseif ( strtolower( $project ) == 'wikisource' ) {

			if ( $lang ) {

				$url = 'https://'.$lang.'.'.'wikisource.org/wiki/';

			} else {

				// fall back to wikisource.org instead of en since it exists
				$url = 'https://wikisource.org/wiki/';

			}
		} else {

			return 'error, no known project given';
		}

		if ( $page ) {

			$url .= $page;

		}

		return $url;
	}

}
