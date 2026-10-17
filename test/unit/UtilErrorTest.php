<?php

namespace Test\Unit;

use InvalidArgumentException;
use Test\TestCase;
use Web3p\EthereumUtil\Util;

/**
 * Util with a pair of accessors, so the magic __get/__set dispatch can be tested.
 */
class UtilWithAccessors extends Util
{
    private $bar = 'bar';

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($value)
    {
        $this->bar = $value;
    }
}

class UtilErrorTest extends TestCase
{
    const PRIVATE_KEY = '4646464646464646464646464646464646464646464646464646464646464646';
    const PUBLIC_KEY = '044bc2a31265153f07e70e0bab08724e6b85e217f8cd628ceb62974247bb493382ce28cab79ad7119ee1ad3ebcdb98a16805211530ecc6cfefa1b88e6dff99232a';
    const HASH = 'daf5a779ae972f972197303d7b574746c7ef83eadac0f2791ad23db92e4c8e53';

    /**
     * testConstruct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(Util::class, new Util());
    }

    /**
     * testMagicGet
     *
     * @return void
     */
    public function testMagicGet()
    {
        $util = new UtilWithAccessors;

        $this->assertEquals('bar', $util->bar);
        // no matching getter and no matching setter must not blow up
        $this->assertFalse($util->unknown);
    }

    /**
     * testMagicSet
     *
     * @return void
     */
    public function testMagicSet()
    {
        $util = new UtilWithAccessors;
        $util->bar = 'baz';

        $this->assertEquals('baz', $util->bar);

        $util->unknown = 'value';
        $this->assertFalse(isset($util->unknown));
    }

    /**
     * testPublicKeyToAddressInvalidFormat
     *
     * @return void
     */
    public function testPublicKeyToAddressInvalidFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid public key format.');
        (new Util)->publicKeyToAddress('not a key');
    }

    /**
     * testPublicKeyToAddressInvalidLength
     *
     * @return void
     */
    public function testPublicKeyToAddressInvalidLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid public key length.');
        (new Util)->publicKeyToAddress('0x1234');
    }

    /**
     * testPrivateKeyToPublicKeyInvalidFormat
     *
     * @return void
     */
    public function testPrivateKeyToPublicKeyInvalidFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid private key format.');
        (new Util)->privateKeyToPublicKey('zzzz');
    }

    /**
     * testPrivateKeyToPublicKeyInvalidLength
     *
     * @return void
     */
    public function testPrivateKeyToPublicKeyInvalidLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid private key length.');
        (new Util)->privateKeyToPublicKey('0x1234');
    }

    /**
     * testRecoverPublicKeyInvalidHash
     *
     * @return void
     */
    public function testRecoverPublicKeyInvalidHash()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid hash format.');
        (new Util)->recoverPublicKey('xyz', '0x28', '0x67', 0);
    }

    /**
     * testRecoverPublicKeyInvalidSignatureFormat
     *
     * @return void
     */
    public function testRecoverPublicKeyInvalidSignatureFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid signature format.');
        (new Util)->recoverPublicKey('0x' . self::HASH, 'xyz', '0x67', 0);
    }

    /**
     * testRecoverPublicKeyInvalidSignatureLength
     *
     * @return void
     */
    public function testRecoverPublicKeyInvalidSignatureLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid signature length.');
        (new Util)->recoverPublicKey('0x' . self::HASH, '0x28', '0x67', 0);
    }

    /**
     * testRecoverPublicKeyRoundTrip
     *
     * @return void
     */
    public function testRecoverPublicKeyRoundTrip()
    {
        $util = new Util;
        $signature = $util->ecsign(self::PRIVATE_KEY, self::HASH);
        $recovered = $util->recoverPublicKey(
            self::HASH,
            $signature->r->toString(16),
            $signature->s->toString(16),
            $signature->recoveryParam - 35
        );

        $this->assertEquals('0x' . self::PUBLIC_KEY, $recovered);
        $this->assertEquals('0x' . self::PUBLIC_KEY, $util->privateKeyToPublicKey(self::PRIVATE_KEY));
        $this->assertEquals('0x9d8a62f656a8d1615c1294fd71e9cfb3e4855a4f', $util->publicKeyToAddress(self::PUBLIC_KEY));
    }

    /**
     * testEcsignInvalidPrivateKeyFormat
     *
     * @return void
     */
    public function testEcsignInvalidPrivateKeyFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid private key format.');
        (new Util)->ecsign('zzzz', self::HASH);
    }

    /**
     * testEcsignInvalidPrivateKeyLength
     *
     * @return void
     */
    public function testEcsignInvalidPrivateKeyLength()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Private key length was wrong.');
        (new Util)->ecsign('0x123', self::HASH);
    }

    /**
     * testSha3AndHashPersonalMessage
     *
     * @return void
     */
    public function testSha3AndHashPersonalMessage()
    {
        $util = new Util;

        $this->assertNull($util->sha3(''));
        $this->assertEquals('8144a6fa26be252b86456491fbcd43c1de7e022241845ffea1c3df066f7cfede', $util->hashPersonalMessage('Hello world'));
        $this->assertNotEquals($util->sha3('Hello world'), $util->hashPersonalMessage('Hello world'));
    }
}
