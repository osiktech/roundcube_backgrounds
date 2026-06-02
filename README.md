# Roundcube Backgrounds

Roundcube plugin that shows a full-page wallpaper on the **login** and **logout** screens.

## Features

- **Monthly** — uses `01.jpg` … `12.jpg` (or `january.jpg`, `february.jpg`, …); falls back to cycling images in folder order
- **Random** — picks a random image on each page load
- **Fixed** — always shows one chosen image

All options are configured in `config.inc.php` only.

## Installation

1. Copy the plugin folder into your Roundcube `plugins/` directory as `plugins/roundcube_backgrounds/`.
2. Enable the plugin in `config/config.inc.php`:

   ```php
   $config['plugins'] = ['roundcube_backgrounds'];
   ```

3. Copy the sample config and adjust:

   ```bash
   cp plugins/roundcube_backgrounds/config.inc.php.dist plugins/roundcube_backgrounds/config.inc.php
   ```

4. Add wallpaper images to `plugins/roundcube_backgrounds/assets/` (jpg, png, webp, gif, or svg).

## Configuration (`config.inc.php`)

| Option | Description |
|--------|-------------|
| `roundcube_backgrounds_mode` | `monthly`, `random`, or `fixed` |
| `roundcube_backgrounds_fixed` | Filename for fixed mode |
| `roundcube_backgrounds_path` | Custom image directory (optional) |
| `roundcube_backgrounds_size` | CSS `background-size` (default: `cover`) |
| `roundcube_backgrounds_position` | CSS `background-position` |
| `roundcube_backgrounds_repeat` | CSS `background-repeat` |
| `roundcube_backgrounds_attachment` | CSS `background-attachment` |

## Monthly naming examples

```
assets/images/01.jpg  … images/12.jpg
assets/images/january.jpg  … images/december.jpg
```

If no month-specific file exists, the plugin uses image index `(month - 1) % number_of_images`.

## Compatibility

- **Roundcube 1.7+** — uses standard plugin hooks (`startup`, `render_page`) unchanged in 1.7
- **PHP 8.1+** — required by Roundcube 1.7
- **Elastic skin** — login template and `#layout-content` selectors are unchanged in 1.7
- Deploy with **`public_html/`** as the web root; plugin images under `plugins/roundcube_backgrounds/assets/images` are served via `static.php`

## License

- Code: GNU GPLv3+
- Pictures are licensed under CC0/Public Domain
  - 01.jpg https://www.goodfreephotos.com/italy/other-italy/high-mountains-of-the-alps.jpg.php
  - 02.jpg https://wallpaperscraft.com/download/forest_snow_trees_1561953/3840x2160
  - 03.jpg https://pixnio.com/nature-landscapes/mountain/snow-mountains-austria-alps-europe
  - 04.jpg https://pixnio.com/nature-landscapes/spring/sprig-pink-plum-flowers-blossoms
  - 05.jpg https://pixnio.com/nature-landscapes/meadows/field-meadow-flower-wildflower-mountain
  - 06.jpg https://pixnio.com/nature-landscapes/meadows/flowers-grass-nature-field-meadow
  - 07.jpg https://pixnio.com/media/lake-dark-red-boat-river-boat-trees
  - 08.jpg https://wallpaperscraft.com/download/grasses_dusk_lake_186359/3840x2160
  - 09.jpg https://wallpaperscraft.com/download/mountain_lake_bridge_901963/3840x2160
  - 10.jpg https://pixnio.com/nature-landscapes/lake/reflection-tree-dock-forest-nature-lake
  - 11.jpg https://pixnio.com/nature-landscapes/lake/landscape-water-mountain-nature-sea
  - 12.jpg https://pixnio.com/media/shadow-silhouette-snow-forest-backlight