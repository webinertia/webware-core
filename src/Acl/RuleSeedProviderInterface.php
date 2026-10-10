<?php

declare(strict_types=1);

namespace Webware\Core\Acl;

/**
 * Implemented by any package that owns routes and therefore owns policy for them.
 *
 * Providers are *published*, never called across packages: a provider is listed
 * under the ACL config key's `rule_seed_providers` entry by its own package, and
 * whichever seeding command runs collects them from configuration. That keeps the
 * dependency direction one-way - every package points at core, and no package
 * names another package's class.
 *
 * `$adminName` is passed in rather than resolved by the provider so a provider
 * stays free of container lookups and is trivially constructible in tests. The
 * caller resolves it once, from the admin component's configuration.
 *
 * @api
 */
interface RuleSeedProviderInterface
{
    /**
     * @return list<RuleSeed>
     */
    public function ruleSeeds(string $adminName): array;
}
