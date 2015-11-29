<?php
namespace SeleniumSetup\Binary;

class Binary implements BinaryInterface
{
    protected $label;
    protected $version;
    protected $downloadUrl;
    protected $binName;
    protected $osSpecific = null;

    public static function createFromObject(\stdClass $object)
    {
        $binary = new Binary();

        foreach ($object as $key => $value) {
            if (property_exists($binary, $key)) {
                $binary->{$key} = $value;
            }
        }

        return $binary;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return Binary
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     * @return Binary
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * @param mixed $downloadUrl
     * @return Binary
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBinName()
    {
        return $this->binName;
    }

    /**
     * @param mixed $binName
     * @return Binary
     */
    public function setBinName($binName)
    {
        $this->binName = $binName;
        return $this;
    }

    /**
     * @return null
     */
    public function getOsSpecific()
    {
        return $this->osSpecific;
    }

    /**
     * @param null $osSpecific
     * @return Binary
     */
    public function setOsSpecific($osSpecific)
    {
        $this->osSpecific = $osSpecific;
        return $this;
    }

}