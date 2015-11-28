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
}