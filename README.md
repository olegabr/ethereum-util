# ethereum-util

[![CI](https://github.com/olegabr/ethereum-util/actions/workflows/ci.yml/badge.svg)](https://github.com/olegabr/ethereum-util/actions/workflows/ci.yml)
[![Licensed under the MIT License](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/olegabr/ethereum-util/blob/master/LICENSE)

A collection of utility functions for Ethereum written in php.

This repository is the EthereumICO fork ([olegabr/ethereum-util](https://github.com/olegabr/ethereum-util))
of [web3p/ethereum-util](https://github.com/web3p/ethereum-util). It keeps the
`Web3p\EthereumUtil` namespace and adds PHP 8.x support and tests.

# Install

The fork is not published on Packagist, so point Composer at the repository:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/olegabr/ethereum-util" }
    ],
    "require": {
        "olegabr/ethereum-util": "dev-master"
    }
}
```

Pin a tagged release instead of `dev-master` when one covers your needs.
