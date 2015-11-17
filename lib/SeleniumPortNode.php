<?php
class SeleniumPortNode
{
    protected $snapshot;

    protected $identifier;

    public function __construct($raw)
    {
        $this->snapshot = (array)$raw;
    }

    public function getName()
    {
        foreach ($this->snapshot['@attributes'] as $key => $attribute) {
            if ($key === 'name') {
                return $attribute;
            }
        }
    }

    public function getValue()
    {
        foreach ($this->snapshot['@attributes'] as $key => $attribute) {
            if ($key === 'value') {
                return $attribute;
            }
        }
    }

    public function setValue($value)
    {
        $file = SetPortValueTask::getPhunitFilename();
        $settings = simplexml_load_file($file);

        foreach ($settings->php->children() as $item) {
            $itemName = (string)$item->attributes()->{'name'};

            if ($itemName === $this->getName()) {
                $item->attributes()->{'value'} = $value;
                file_put_contents(SetPortValueTask::getPhunitFilename(), $settings->saveXML());
            }
        }

        return $this;
    }

    public static function buildSeleniumServerPortFromPhpunitFile()
    {
        $settings = simplexml_load_file(SetPortValueTask::getPhunitFilename());

        foreach ($settings->php->children() as $item) {
            $element = new self($item);

            if ($element->getName() === 'seleniumServerPort') {
                return $element;
            }
        }
    }
}

