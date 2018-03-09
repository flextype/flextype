<?php
namespace Rawilum;

use Url;
use Response;
use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Page
{
    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * __construct
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;
    }

    /**
     * Get page
     */
    public function getPage($url = '', $raw = false, $url_abs = false)
    {

        $file = $this->finder($url, $url_abs);

        if ($raw) {
            $page = trim(file_get_contents($file));
        } else {
            $page = $this->parse($file);

            $page_frontmatter = $page['frontmatter'];
            $page_content = $page['content'];

            $page = $page_frontmatter;

            // Parse page for summary <!--more-->
            if (($pos = strpos($page_content, "<!--more-->")) === false) {
                $page_content = $this->rawilum['filters']->dispatch('content', $page_content);
            } else {
                $page_content = explode("<!--more-->", $page_content);
                $page['summary']  = $this->rawilum['filters']->dispatch('content', $page_content[0]);
                $page['content']  = $this->rawilum['filters']->dispatch('content', $page_content[0].$page_content[1]);
            }

            if (is_array($page_content)) {
                $page['summary'] = $this->rawilum['markdown']->text($page['summary']);
                $page['content'] = $this->rawilum['markdown']->text($page['content']);
            } else {
                $page['content'] = $this->rawilum['markdown']->text($page_content);
            }
        }

        return $page;
    }

    /**
     * Page finder
     */
    public function finder($url = '', $url_abs = false)
    {

        // If url is empty that its a homepage
        if ($url_abs) {
          if ($url) {
              $file = $url;
          } else {
              $file = CONTENT_PATH . '/pages/' . $this->rawilum['config']->get('site.pages.main') . '/' . 'index.md';
          }
        } else {
          if ($url) {
              $file = CONTENT_PATH . '/pages/' . $url . '/index.md';
          } else {
              $file = CONTENT_PATH . '/pages/' . $this->rawilum['config']->get('site.pages.main') . '/' . 'index.md';
          }
        }

        // Get 404 page if file not exists
        if ($this->rawilum['filesystem']->exists($file)) {
            $file = $file;
        } else {
            $file = CONTENT_PATH . '/pages/404/index.md';
            Response::status(404);
        }

        return $file;
    }

    /**
     * Render page
     */
    public function renderPage($page)
    {
        $template_ext = '.php';
        $template_name = empty($page['template']) ? 'index' : $page['template'];

        include THEMES_PATH . '/' . $this->rawilum['config']->get('site.theme') . '/' . $template_name . $template_ext;
    }

    /**
     * Page parser
     */
    public function parse($file)
    {
        $page = trim(file_get_contents($file));

        $page = explode('---', $page, 3);

        $frontmatter = Yaml::parse($page[1]);
        $content = $page[2];

        $url = str_replace(CONTENT_PATH . '/pages', Url::getBase(), $file);
        $url = str_replace('index.md', '', $url);
        $url = str_replace('.md', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');

        $frontmatter['url']  = $url;
        $frontmatter['slug'] = basename($file, '.md');

        return ['frontmatter' => $frontmatter, 'content' => $content];
    }
}
