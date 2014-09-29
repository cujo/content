<?php
namespace Cujo\Content;

class Multilingual extends Content implements Mutable
{
    private $contents = [];
    private $locale;
    private $fallback;

    public function __construct(array $contents, $locale = null, $fallback = null)
    {
        $this->contents = $contents;
        $this->locale = $locale;
        $this->fallback = $fallback;
    }

    public function addContent($locale, $content)
    {
        $this->contents[$locale] = $content;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function setFallback($locale)
    {
        $this->fallback = $locale;
    }

    public function getLocale()
    {
        return $this->locale ?: \Locale::getDefault();
    }

    public function getLocales()
    {
        return array_keys($this->contents);
    }

    public function get($key)
    {
        $current = $this->getLocale();
        $locales = array_keys($this->contents);
        while (!empty($locales)) {
            $locale = \Locale::lookup($locales, $current, false, $this->fallback);
            if (!$locale) {
                return false;
            }
            $value = $this->contents[$locale]->get($key);
            if (false !== $value && null !== $value) {
                return $value;
            }
            unset($locales[array_search($locale, $locales)]);
        }
    }

    public function find(array $criteria)
    {
        if ($content = $this->getContent()) {
            return $content->find($criteria);
        }
    }

    public function set($key, $value)
    {
        if ($content = $this->getContent()) {
            $content->set($key, $value);
        }
    }

    public function remove($key)
    {
        if ($content = $this->getContent()) {
            $content->remove($key);
        }
    }

    public function isMutable()
    {
        if ($content = $this->getContent()) {
            return $content->isMutable();
        }
        return false;
    }

    protected function getContent()
    {
        $locales = array_keys($this->contents);
        $locale = \Locale::lookup($locales, $this->getLocale(), false, false);
        if ($locale) {
            return $this->contents[$locale];
        }
        return null;
    }
}
