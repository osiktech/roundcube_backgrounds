<?php

/**
 * Roundcube login/logout wallpaper plugin
 *
 * @license GNU GPLv3+
 */

class roundcube_backgrounds extends rcube_plugin
{
    public $task = 'login|logout';

    private const MODES = ['monthly', 'random', 'fixed'];
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    private const ASSETS_SUBDIR = 'assets/images';

    /** @var rcube */
    private $rc;

    #[\Override]
    public function init()
    {
        $this->rc = rcube::get_instance();

        $this->load_config();

        $this->add_hook('startup', [$this, 'startup']);
    }

    /**
     * Register render_page only for login/logout HTML responses.
     */
    public function startup($args)
    {
        if (($args['task'] ?? '') === 'login' || ($args['task'] ?? '') === 'logout') {
            $this->add_hook('render_page', [$this, 'render_page']);
        }

        return $args;
    }

    /**
     * Attach dynamic stylesheet on login and logout pages.
     */
    public function render_page($args)
    {
        $template = $args['template'] ?? '';

        if ($template !== 'login' && $template !== 'logout') {
            return $args;
        }

        $image = $this->resolve_wallpaper();
        if (!$image) {
            return $args;
        }

        $url = $this->image_url($image);
        $size = $this->rc->config->get('roundcube_backgrounds_size', 'cover');
        $position = $this->rc->config->get('roundcube_backgrounds_position', 'center center');
        $repeat = $this->rc->config->get('roundcube_backgrounds_repeat', 'no-repeat');
        $attachment = $this->rc->config->get('roundcube_backgrounds_attachment', 'fixed');

        $url = preg_replace('/[\"\\\'\\\\\\\\]/', '\\\\\\\\$0', $url);

        $css = <<<CSS
html, body {
    min-height: 100%;
}
body {
    background-image: url("{$url}") !important;
    background-size: {$size} !important;
    background-position: {$position} !important;
    background-repeat: {$repeat} !important;
    background-attachment: {$attachment} !important;
}
#layout-content {
    background-color: transparent !important;
}
CSS;

        $this->rc->output->add_header("<style>{$css}</style>");

        return $args;
    }

    private function resolve_wallpaper(): ?string
    {
        $images = $this->list_images();
        if (empty($images)) {
            return null;
        }

        $mode = $this->rc->config->get('roundcube_backgrounds_mode', 'monthly');
        if (!in_array($mode, self::MODES, true)) {
            $mode = 'monthly';
        }

        switch ($mode) {
            case 'fixed':
                $fixed = $this->rc->config->get('roundcube_backgrounds_fixed', '');
                if ($fixed && in_array($fixed, $images, true)) {
                    return $fixed;
                }
                return $images[0];

            case 'random':
                return $images[array_rand($images)];

            case 'monthly':
            default:
                return $this->resolve_monthly_image($images);
        }
    }

    private function resolve_monthly_image(array $images): string
    {
        $month = (int) date('n');
        $padded = sprintf('%02d', $month);

        foreach ([$padded, (string) $month] as $stem) {
            foreach (self::IMAGE_EXTENSIONS as $ext) {
                $name = "{$stem}.{$ext}";
                if (in_array($name, $images, true)) {
                    return $name;
                }
            }
        }

        $names = [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december',
        ];

        $stem = $names[$month] ?? '';
        if ($stem) {
            foreach (self::IMAGE_EXTENSIONS as $ext) {
                $name = "{$stem}.{$ext}";
                if (in_array($name, $images, true)) {
                    return $name;
                }
            }
        }

        $index = ($month - 1) % count($images);

        return $images[$index];
    }

    /**
     * @return list<string> Basenames of image files, sorted naturally.
     */
    private function list_images(): array
    {
        $dir = $this->images_directory();
        if (!is_dir($dir) || !is_readable($dir)) {
            return [];
        }

        $images = [];
        foreach (self::IMAGE_EXTENSIONS as $ext) {
            foreach (glob($dir . '/*.' . $ext) ?: [] as $path) {
                if (is_file($path)) {
                    $images[] = basename($path);
                }
            }
        }
        $images = array_unique($images);

        natsort($images);

        return array_values($images);
    }

    private function images_directory(): string
    {
        $custom = $this->rc->config->get('roundcube_backgrounds_path', '');
        if ($custom !== '') {
            $path = unslashify($custom);
            if ($path[0] !== '/') {
                $path = RCUBE_INSTALL_PATH . $path;
            }

            return $path;
        }

        return $this->home . '/' . self::ASSETS_SUBDIR;
    }

    private function image_url(string $filename): string
    {
        $custom = $this->rc->config->get('roundcube_backgrounds_path', '');
        if ($custom !== '') {
            $base = unslashify($custom);
            if ($base[0] !== '/') {
                return $this->rc->config->get('webmail_path', '/') . $base . '/' . rawurlencode($filename);
            }

            return $base . '/' . rawurlencode($filename);
        }

        // Same static.php prefix logic as rcmail_output_html::resource_location() (required since 1.7)
        $path = $this->url(self::ASSETS_SUBDIR . '/' . rawurlencode($filename));
        $assets_path = $this->rc->config->get('assets_path');
        if (!empty($assets_path)) {
            return $assets_path . $path;
        }
        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        $prefix = '';
        if (class_exists('rcmail_output_html') && ($info = rcmail_output_html::path_info())) {
            $prefix = str_repeat('../', substr_count($info, '/') + 2);
        }

        return $prefix . 'static.php/' . $path;
    }
}
