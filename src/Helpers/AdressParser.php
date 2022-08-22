<?php

namespace ImapRecipient\Helpers;

use ImapRecipient\Constants\DomainsList;

/**
 * Class AdressParser
 * @package ImapRecipient
 */
class AdressParser
{
    /**
     * get main domain main domain from subdomain. Example inbox.ru -> mail.ru
     *
     * @param string $email
     * @return string
     */
    public static function getMailBox(string $email): string
    {
        $domain = self::getDomain(self::getSubDomain($email));

        return DomainsList::DOMAINS[$domain];
    }

    /**
     * get main domain from sub or main domain
     *
     * @param string $subDomain
     * @return string
     */
    public static function getDomain(string $subDomain): string
    {
        $postServer = self::getDnsRecord($subDomain);
        return self::getMainDomainFromServerInfo($postServer);
    }

    /**
     * extract subdomain from email
     *
     * @param string $email
     * @return string
     */
    public static function getSubDomain(string $email): string
    {
        $sub_domain = explode("@", $email);

        return $sub_domain[1];
    }

    /**
     * extract user name from email
     *
     * @param string $email
     * @return string
     */
    public static function getUserName(string $email): string
    {
        $user_name = explode("@", $email);

        return $user_name[0];
    }

    /**
     * @param $subDomain
     * @return array
     */
    protected static function getDnsRecord($subDomain): array
    {
        return dns_get_record($subDomain, DNS_MX);
    }

    /**
     * @param array $postServer
     * @return string
     */
    protected static function getMainDomainFromServerInfo(array $postServer): string
    {
        if (!isset($postServer[0]['target'])) return "";
        $postDomain = $postServer[0]['target'];
        $postDomain = explode('.', $postDomain);
        $firstDomain = array_pop($postDomain);
        $secondDomain = array_pop($postDomain);
        return implode('.', [$secondDomain, $firstDomain]);
    }
}