<?php
namespace SeleniumSetup\Binary;

interface BinaryInterface
{
    public function setLabel($label);
    public function getLabel();
    public function setVersion($version);
    public function getVersion();
    public function setDownloadUrl($downloadUrl);
    public function getDownloadUrl();
    public function setBinName($binName);
    public function getBinName();
    public function setOs($osName);
    public function getOs();
    public function setOsType($osType);
    public function getOsType();
    public function toArray();
}