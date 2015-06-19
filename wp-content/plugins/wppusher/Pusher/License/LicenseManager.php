<?php

namespace Pusher\License;

use Pusher\Pusher;

class LicenseManager
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * @var WpShipperClient
     */
    private $client;

    public function __construct(Pusher $pusher, WpShipperClient $client)
    {
        $this->pusher = $pusher;
        $this->client = $client;
    }

    public function licenseKey()
    {
        $key = get_option('wppusher_license_key', false);

        if ( ! $key) {
            return false;
        }

        $key = $this->client->getLicenseKey($key);

        return $key;
    }

    public function activateSiteLicense($key, $oldKey)
    {
        // Field is deactivated, this means we
        // want to revoke it, since it can't be activated twice.
        $deactivate = is_null($key);

        if ($deactivate) {
            return $this->client->removeLicenseFomSite($oldKey);
        }

        $isValid = $this->client->registerKeyForSite($key);

        if ( ! $isValid) {
            add_settings_error('invalid-license-key', '', 'WP Pusher license could not be activated.');
        }

        return $isValid;
    }
}
