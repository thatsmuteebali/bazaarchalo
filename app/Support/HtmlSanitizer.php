<?php

namespace App\Support;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMProcessingInstruction;

/**
 * Cleans rich-text (Summernote) HTML before it is shown to customers.
 * Keeps basic formatting, removes scripts, event handlers, styles and unsafe links.
 */
class HtmlSanitizer
{
    private const ALLOWED = [
        'p', 'br', 'hr', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u', 's',
        'h2', 'h3', 'h4', 'h5', 'blockquote', 'span', 'div',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'a', 'img',
    ];

    private const DROP = [
        'script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button',
        'textarea', 'select', 'link', 'meta', 'svg', 'math', 'noscript', 'base',
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        // plain text (no tags): just keep the line breaks
        if ($html === strip_tags($html)) {
            return nl2br(e($html));
        }

        $previous = libxml_use_internal_errors(true);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementsByTagName('div')->item(0);

        if (! $root) {
            return e(strip_tags($html));
        }

        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes, false) as $child) {
            if ($child instanceof DOMComment || $child instanceof DOMProcessingInstruction) {
                $node->removeChild($child);
                continue;
            }

            if (! $child instanceof DOMElement) {
                continue; // text stays
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }

            self::walk($child); // clean the inside first

            if (! in_array($tag, self::ALLOWED, true)) {
                // unknown tag: keep its (already clean) content, drop the tag itself
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            self::cleanAttributes($child, $tag);
        }
    }

    private static function cleanAttributes(DOMElement $el, string $tag): void
    {
        $keep = match ($tag) {
            'a'        => ['href'],
            'img'      => ['src', 'alt'],
            'td', 'th' => ['colspan', 'rowspan'],
            default    => [],
        };

        foreach (iterator_to_array($el->attributes, false) as $attr) {
            if (! in_array($attr->name, $keep, true)) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($tag === 'a') {
            $href = trim($el->getAttribute('href'));

            if (preg_match('#^(https?://|mailto:)#i', $href)) {
                $el->setAttribute('rel', 'noopener noreferrer nofollow');
                $el->setAttribute('target', '_blank');
            } else {
                $el->removeAttribute('href');
            }
        }

        if ($tag === 'img') {
            $src = trim($el->getAttribute('src'));

            if (preg_match('#^(https?://|/)#i', $src)) {
                $el->setAttribute('loading', 'lazy');
            } elseif ($el->parentNode) {
                $el->parentNode->removeChild($el);
            }
        }
    }
}
