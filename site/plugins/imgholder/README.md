# Imgholder Plugin for [Flextype](http://flextype.org/)
![version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg?style=flat-square "Version")
![Flextype](https://img.shields.io/badge/Flextype-0.x-green.svg?style=flat-square "Flextype Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/flextype-plugins/imgholder/blob/master/LICENSE.txt)

Imgholder plugin to create placeholder images for Flextype

## Installation
1. Unzip plugin to the folder `/site/plugins/`
2. Go to `/site/config/site.yaml` and add plugin name to plugins section.
3. Save your changes.

Example:
```
plugins:
  - imgholder
```

## Usage in page content

Simple usage

```
![Image]([imgholder])
```

Set width and height

```
![Image]([imgholder width=200 height=200])
```

Set text color

```
![Image]([imgholder width=200 height=200 text_color=white])
```

Set background color

```
![Image]([imgholder width=200 height=200 text_color=white bg_color=black])
```

Set text

```
![Image]([imgholder width=200 height=200 text_color=white bg_color=black text_color=white text='Pixel'])
```

Set font name

```
![Image]([imgholder width=200 height=200 text_color=white bg_color=black text_color=white text='Pixel' font_name=roboto])
```

Set font size

```
![Image]([imgholder width=200 height=200 text_color=white bg_color=black text_color=white text='Pixel' font_name=roboto font_size=12])
```

Set image extension

```
![Image]([imgholder width=200 height=200 text_color=white bg_color=black text_color=white text='Pixel' font_name=roboto font_size=12 ext="png"])
```

## Usage in template
```
<img src="<?php echo imgholder(['width' => 200, 'height' => 200, 'text_color' => 'white', 'bg_color' => 'black' 'text_color' => 'white', 'text' => 'Imgholder', 'font_name' => 'roboto', 'font_size' => 12, ext => 'png']); ?>" alt="">
```

## Options

| name  | value | description |
|---|---|---|
| enabled | true | or `false` to disable the plugin |
| width | 300 | Image width |
| height | 200 | Image height |
| text_color | white | Image text color |
| bg_color | white | Image bg color |
| text | Imgholder | Image text |
| font_name | Imgholder | Image font name (roboto, arial, bebas, bitter, corki, debby, fashon fetish, gtw, kelson, matias, ptsans, ptsans italic, ptserif, robotoslab, tahoma) |
| font_size | 12 | Image font size |
| ext | png | Image extension |

## License
See [LICENSE](https://github.com/flextype-plugins/imgholder/blob/master/LICENSE)
