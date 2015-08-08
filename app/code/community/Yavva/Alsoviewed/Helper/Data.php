<?php

class Yavva_Alsoviewed_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isIpAddressIgnored($ip = null)
    {
        if (!Mage::getStoreConfig('alsoviewed/log/ignore_ip_address')) {
            return false;
        }
        if (!$ignoredIps = Mage::getStoreConfig('alsoviewed/log/ignored_ip_address')) {
            return false;
        }

        if (null === $ip) {
            $ip = Mage::helper('core/http')->getRemoteAddr();
            if (!$ip) {
                return false;
            }
        }

        $ignoredIps = explode(',', $ignoredIps);
        return in_array($ip, $ignoredIps);
    }

    public function isUserAgentIgnored($userAgent = null)
    {
        if (!Mage::getStoreConfig('alsoviewed/log/ignore_user_agent')) {
            return false;
        }
        if (!$ignoredUserArgents = Mage::getStoreConfig('alsoviewed/log/ignored_user_agent')) {
            return false;
        }

        if (null === $userAgent) {
            $userAgent = Mage::helper('core/http')->getHttpUserAgent();
            if (!$userAgent) {
                return false;
            }
        }

        $regexp = '/' . trim($ignoredUserArgents, '/') . '/';
        return @preg_match($regexp, $userAgent);
    }
}
