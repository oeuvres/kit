<?php declare(strict_types=1);

/**
 * BitSet backed on a String 
 * 
 * Part of Teinte https://github.com/oeuvres/teinte
 * Copyright (c) 2023 frederic.glorieux@fictif.org
 * BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause
 */

namespace Oeuvres\Kit\Bitset;
 
use InvalidArgumentException, OutOfRangeException;

class BitString extends BitSet
{
    /**
     * String implementation
     */
    private string $data;
    /**
     * Biggest char index
     */
    private int $charLength = -1;
    /**
     * Allocated chars
     */
    private int $charAlloc = 8;


    public function __construct()
    {
        $this->data = str_repeat("\0", $this->charAlloc);
    }

    static public function nextSquare(int $x)
    {
        if ($x == 0) return 1;
        $x--;
        $x |= $x >> 1;
        $x |= $x >> 2;
        $x |= $x >> 4;
        $x |= $x >> 8;
        $x |= $x >> 16;
        $x |= $x >> 32;
        return $x + 1;
    }

    /**
     * Test bit index for write, and return char index
     */
    private function resize(int $bitIndex): int
    {
        if ($bitIndex < 0) {
            throw new OutOfRangeException("\$bitIndex={$bitIndex}, negative index not supported");
        }
        $this->length = max($this->length, $bitIndex + 1);
        $charIndex = $bitIndex >> 3;
        // grow by power of 2 is said to be faster in Java
        if ($charIndex >= $this->charAlloc) {
            $charAlloc = self::nextSquare($charIndex + 1);
            $this->data .= str_repeat("\0", $charAlloc - $this->charAlloc);
            $this->charAlloc = $charAlloc;
        }
        $this->charLength = max($this->charLength, $charIndex + 1);
        return $charIndex;
    }

    public function set(int $bitIndex): void
    {
        $charIndex = $this->resize($bitIndex);
        // bits can shift out left side
        $bitMask = 1 << ($bitIndex % 8);
        $codePoint = ord($this->data[$charIndex]) | $bitMask;
        $this->data[$charIndex] = chr( $codePoint );
    }



    /**
     * Returns the bool value of the bit at the specified index
     *
     * @throws OutOfRangeException
     *
     * @param int $bitIndex
     *
     * @return boolean
     */
    public function get(int $bitIndex): bool
    {
        if ($bitIndex < 0) {
            throw new OutOfRangeException("\$bitIndex={$bitIndex}, negative index not supported");
        }
        // because size is not defined, answer
        if ($bitIndex >= $this->length) {
            return false;
        }
        $charIndex = $bitIndex >> 3;
        $bitMask = 1 << ($bitIndex & 255);
        return boolval(ord($this->data[$charIndex]) | $bitMask);
    }

    public function getInt(int $bitIndex, int $bitCount): int
    {
        if ($bitCount > PHP_INT_SIZE << 3) {
            throw new OutOfRangeException("\$bitCount={$bitCount} is bigger than the size of int on your system: " . (PHP_INT_SIZE << 3));
        }
        $bitMax = $bitIndex + $bitCount;
        if ($bitMax > $this->length) {
            throw new OutOfRangeException("\$bitCount+\$bitCount={$bitMax} exceed available bits: {$this->length}");
        }
        return 0;
    }

    public function toBytes(): string {
        return substr($this->data, 0, $this->charLength);
    }

    public function fromBytes(string $bytes = null): void {
        if (empty($bytes)) {
            $this->data = '';
        }
        else {
            $this->data = $bytes;
        }
    }

    /**
     * Returns a human-readable string representation of the bit set as binary
     *
     * @return string
     */
    public function __toString(): string
    {
        $bin = "";
        $len = $this->charLength;
        for($i = 0 ; $i < $len ; $i++) {
            $bin .= ' ' . strrev(decbin(ord($this->data[$i])));
        }
        return trim($bin);
    }

}
