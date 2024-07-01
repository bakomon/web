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
