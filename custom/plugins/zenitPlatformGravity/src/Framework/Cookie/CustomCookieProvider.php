<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Framework\Cookie;

use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CustomCookieProvider implements CookieProviderInterface {

    private CookieProviderInterface $originalService;
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService, CookieProviderInterface $service)
    {
        $this->systemConfigService = $systemConfigService;
        $this->originalService = $service;
    }

    private const googleFontsCookie = [
        'snippet_name' => 'zentheme.cookie.googleFonts.name',
        'snippet_description' => 'zentheme.cookie.googleFonts.description',
        'cookie' => 'zen_google_fonts',
        'value' => '1',
        'expiration' => '30'
    ];

    private const googleDocsCookie = [
        'snippet_name' => 'zentheme.cookie.googleDocs.name',
        'snippet_description' => 'zentheme.cookie.googleDocs.description',
        'cookie' => 'zen_google_docs',
        'value' => '1',
        'expiration' => '30'
    ];

    public function getCookieGroups(): array
    {

        $cookies = $this->originalService->getCookieGroups();

        foreach ($cookies as &$cookie) {
            if (!\is_array($cookie)) {
                continue;
            }

            if (!$this->isComfortFeaturesGroup($cookie)) {
                continue;
            }

            if (!\array_key_exists('entries', $cookie)) {
                continue;
            }

            if ($this->systemConfigService->get('zenitPlatformGravity.config.googleFontsCookie')) {
                $cookie['entries'][] = array_merge(
                    self::googleFontsCookie
                );
            }

            if ($this->systemConfigService->get('zenitPlatformGravity.config.pdfrenderer') == 'googledocs') {
                $cookie['entries'][] = array_merge(
                    self::googleDocsCookie
                );
            }
        }

        return $cookies;
    }

    private function isComfortFeaturesGroup(array $cookie): bool
    {
        return (\array_key_exists('snippet_name', $cookie) && $cookie['snippet_name'] === 'cookie.groupComfortFeatures');
    }
}