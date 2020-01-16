<?php

namespace App\Utils;

class Domains
{
    /**
     * @param string $name
     * @param string[] $tlds
     * @return string[]
     */
    public static function fromNameAndTlds(string $name, array $tlds)
    {
        return self::fromNamesAndTlds([$name], $tlds);
    }

    /**
     * @param string[] $names
     * @param string[] $tlds
     * @return string[]
     */
    public static function fromNamesAndTlds(array $names, array $tlds)
    {
        $domains = [];
        foreach ($names as $name) {
            foreach ($tlds as $tld) {
                $domains[] = "$name.$tld";
            }
        }
        return $domains;
    }

    /**
     * @param string $domain
     * @return bool
     */
    public static function valid(string $domain): bool
    {
        return preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain);
    }

    /**
     * @param string $domain
     * @return bool
     */
    public static function validDomainName(string $domain): bool
    {
        return preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]){1}$/', $domain);
    }

    /**
     * @param string $domain
     * @return string
     */
    public static function getTld(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $parseDomain = explode('.', $domain);
        unset($parseDomain[0]);

        return implode('.', $parseDomain);
    }

    /**
     * @param string $domain
     * @return string
     */
    public static function getSld(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $parseDomain = explode('.', $domain);

        return $parseDomain[0];
    }
}
