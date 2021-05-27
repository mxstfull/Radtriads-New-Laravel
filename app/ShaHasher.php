<?php
namespace App; 

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;

class ShaHasher extends AbstractHasher implements HasherContract {

    public function make($value, array $options = array()) {
        // $value = env('SALT', '').$value;
        return sha1($value);
    }

    public function check($value, $hashedValue, array $options = array()) {
        return $this->make($value) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = array()) {
        return false;
    }

}
