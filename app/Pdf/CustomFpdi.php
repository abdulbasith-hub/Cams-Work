<?php
namespace App\Pdf;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\FpdiPdfParser\PdfParser as FpdiPdfParser;
use setasign\FpdiPdfParser\PdfParser\CrossReference\CompressedReader;
use setasign\FpdiPdfParser\PdfParser\CrossReference\CorruptedReader;

class CustomFpdi extends Fpdi
{
    protected $pdfParserClass = null;

    public function setPdfParserClass($pdfParserClass)
    {
        $this->pdfParserClass = $pdfParserClass;
    }

    protected function getPdfParserInstance(StreamReader $streamReader, array $parserParams = [])
    {
        if ($this->pdfParserClass !== null) {
            return new $this->pdfParserClass($streamReader, $parserParams);
        }

        return parent::getPdfParserInstance($streamReader, $parserParams);
    }

    public function getXrefInfo()
    {
        foreach (array_keys($this->readers) as $readerId) {
            $crossReference = $this->getPdfReader($readerId)->getParser()->getCrossReference();
            $readers = $crossReference->getReaders();
            foreach ($readers as $reader) {
                if ($reader instanceof CompressedReader) {
                    return 'compressed';
                }
                if ($reader instanceof CorruptedReader) {
                    return 'corrupted';
                }
            }
        }

        return 'normal';
    }

    public function isSourceFileEncrypted()
    {
        $reader = $this->getPdfReader($this->currentReaderId);
        if ($reader && $reader->getParser() instanceof FpdiPdfParser) {
            return $reader->getParser()->getSecHandler() !== null;
        }

        return false;
    }
}
