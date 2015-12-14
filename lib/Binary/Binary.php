<?php
namespace SeleniumSetup\Binary;

class Binary implements BinaryInterface
{
    protected $label;
    protected $version;
    protected $downloadUrl;
    protected $binName;
    protected $os = null;
    protected $osType = null;

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
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param null $os
     * @return Binary
     */
    public function setOs($os)
    {
        $this->os = $os;
        return $this;
    }

    /**
     * @return null
     */
    public function getOsType()
    {
        return $this->osType;
    }

    /**
     * @param null $osType
     * @return Binary
     */
    public function setOsType($osType)
    {
        $this->osType = $osType;
        return $this;
    }

    public function toArray()
    {
        return (array)get_object_vars($this);
    }
}