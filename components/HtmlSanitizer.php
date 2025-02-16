<?php

namespace app\components;

use yii\base\Component;

class HtmlSanitizer extends Component
{
    /**
     * Список разрешенных HTML тегов
     */
    public $allowedTags = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'p' => ['style', 'class'],
        'div' => ['style', 'class'],
        'span' => ['style', 'class'],
        'br' => [],
        'b' => [],
        'strong' => [],
        'i' => [],
        'em' => [],
        'u' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        'blockquote' => [],
    ];

    /**
     * Очищает HTML, оставляя только разрешенные теги и атрибуты
     */
    public function sanitize($html)
    {
        if (empty($html)) {
            return '';
        }

        // Создаем конфигурацию для strip_tags
        $allowedTagsStr = '';
        foreach ($this->allowedTags as $tag => $attributes) {
            $allowedTagsStr .= "<$tag>";
        }

        // Сначала удаляем все неразрешенные теги
        $html = strip_tags($html, $allowedTagsStr);

        // Создаем DOM документ
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Проходим по всем элементам
        $elements = $dom->getElementsByTagName('*');
        for ($i = $elements->length - 1; $i >= 0; $i--) {
            $element = $elements->item($i);
            $tagName = strtolower($element->tagName);

            // Проверяем, разрешен ли тег
            if (!isset($this->allowedTags[$tagName])) {
                $element->parentNode->removeChild($element);
                continue;
            }

            // Проверяем атрибуты
            $allowedAttributes = $this->allowedTags[$tagName];
            for ($j = $element->attributes->length - 1; $j >= 0; $j--) {
                $attribute = $element->attributes->item($j);
                if (!in_array($attribute->name, $allowedAttributes)) {
                    $element->removeAttribute($attribute->name);
                }
            }

            // Особая обработка для ссылок
            if ($tagName === 'a') {
                $href = $element->getAttribute('href');
                if ($href) {
                    // Проверяем протокол
                    $allowedProtocols = ['http://', 'https://', 'mailto:', 'tel:', 'ftp://'];
                    $valid = false;
                    foreach ($allowedProtocols as $protocol) {
                        if (stripos($href, $protocol) === 0) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        $element->setAttribute('href', '#');
                    }
                }
            }

            // Особая обработка для изображений
            if ($tagName === 'img') {
                $src = $element->getAttribute('src');
                if ($src) {
                    // Проверяем протокол
                    $allowedProtocols = ['http://', 'https://', 'data:image/'];
                    $valid = false;
                    foreach ($allowedProtocols as $protocol) {
                        if (stripos($src, $protocol) === 0) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        $element->parentNode->removeChild($element);
                    }
                }
            }
        }

        // Получаем очищенный HTML
        $html = $dom->saveHTML();
        
        // Удаляем лишние пробелы и переносы строк
        $html = preg_replace('/^\s+|\s+$/m', '', $html);
        
        return $html;
    }
}
