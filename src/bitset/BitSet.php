<?php declare(strict_types=1);

/**
 * BitSet 
 * 
 * Part of Teinte https://github.com/oeuvres/teinte
 * Copyright (c) 2023 frederic.glorieux@fictif.org
 * BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause
 */

namespace Oeuvres\Kit\Bitset;
 
use InvalidArgumentException, OutOfRangeException;

abstract class BitSet
{
    /**
     * Biggest bit index
     */
    protected int $length = 0;
    /**
     * Returns the highest set bit plus one
     *
     * @return int
     */
    public function length(): int
    {
        return $this->length;
    }

    /**
     * Returns the bits as binary string of bytes
     */
    abstract public function toBytes(): string;
    
    /**
     * Base 64 export of bytes
     */
    public function toBase64(): string
    {
        return base64_encode($this->toBytes());
    }

    /**
     * Import bits from a binary string of bytes
     */
    abstract public function fromBytes(string $bytes): void;
    /**
     * From Base 64 import bytes
     */
    public function fromBase64(string $base64=null): void
    {
        if (empty($base64)) {
            $this->length = 0;
            $this->fromBytes('');
            return;
        }
        // maybe a base64 URL compat
        $base64 = strtr($base64, '-_', '+/');
        $bytes = base64_decode($base64, true);
        if ($bytes === false) {
            throw new InvalidArgumentException("Invalid base64 string: $base64");
        }
        $this->fromBytes($bytes);
    }

    /**
     * Returns a human-readable string representation of the bit set as binary
     * in little endian order.
     */
    public function __toString(): string
    {
        $chars = '';
        $bytes = $this->toBytes();
        $len = strlen($bytes);
        for($i = 0 ; $i < $len ; $i++) {
            $chars .= ' ' . strrev(decbin(ord($bytes[$i])));
        }
        return trim($chars);
    }
    /**
     * Sets the bit at the specified index to true.
     */
    abstract public function set(int $bitIndex):void;
    /**
     * Get the bit at the specified index.
     */
    abstract public function get(int $bitIndex):bool;

}