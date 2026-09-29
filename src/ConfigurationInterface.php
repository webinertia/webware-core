<?php

declare(strict_types=1);

namespace Webware\Core;

/**
 * @api
 */
interface ConfigurationInterface
{
    /**
     * Generic top level config key for all
     * Webware packages to use as a container for their config values.
     * Expect this to be overridden by component Interface::class
     * per component
     */
    public const string CONFIG_KEY = 'webware';

    /**
     * Canonical, immutable component name — the root of every route name and
     * URI segment this component owns.
     *
     * Route names are dot separated and carry the name verbatim (`user.session.read`);
     * URI segments are the same token dash joined (`/user-manager/login`). A component
     * that publishes admin routes nests the token under the admin namespace, whose base
     * is resolved through webware-admin's Configuration (`admin.user.`).
     *
     * The value is a public contract — URLs, ACL resource ids and navigation links all
     * carry it — so changing it is a breaking change, not a rename.
     */
    public const string COMPONENT_NAME = 'webware';
}
