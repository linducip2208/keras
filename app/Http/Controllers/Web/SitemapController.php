<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];
        $base = config('app.url', 'http://localhost');

        $urls[] = ['loc' => $base . '/', 'priority' => '1.0', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => $base . '/best-aplikasi-hris-' . date('Y'), 'priority' => '0.9', 'changefreq' => 'monthly'];

        foreach (ProgrammaticSeoController::INDUSTRIES as $slug => $_name) {
            $urls[] = ['loc' => $base . '/best-aplikasi-hris-untuk-' . $slug, 'priority' => '0.8'];
        }
        foreach (ProgrammaticSeoController::CITIES as $slug => $_name) {
            $urls[] = ['loc' => $base . '/aplikasi-hris-' . $slug, 'priority' => '0.7'];
        }
        foreach (ProgrammaticSeoController::COMPETITORS as $slug => $_data) {
            $urls[] = ['loc' => $base . '/compare/hris-vs-' . $slug, 'priority' => '0.8'];
            $urls[] = ['loc' => $base . '/alternatives-to-' . $slug, 'priority' => '0.8'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc']) . '</loc>' . "\n";
            $xml .= '    <changefreq>' . ($u['changefreq'] ?? 'monthly') . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $u['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $base = config('app.url', 'http://localhost');
        $content = "User-agent: *\nAllow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /__pair\n";
        $content .= "Disallow: /storage\n\n";
        $content .= "Sitemap: {$base}/sitemap.xml\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
