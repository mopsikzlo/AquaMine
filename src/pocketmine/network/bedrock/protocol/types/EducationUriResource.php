<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

use pocketmine\network\mcpe\NetworkBinaryStream;

final class EducationUriResource{
    public $buttonName;
    public $linkUri;

    public function __construct(string $buttonName, string $linkUri){
        $this->buttonName = $buttonName;
        $this->linkUri = $linkUri;
    }

    public function getButtonName() : string{ return $this->buttonName; }

    public function getLinkUri() : string{ return $this->linkUri; }

    public static function read(NetworkBinaryStream $in) : self{
        $buttonName = $in->getString();
        $linkUri = $in->getString();
        return new self($buttonName, $linkUri);
    }

    public function write(NetworkBinaryStream $out) : void{
        $out->putString($this->buttonName);
        $out->putString($this->linkUri);
    }
}