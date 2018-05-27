# Pixel Plugin for [Flextype](http://flextype.org/)
![version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg?style=flat-square "Version")
![Flextype](https://img.shields.io/badge/Flextype-0.x-green.svg?style=flat-square "Flextype Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/flextype-plugins/pixel/blob/master/LICENSE.txt)

Pixel plugin to create placeholder images for Flextype

## Installation
1. Unzip plugin to the folder `/site/plugins/`
2. Go to `/site/config/site.yaml` and add plugin name to plugins section.
3. Save your changes.

Example:
```
plugins:
  - pixel
```

## Usage in page content

Simple usage

```
![Image]([pixel])
```

Set width and height

```
![Image]([pixel width=200 height=200])
```

Set category name

```
![Image]([pixel width=200 height=200 category=city])
```

Set gray filter

```
![Image]([pixel width=200 height=200 category=city gray=true])
```

Set text

```
![Image]([pixel width=200 height=200 category=city gray=true text='Pixel'])
```

## Usage in template
```
<img src="<?php echo pixel(['width' => 200, 'height' => 200, 'category' => 'city', 'gray' => 'true', 'text' => "Pixel"]); ?>" alt="">
```

## Options

| name  | value | description |
|---|---|---|
| enabled | true | or `false` to disable the plugin |
| width | 300 | Image width |
| height | 200 | Image height |
| category | sports | Image category (abstract, animals, business, cats, city, food, night, life, fashion, people, nature, sports, technics, transport) |
| text | '' | Image text |

## License
See [LICENSE](https://github.com/flextype-plugins/pixel/blob/master/LICENSE)
