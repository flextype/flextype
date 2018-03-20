# Redirect Plugin for [Flextype](http://flextype.org/)
![version](https://img.shields.io/badge/version-1.0.1-brightgreen.svg?style=flat-square "Version")
![Flextype](https://img.shields.io/badge/Flextype-0.x-green.svg?style=flat-square "Flextype Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/flextype-plugins/redirect/blob/master/LICENSE.txt)

Simple redirect plugin for Flextype. It allows you to create a simple redirects for your pages.

## Installation
1. Unzip plugin to the folder `/site/plugins/`
2. Go to `/site/config/site.yml` and add plugin name to plugins section.
3. Save your changes.

Example:
```
...
plugins:
  - redirect
```

## Usage in the pages
You can simple add new redirect in the frontmatter of your page
```
redirect: new-page
```

## Create several redirects
This plugins allows you to setup several redirects, just go to the `/site/config/site.yml` and create section with your redirects.
```
redirects:
  old-page: new-page
  another-old-page: https://www.google.com
```

## License
[MIT](https://github.com/flextype-plugins/redirect/blob/master/LICENSE.txt)
