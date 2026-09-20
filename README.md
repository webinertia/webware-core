# webware/webware-core

[![PHP Version](https://img.shields.io/packagist/php-v/webware/webware-core)](https://packagist.org/packages/webware/webware-core)
[![Latest Version](https://img.shields.io/packagist/v/webware/webware-core)](https://packagist.org/packages/webware/webware-core)
[![License](https://img.shields.io/github/license/webinertia/webware-core)](LICENSE)
[![Continuous Integration](https://github.com/webinertia/webware-core/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/webinertia/webware-core/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/webinertia/webware-core/graph/badge.svg)](https://codecov.io/gh/webinertia/webware-core)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fwebinertia%2Fwebware-core%2F0.1.x)](https://dashboard.stryker-mutator.io/reports/github.com/webinertia/webware-core/0.1.x)

Contracts and utilities for the webware-* ecosystem.

## Installation

```bash
composer require webware/webware-core
```

### Provider load order

This package declares an ecosystem-wide alias so that every consumer resolves the Mezzio
authentication contract to the Webware implementation:

```php
'aliases' => [
    Mezzio\Authentication\UserInterface::class => Webware\Core\UserInterface::class,
],
```

The implementation behind that alias is registered by `webware/webware-usermanager`, and
`mezzio/mezzio-authentication` declares a factory for the same service name. Register the
providers in this order in `config/config.php` so the alias is the final word on that key:

1. `Mezzio\Authentication\ConfigProvider`
2. `Webware\Core\ConfigProvider`
3. `Webware\UserManager\ConfigProvider`

The component installer adds providers in the order packages were installed, which is not
necessarily this order — check `config/config.php` after installing and reorder if needed.
Any provider that declares an entry for `Mezzio\Authentication\UserInterface` competes for
the same service name and the last one aggregated wins, so keep the Webware providers after
`mezzio/mezzio-authentication`, with core before usermanager.
