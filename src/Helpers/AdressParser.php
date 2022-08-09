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
     * @param string $sub_domain
     * @return string
     */
    public static function getDomain(string $sub_domain): string
    {
        $post_server = dns_get_record($sub_domain, DNS_MX);
        if (!isset($post_server[0]['target'])) return "";
        $post_domain = $post_server[0]['target'];
        $post_domain = explode('.', $post_domain);
        $first_domain = array_pop($post_domain);
        $second_domain = array_pop($post_domain);
        $main_domain = implode('.', [$second_domain, $first_domain]);

        return $main_domain;
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
}