<?php

use PHPUnit\Framework\TestCase;
use ImapRecipient\Helpers\MailStructure;

class MailStructureTest extends TestCase
{
    /**
     * @dataProvider filenamesProvider
     *
     * @param $part
     * @param $filename
     */
    public function testGetFilenameFromPart($part, $filename)
    {
        $this->assertEquals($filename, MailStructure::getFilenameFromPart($part));
    }

    /**
     * @dataProvider subjectsProvider
     *
     * @param string $encodeString
     * @param string $decodeString
     */
    public function testGetSubjectDecode(string $encodeString, string $decodeString)
    {
        $this->assertEquals($decodeString, MailStructure::getSubjectDecode($encodeString));
    }

    /**
     * @return array
     */
    public function subjectsProvider(): array
    {
        return [
            'BASE_64' => ['=?utf-8?B?VGVzdCBiYXNlNjQgc3RyaW5nIGRlY29kZQ==?=', 'Test base64 string decode'],
            //'IMAP_8BIT' => ['=?utf-8?Q?VGVzdCBiYXNlNjQgc3RyaW5nIGRlY29kZQ==?=', 'Test 8bit string decode'], //ignore when can include imap library with function and constants
            'Without encoding' => ['Test not encoding string', 'Test not encoding string']
        ];
    }

    /**
     * @return array
     */
    public function filenamesProvider(): array
    {
        $parameters = new stdClass();
        $parameters->attribute = 'name';
        $parameters->value = 'testName.png';

        $dParameters = new stdClass();
        $dParameters->attribute = 'filename';
        $dParameters->value = 'testFilename.png';


        $partParameters = new \stdClass();
        $partParameters->ifparameters = 1;
        $partParameters->parameters = [
            $parameters
        ];

        $partDParameters = new \stdClass();
        $partDParameters->ifdparameters = 1;
        $partDParameters->dparameters = [
            $dParameters
        ];

        $partBlank = new stdClass();

        return [
            [$partParameters, 'testName.png'],
            [$partDParameters, 'testFilename.png'],
            [$partBlank, '']
        ];
    }
}
