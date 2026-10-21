<?php

namespace App\Support;

/**
 * Pembersih HTML untuk konten dari editor teks kaya (deskripsi, tata tertib, deskripsi sesi).
 * Daftar putih: hanya tag/atribut/properti CSS aman yang dipertahankan, sisanya dibuang,
 * sehingga aman ditampilkan dengan {!! !!} di halaman publik.
 */
class Html
{
    private const TAGS = [
        'p', 'br', 'hr', 'div', 'span', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'sub', 'sup',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre', 'code',
        'ul', 'ol', 'li', 'a', 'img', 'figure', 'figcaption',
        'table', 'caption', 'colgroup', 'col', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
    ];

    /** Tag berbahaya: dibuang beserta isinya (bukan sekadar dilepas). */
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'select', 'textarea', 'link', 'meta', 'base', 'svg', 'math', 'noscript', 'template', 'applet', 'frame', 'frameset'];

    private const ATTRS = [
        '*' => ['style', 'title'],
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height'],
        'td' => ['colspan', 'rowspan', 'width', 'height', 'align', 'valign'],
        'th' => ['colspan', 'rowspan', 'width', 'height', 'align', 'valign', 'scope'],
        'table' => ['border', 'cellpadding', 'cellspacing', 'width', 'align'],
        'col' => ['span', 'width'],
        'colgroup' => ['span', 'width'],
    ];

    private const CSS = [
        'color', 'background-color', 'font-size', 'font-family', 'font-weight', 'font-style',
        'text-decoration', 'text-align', 'text-indent', 'line-height', 'letter-spacing',
        'width', 'height', 'max-width', 'min-width', 'vertical-align', 'float',
        'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom',
        'padding', 'padding-left', 'padding-right', 'padding-top', 'padding-bottom',
        'border', 'border-collapse', 'border-spacing', 'border-color', 'border-width', 'border-style',
        'border-top', 'border-bottom', 'border-left', 'border-right',
    ];

    public static function isHtml(?string $v): bool
    {
        return $v !== null && (bool) preg_match('/<\s*[a-z][a-z0-9]*[^>]*>/i', $v);
    }

    /** Bersihkan HTML. Hasil kosong secara visual (mis. "<p><br></p>") dikembalikan sebagai null. */
    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }
        if (! self::isHtml($html)) {
            return trim($html) === '' ? null : $html;   // teks biasa dibiarkan apa adanya
        }

        $prev = libxml_use_internal_errors(true);
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementById('__root');
        if (! $root) {
            return null;
        }
        self::walk($root);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }
        $out = trim($out);

        // kosong secara visual (tanpa teks, gambar, tabel, garis)?
        if (trim(strip_tags($out, '<img><table><hr>')) === '' || trim(html_entity_decode(strip_tags($out, '<img><table><hr>'), ENT_QUOTES, 'UTF-8'), " \t\n\r\0\x0B\xC2\xA0") === '') {
            return null;
        }

        return $out;
    }

    /** Siap tampil: HTML dibersihkan; teks biasa diubah newline -> <br>. */
    public static function render(?string $v): string
    {
        if ($v === null || trim($v) === '') {
            return '';
        }

        return self::isHtml($v) ? (string) self::clean($v) : nl2br(e($v));
    }

    /** Teks polos untuk ringkasan/kartu. */
    public static function plain(?string $v, ?int $limit = null): string
    {
        $t = $v === null ? '' : trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '<br/>', '</li>'], ' ', $v)), ENT_QUOTES, 'UTF-8')));

        return $limit ? \Illuminate\Support\Str::limit($t, $limit) : $t;
    }

    private static function walk(\DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMComment || $child instanceof \DOMProcessingInstruction) {
                $node->removeChild($child);
                continue;
            }
            if (! $child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }

            self::walk($child);

            if (! in_array($tag, self::TAGS, true)) {
                // tag tak dikenal: lepas pembungkusnya, pertahankan isinya
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            self::cleanAttributes($child, $tag);
        }
    }

    private static function cleanAttributes(\DOMElement $el, string $tag): void
    {
        $allowed = array_merge(self::ATTRS['*'], self::ATTRS[$tag] ?? []);

        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->nodeName);
            if (! in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->nodeName);
                continue;
            }

            $val = $attr->nodeValue;
            if ($name === 'href' && ! self::safeUrl($val, ['http', 'https', 'mailto', 'tel'], true)) {
                $el->removeAttribute($attr->nodeName);
            } elseif ($name === 'src' && ! self::safeUrl($val, ['http', 'https'], false)) {
                $el->removeAttribute($attr->nodeName);
            } elseif ($name === 'style') {
                $css = self::cleanStyle($val);
                $css === '' ? $el->removeAttribute('style') : $el->setAttribute('style', $css);
            } elseif ($name === 'target' && ! in_array($val, ['_blank', '_self'], true)) {
                $el->removeAttribute('target');
            }
        }

        if ($tag === 'a' && $el->getAttribute('target') === '_blank') {
            $el->setAttribute('rel', 'noopener noreferrer');
        }
        if ($tag === 'img' && ! $el->hasAttribute('src')) {
            $el->parentNode && $el->parentNode->removeChild($el);
        }
    }

    private static function safeUrl(string $url, array $schemes, bool $allowFragment): bool
    {
        $u = trim(preg_replace('/[\x00-\x20]+/', '', $url));
        if ($u === '') {
            return false;
        }
        if ($u[0] === '/' && ! (isset($u[1]) && $u[1] === '/')) {
            return true;                                   // path relatif ke situs ini
        }
        if ($allowFragment && $u[0] === '#') {
            return true;
        }
        if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $u, $m)) {
            return in_array(strtolower($m[1]), $schemes, true);
        }

        return false;
    }

    private static function cleanStyle(string $style): string
    {
        $out = [];
        foreach (explode(';', $style) as $decl) {
            if (strpos($decl, ':') === false) {
                continue;
            }
            [$prop, $val] = array_map('trim', explode(':', $decl, 2));
            $prop = strtolower($prop);
            if (! in_array($prop, self::CSS, true) || $val === '') {
                continue;
            }
            if (preg_match('/url\s*\(|expression|javascript|@import|behavior|-moz-binding|\\\\|<|>/i', $val)) {
                continue;
            }
            if (! preg_match('/^[#a-z0-9\s.,%()\-+\'"!]+$/i', $val)) {
                continue;
            }
            $out[] = $prop . ': ' . $val;
        }

        return implode('; ', $out);
    }
}
