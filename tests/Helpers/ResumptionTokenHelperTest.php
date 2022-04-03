<?php

namespace Terraformers\OpenArchive\Tests\Helpers;

use ReflectionClass;
use SilverStripe\Dev\SapphireTest;
use Terraformers\OpenArchive\Helpers\ResumptionTokenHelper;

class ResumptionTokenHelperTest extends SapphireTest
{

    public function testResumptionToken(): void
    {
        $expectedParts = [
            'verb' => 'ListRecords',
            'page' => 3,
            'from' => '2022-01-01T01:00:00Z',
            'until' => '2022-01-01T02:00:00Z',
            'set' => 2,
        ];

        // Generate our Token
        $token = ResumptionTokenHelper::generateResumptionToken(
            'ListRecords',
            3,
            '2022-01-01T01:00:00Z',
            '2022-01-01T02:00:00Z',
            2
        );

        // Now decode that Token
        $reflection = new ReflectionClass(ResumptionTokenHelper::class);
        $method = $reflection->getMethod('getResumptionTokenParts');
        $method->setAccessible(true);
        $resumptionParts = $method->invoke(null, $token);

        // And check that the Token that was encoded and decoded matches our expected values
        $this->assertEquals(ksort($expectedParts), ksort($resumptionParts));
        // And check that our "get page number" method works as well
        $this->assertEquals(
            3,
            ResumptionTokenHelper::getPageFromResumptionToken(
                $token,
                'ListRecords',
                '2022-01-01T01:00:00Z',
                '2022-01-01T02:00:00Z',
                2
            )
        );
    }

}
