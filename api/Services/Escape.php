<?php

namespace Api\Services;

use \DOMDocument;

class Escape
{
    public function escape_attribute_values($html) {
        return preg_replace_callback(
            '/(\s[\w:-]+)=([\'"])(.*?)\2/s',
            function ($matches) {
                $attr = $matches[1]; // attribute name (e.g., x-data)
                $quote = $matches[2]; // quote character (either ' or ")
                $value = $matches[3]; // unescaped attribute value

                // Escape <, >, &, ", '
                $escaped = htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
                return $attr . '=' . $quote . $escaped . $quote;
            },
            $html
        );
    }

    public function fix_void_elements($html) {
        // https://developer.mozilla.org/en-US/docs/Glossary/Void_element
        $voidElements = [
            'area', 'base', 'br', 'col', 'embed', 'hr', 'img',
            'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
        ];

        $pattern = '/<(' . implode('|', array_map('preg_quote', $voidElements)) . ')\b([^>]*)>/i';

        return preg_replace_callback($pattern, function ($matches) {
            $tagName = $matches[1];
            $attributes = trim($matches[2]);

            // Avoid double-closing (e.g., already <input ... />)
            if (substr($attributes, -1) === '/') return "<$tagName $attributes>";

            // Return self-closing version
            return "<$tagName" . ($attributes ? " $attributes" : "") . " />";
        }, $html);
    }

    public function fix_boolean_attributes($html) {
        // https://meiert.com/blog/boolean-attributes-of-html/
        $booleanAttrs = [
            'allowfullscreen', 'alpha', 'async', 'autofocus', 'autoplay', 'checked',
            'controls', 'default', 'defer', 'disabled', 'formnovalidate', 'inert',
            'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate',
            'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected',
            'shadowrootclonable', 'shadowrootcustomelementregistry',
            'shadowrootdelegatesfocus', 'shadowrootserializable'
        ];
        $pattern = implode('|', array_map('preg_quote', $booleanAttrs));

        return preg_replace_callback('/<\w+[^>]*>/i', function ($matches) use ($pattern) {
            $tag = $matches[0];

            // Mask quoted sections (so we don't modify inside attributes)
            $placeholders = [];
            $tag = preg_replace_callback('/"[^"]*"|\'[^\']*\'/', function ($m) use (&$placeholders) {
                $key = "__Q" . count($placeholders) . "__";
                $placeholders[$key] = $m[0];
                return $key;
            }, $tag);

            // Replace standalone boolean attributes (not followed by '=')
            $tag = preg_replace("/\b($pattern)\b(?!\s*=)(?=[\s>])/i", '$1="$1"', $tag);

            // Restore quoted sections
            $tag = strtr($tag, $placeholders);

            return $tag;
        }, $html);
    }

    /**
     * Fix missing closing curly braces in style blocks or inline styles.
     * Example: #user-icons{background-image:url(...)
     * Will add a closing } if missing.
     */
    public function fix_missing_closing_curly_brace($html) {
        // Fix inside <style>...</style> blocks
        return preg_replace_callback(
            '/(<style[^>]*>)(.*?)(<\/style>)/is',
            function ($matches) {
                $content = $matches[2];
                // Add "}" if there is an opening "{" without a closing "}"
                $openCount = substr_count($content, '{');
                $closeCount = substr_count($content, '}');
                if ($openCount > $closeCount) {
                    $content .= str_repeat('}', $openCount - $closeCount);
                }
                return $matches[1] . $content . $matches[3];
            },
            $html
        );
    }

    /**
     * Fix malformed HTML comments <!-- ... --!> to <!-- ... -->
     */
    public function fix_html_comments($html) {
        return preg_replace('/<!--(.*?)!?--!?>/s', '<!--$1-->', $html);
    }
}
