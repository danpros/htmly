<?php

class opml
{
    private $data;
    private $writer;

    public function __construct($data)
    {
        $this->data = $data;
        $this->writer = new XMLWriter();
        $this->writer->openMemory();
    }

    public function render()
    {
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('opml');
        $this->writer->writeAttribute('version', '2.0');

        // Header
        $this->writer->startElement('head');
        foreach ($this->data['head'] as $key => $value) {
            $this->writer->writeElement($key, $value);
        }
        $this->writer->writeElement('dateModified', date("D, d M Y H:i:s T"));
        $this->writer->endElement();

        // Body
        $this->writer->startElement('body');
        foreach ($this->data['body'] as $outlines) {
            $this->writer->startElement('outline');
            foreach ($outlines as $key => $value) {
                $this->writer->writeAttribute($key, $value);
            }
            $this->writer->endElement();
        }
        $this->writer->endElement();

        $this->writer->endElement();
        $this->writer->endDocument();
        return $this->writer->outputMemory();
    }
}
