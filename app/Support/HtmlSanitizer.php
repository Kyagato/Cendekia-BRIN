<?php

namespace App\Support;

class HtmlSanitizer
{
    /**
     * Daftar tag HTML yang diperbolehkan untuk konten rich text.
     */
    protected const ALLOWED_TAGS = [
        'p', 'br', 'b', 'strong', 'i', 'em', 'u', 's', 'strike', 'del',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code', 'hr',
        'div', 'span', 'font',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'a',
    ];

    /**
     * Tag berbahaya yang harus dihapus secara menyeluruh beserta isinya.
     */
    protected const DANGEROUS_TAGS = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object',
        'embed', 'applet', 'svg', 'math', 'form', 'input', 'button',
        'textarea', 'select', 'option', 'link', 'meta', 'base',
    ];

    /**
     * Atribut yang diizinkan untuk tag HTML yang lolos seleksi.
     */
    protected const ALLOWED_ATTRIBUTES = [
        'class', 'style', 'title', 'id',
        'href', 'target', 'rel',
        'align', 'size', 'color',
        'colspan', 'rowspan', 'border',
    ];

    /**
     * Membersihkan string HTML dari potensi XSS.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        // Jika tidak ada karakter tag HTML sama sekali, return apa adanya
        if (!str_contains($trimmed, '<') && !str_contains($trimmed, '>')) {
            return $trimmed;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Bungkus dengan wrapper div dan meta UTF-8 agar encoding tidak rusak
        $encodedHtml = '<?xml encoding="utf-8" ?>' .
            '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><div id="__sanitizer_root__">' .
            $trimmed .
            '</div></body></html>';

        $dom->loadHTML($encodedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // 1. Hapus semua tag berbahaya beserta seluruh child nodenya
        foreach (self::DANGEROUS_TAGS as $dangerousTag) {
            $nodes = $xpath->query('//' . $dangerousTag);
            if ($nodes) {
                foreach ($nodes as $node) {
                    $node->parentNode?->removeChild($node);
                }
            }
        }

        // 2. Iterasi seluruh elemen tersisa
        $allElements = $xpath->query('//*');
        if ($allElements) {
            for ($i = $allElements->length - 1; $i >= 0; $i--) {
                $node = $allElements->item($i);
                if (!$node instanceof \DOMElement) {
                    continue;
                }

                $tagName = strtolower($node->nodeName);

                // Abaikan html, head, meta, body, dan sanitizer root
                if (in_array($tagName, ['html', 'head', 'meta', 'body'])) {
                    continue;
                }
                if ($node->getAttribute('id') === '__sanitizer_root__') {
                    continue;
                }

                // Jika tag tidak termasuk daftar tag yang diizinkan, lepaskan kontennya (unwrap)
                if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                    $parent = $node->parentNode;
                    if ($parent) {
                        while ($node->hasChildNodes()) {
                            $parent->insertBefore($node->firstChild, $node);
                        }
                        $parent->removeChild($node);
                    }
                    continue;
                }

                // 3. Periksa dan bersihkan atribut
                if ($node->hasAttributes()) {
                    $attributesToRemove = [];

                    foreach ($node->attributes as $attr) {
                        $attrName = strtolower($attr->name);
                        $attrValue = $attr->value;

                        // Larang semua event handler seperti onload, onerror, onclick, onmouseover, dll.
                        if (str_starts_with($attrName, 'on')) {
                            $attributesToRemove[] = $attr->name;
                            continue;
                        }

                        // Larang atribut di luar whitelist
                        if (!in_array($attrName, self::ALLOWED_ATTRIBUTES, true)) {
                            $attributesToRemove[] = $attr->name;
                            continue;
                        }

                        // Validasi href
                        if ($attrName === 'href') {
                            $normalizedUrl = preg_replace('/[\x00-\x20\s]+/', '', strtolower($attrValue));
                            if (
                                str_starts_with($normalizedUrl, 'javascript:') ||
                                str_starts_with($normalizedUrl, 'vbscript:') ||
                                str_starts_with($normalizedUrl, 'data:')
                            ) {
                                $attributesToRemove[] = $attr->name;
                                continue;
                            }
                        }

                        // Validasi inline style
                        if ($attrName === 'style') {
                            if (preg_match('/(expression|javascript|behavior|-moz-binding)/i', $attrValue)) {
                                $attributesToRemove[] = $attr->name;
                                continue;
                            }
                        }
                    }

                    foreach ($attributesToRemove as $nameToRemove) {
                        $node->removeAttribute($nameToRemove);
                    }

                    // Jika elemen <a> memiliki target="_blank", tambahkan rel="noopener noreferrer"
                    if ($tagName === 'a' && $node->getAttribute('target') === '_blank') {
                        $node->setAttribute('rel', 'noopener noreferrer');
                    }
                }
            }
        }

        // Ambil isi HTML dari wrapper __sanitizer_root__
        $rootNode = $dom->getElementById('__sanitizer_root__');
        if (!$rootNode) {
            return '';
        }

        $output = '';
        foreach ($rootNode->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }
}
