# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0] - 2023-03-25

### Fixed

- switch page before `bmf_chapter_nav` is called (chapter page)
- summary accordion on mobile
- if image error and `bmv_rgx_cdn` is match (lazyLoad)
- `.disqus-trigger` clicked more than once on chapter page
- shortlink not found on series page (api)
- detect settings value changed
- ch. visited & cover on chapter page
- other minor bug fixes and improvements.

### Added

- cache mechanism
- new source `maid.my.id` (koidezign)
- new source `neumanga.net` (koidezign)
- new source `mgkomik.com` (madara)
- new source `shinigami.id` (madara)
- user id (uid) on profile page
- `.f_grow.f_clamp` css (force line-clamp)
- last read on bookmark page
- load images with `lazyLoadQueue`
- `url` and `source` on `hs_visited` (history)
- `m-delete-all` button on bookmark and history page
- alternative titles on series page
- move auto load next image to `lazyLoad` (chapter)
- firebase [app check](https://firebase.google.com/docs/app-check)
- css `aspect-ratio` property on `.post-list .cover img`
- keys and new icon on `manifest.json`
- `l10n` on member page
- auto select menu based on url
- new cdn, `imagecdn.app` and `imageoptim.com`
- `system` mode on `theme-switch`
- `completed` status on latest, search, and member page
- `default` search on advanced search
- `hiatus` status on advanced search
- show `#back-to` on chapter page
- `goto` input on bookmark and history page
- daily backup firebase data to server
- captcha to contact form
- `reset all settings` button on settings page
- notification area
- ecmascript 2015 (es6) cross-browser detection
- load next image afer 5 seconds on lazyload

### Changed

- `position` to `display` on `.t_perfect` (mobile)
- `bacamanga.org` to `mangatale.co`
- `tukangkomik.com` to `tukangkomik.id`
- `event handlers` to `event listeners`
- komiklab selector api
- `keyCode` to `keyEvent`
- svg icon to [`icon sets`](https://github.com/iconify/icon-sets) by Iconify
- update `l10n` text

### Removed

- cache data from `sessionStorage`


## [1.1] - 2023-04-08

### Added

- lazyload: disable skip image if current page is chapter
- lazyload: get image dimensions before image has fully loaded
- lazyload: current image loading info on chapter page (LZ)
- connection-notif: show/hide click on chapter page


## [1.2] - 2023-04-16

### Fixed

- minor bug fixes and improvements.

### Added

- lazyload: load `single` image directly


## [1.3] - 2023-07-05

### Fixed

- api: slug on latest & search page
- api: path on search page
- search: if value is not lowercase
- series: chapter number
- `DOMDocument loadHTML` not encoding `UTF-8` correctly
- minor bug fixes and improvements.

### Added

- get `slug` from title on series page
- `reset button` for zoom & load image on chapter page
- `slug_alt` to bookmark data
- `author` and `artist` in series page
- settings: reset with `type` confirm
- bypass `cloudflare` with [ScrapingAnt](https://scrapingant.com/)
- new source `pojokmanga.net` (madara)
- new source `kiryuu.id` (themesia)
- `headers` and `body` to error data

### Changed

- api: `shinigami.id` (madara)
- api: `komikcast.site` (enduser)
- `komikcast.site` to `komikcast.io`

### Removed

- `komiklab.com` (themesia)


## [1.4] - 2023-11-25

### Fixed

- api: remove unnecessary text from `ch_num`
- series: image cover empty
- `.clear-cache` timer doesn't work on [Via Browser](https://play.google.com/store/apps/details?id=mark.via.gp&hl=en&gl=US) if `Go back without reloading` is enabled
- bmhs: hide pagination if search result is zero (empty)
- profile: change password
- adv search: css `box-shadow`
- css loading icon `fixed` middle screen
- keyevent: regexp escape
- numerous bug fixes and improvements.

### Added

- request header `X-Requested-With` to `loadXMLDoc`
- search: encode query string
- resize image with [Image Resize API](https://github.com/falconshark/image-resize-api)
- compress image with [reSmush.it](https://resmush.it/)
- bypass with [WebScraping.AI](https://webscraping.ai/)
- bypass with [Zenscrape](https://zenscrape.com/)
- new source `komikindo.co` (themesia)

### Changed

- api: `bacakomik.me` (eastheme)
- `bacakomik.co` to `bacakomik.me`
- `shinigami.id` to `shinigami.sh`
- `mgkomik.com` to `mgkomik.id`
- chapter `keyEvent (prev/next)` only for desktop

### Removed

- `manhwaindo.org` (eastheme)


## [1.5] - 2024-02-14

### Fixed

- api: CORS (Cross-Origin Resource Sharing)
- api: `cover` image empty (kiryuu)
- api: advanced search
- api: series element selector for `author` komikcast
- load `http` image from [reSmush.it](https://resmush.it/) with [wsrv.nl](https://github.com/weserv/images) (mixed content warning)
- chapter: next/prev different slug
- series: bookmark same title
- numerous bug fixes and improvements.

### Added

- &lt;img&gt; `referrerpolicy` attribute
- api: image `attr_alt`
- new source `klikmanga.com` (madara)
- new source `leviatanscans.com` (madara)
- new source `reaper-scans.com` (themesia)
- check password security with [Have I Been Pwned? (HIBP)](https://haveibeenpwned.com/API/v3#PwnedPasswords)
- firebase: remove user data from [storage](https://firebase.google.com/docs/storage/web/start#web-namespaced-api), if user account deleted
- lazyload: load an image with a specific [referer](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referer)
- api: detect source domain name changed
- add `timeout` property to `loadXMLDoc`
- show history url based on source site

### Changed

- api: web scraper with [hQuery.php](https://github.com/duzun/hQuery.php)
- firebase `8.10.0` to [`8.10.1`](https://firebase.google.com/support/release-notes/js#version_8101_-_january_28_2022)
- firebase storage [security rules `read` and `write`](https://firebase.google.com/docs/rules/rules-and-auth#:~:text=users%20can%20only%20read%20and%20write%20their%20own%20data)
- `bacakomik.me` to `bacakomik.net`
- `komikcast.io` to `komikcast.lol`
- `shinigami.sh` to `shinigami.moe`
- `pojokmanga.net` to `pojokmanga.id`

### Removed

- `neumanga.net` (koidezign)


## [1.6] - 2024-10-22

### Fixed

- image-resize: failed to detect image MIME type
- lazyload: `bmv_lazy_referer` is null on bmhs page
- api: `Http` class return new instances instead of overwriting
- api: `DOMDocument::loadHTML` treats the string as ISO-8859-1
- api: `$title` regexp escape
- `apple-mobile-web-app-capable` is [deprecated](https://web.dev/learn/pwa/web-app-manifest#designing_your_pwa_experience)
- numerous bug fixes and improvements.

### Added

- css: `.bg3`
- compatible with PHP 8.x
- merge duplicate series on `bmhs`
- resize and adjust image quality with [IMGPA](https://github.com/sekedus/imgpa)
- disable directory listing with `.htaccess`
- new source `komiknesia.xyz` (themesia)
- new source `komiklovers.com` (themesia)
- new source `komiku.id`
- new source `webtoons.com`
- new source `ikiru.id` (themesia)
- new source `manhuaus.com` (madara)

### Changed

- UAParser.js source from [`jsdelivr`](https://www.jsdelivr.com/package/npm/ua-parser-js) to [`cdnjs`](https://cdnjs.com/libraries/UAParser.js)
- Update regexp: `chapter`
- string `indexOf` to `includes`
- api: `reaper-scans.com` (themesia)
- api: `leviatanscans.com` (madara)
- `pojokmanga.id` to `pojokmanga.org`
- `komikcast.lol` to `komikcast.cz`
- `shinigami.moe` to `shinigami.ae`
- `kiryuu.id` to `kiryuu.org`
- `komikindo.co` to `komiksin.co`

### Removed

- `mangatale.co` (themesia)


## [1.7] - 2025-06-23

### Fixed

- domain change detection
- api: webtoons search result
- series: visible content shifts when an accordion is displayed
- chapter: disqus identifier
- bmhs: pagination on mobile
- reSmush.it API: `user-agent and website address as referer in API request is mandatory`
- numerous bug fixes and improvements.

### Added

- hQuery: igonore SSL certificate verification with `stream_socket_client`, [source](https://stackoverflow.com/a/43800970)
- bypass `cloudflare` with [Cloudflare Proxy EX](https://github.com/1234567Yang/cf-proxy-ex)
- `released` in series page
- new source `lumoskomik.com` (madara)
- new source `mangapark.net`
- new source `softkomik.com`
- new source `cosmictoon.ae` (themesia)
- new source `manhwalist.com` (themesia)
- new source `ainzscans.net` (themesia)
- new source `soulscans.my.id` (themesia)
- new source `mangasee123.com`
- new source `comick.io`
- new source `westmanga.fun` (themesia)
- new source `komikstation.co` (themesia)

### Changed

- `l10n` on meta-tags
- api: `pojokmanga.com` (madara)
- api: `webtoons.com`
- api: `reaper-scans.com` themesia to madara
- `pojokmanga.org` to `pojokmanga.com`
- `komikcast.lol` to `komikcast.com`
- `kiryuu.id` to `kiryuu.co`
- `klikmanga.id` to `klikmanga.com`
- `tukangkomik.id` to `tukangkomik.com`
- `bacakomik.net` to `bacakomik.me`
- `mgkomik.id` to `mgkomik.my.id`
- `komiksin.co` to `komiksin.ae`

### Removed

- `komiknesia.xyz` (themesia)
- [Image Resize API](https://github.com/bakomon/web/tree/0a626ee9ba8e24723e0c06ed3f371618934f42bb/tools/image-resize)
- Statically CDN
