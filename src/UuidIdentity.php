<?php

namespace Mnemesong\UuidIdentity;

use Mnemesong\IdentityInterface\IdentityInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class UuidIdentity implements IdentityInterface
{
    protected string $uuid;

    /**
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        Assert::true(Uuid::isValid($uuid), "Invalid Uuid");
        $this->uuid = $uuid;
    }


    /**
     * @param IdentityInterface $identity
     * @return bool
     */
    public function isSame(IdentityInterface $identity): bool
    {
        if(!is_a($identity, UuidIdentity::class)) {
            return false;
        }
        return $identity->getUuid() === $this->uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return static
     */
    public static function new(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
}