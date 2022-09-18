<?php

namespace Mnemesong\UuidIdentityTestUnit;

use Mnemesong\IdentityInterface\IdentityInterface;
use Mnemesong\UuidIdentity\UuidIdentity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidIdentityTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsIdentity(): void
    {
        $identities = [];
        $count = 10000;
        for ($i = 0; $i < $count; $i ++)
        {
            $identities[] = UuidIdentity::new();
        }
        $identitiesUuids = array_map(fn(UuidIdentity $id) => ($id->getUuid()), $identities);
        $this->assertCount($count, $identitiesUuids);
        $identityUuidsCounts = array_count_values($identitiesUuids);
        $this->assertCount($count, $identityUuidsCounts);
        foreach ($identityUuidsCounts as $uuidCount)
        {
            $this->assertEquals(1, $uuidCount);
        }
    }

    /**
     * @return void
     */
    public function testGetUuid(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $identity = new UuidIdentity($uuid);
        $this->assertEquals($identity->getUuid(), $uuid);
    }

    /**
     * @return void
     */
    public function testNew(): void
    {
        $id = UuidIdentity::new();
        $this->assertTrue(Uuid::isValid($id->getUuid()));
    }

    /**
     * @return void
     */
    public function testConstructException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $id = new UuidIdentity('invalidUuid');
    }

    /**
     * @return void
     */
    public function testIsSame(): void
    {
        $uuid1 = Uuid::uuid4()->toString();
        $uuid2 = Uuid::uuid4()->toString();

        $id1 = new UuidIdentity($uuid1);
        $id2 = new UuidIdentity($uuid1);
        $this->assertTrue($id1->isSame($id2));
        $this->assertTrue($id2->isSame($id1));

        $id3 = new UuidIdentity($uuid2);
        $this->assertFalse($id1->isSame($id3));
        $this->assertFalse($id3->isSame($id1));

        $anotherClassUuid = new class($uuid1) implements IdentityInterface
        {
            protected string $uuid;

            public function __construct(string  $uuid)
            {
                $this->uuid = $uuid;
            }

            public function getUuid(): string
            {
                return $this->uuid;
            }

            public function isSame(IdentityInterface $identity): bool
            {
                throw new \Error("Dont use this method");
            }
        };

        $this->assertFalse($id1->isSame($anotherClassUuid));
    }
}