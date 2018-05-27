# Maintenance Plugin for [Flextype](http://flextype.org/)
![version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg?style=flat-square "Version")
![Flextype](https://img.shields.io/badge/Flextype-0.x-green.svg?style=flat-square "Flextype Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/flextype-plugins/maintenance/blob/master/LICENSE.txt)

Maintenance plugin allow you to close the website for maintenance.

## Installation
1. Unzip plugin to the folder `/site/plugins/`
2. Go to `/site/config/site.yaml` and add plugin name to plugins section.
3. Save your changes.

Example:
```
...
plugins:
  - maintenance
```

## Settings

```yaml
enabled: true # or `false` to disable the plugin
activated: true # or `false` to deactivate the maintenance mode
msg_title: "" # Title
msg_description: "" # Description
bg_img: "" # Background image

```

## License
See [LICENSE](https://github.com/flextype-plugins/maintenance/blob/master/LICENSE)
