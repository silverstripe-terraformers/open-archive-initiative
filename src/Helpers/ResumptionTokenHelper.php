<?php

namespace Terraformers\OpenArchive\Helpers;

use Exception;

/**
 * Resumption Tokens are a form of pagination, however, they also contain a level of validation.
 *
 * Each Resumption Token should represent a specific request, including whatever filters might have been applied as
 * part of that request, as well as representing a particular "page" in the Paginated List.
 *
 * The goal is to increase reliability of pagination by making sure that each requested "page" came from a request
 * containing the expected filters. EG: You can't send an unfiltered request for OAI Records, see that there are 10
 * pages, and then decide to request page=10 with some filters now applied. The Token itself would be aware that a
 * different filter has been applied, and it would be invalid.
 */
class ResumptionTokenHelper
{

    public static function generateResumptionToken(
        string $verb,
        int $page,
        ?string $from = null,
        ?string $until = null,
        ?int $set = null
    ): string {
        // Every Resumption Token must include a verb and page
        $parts = [
            'page' => $page,
            'verb' => $verb,
        ];

        if ($from) {
            $parts['from'] = $from;
        }

        if ($until) {
            $parts['until'] = $until;
        }

        if ($set) {
            $parts['set'] = $set;
        }

        return base64_encode(json_encode($parts));
    }

    public static function getPageFromResumptionToken(
        string $resumptionToken,
        string $expectedVerb,
        ?string $expectedFrom = null,
        ?string $expectedUntil = null,
        ?int $expectedSet = null
    ): int {
        $resumptionParts = static::getResumptionTokenParts($resumptionToken);

        // Grab the array values of our Resumption Token or default those values to null
        $resumptionPage = $resumptionParts['page'] ?? null;
        $resumptionVerb = $resumptionParts['verb'] ?? null;
        $resumptionFrom = $resumptionParts['from'] ?? null;
        $resumptionUntil = $resumptionParts['until'] ?? null;
        $resumptionSet = $resumptionParts['set'] ?? null;

        // Every Resumption Token should include (at the very least) the active page, if it doesn't, then it's invalid
        if (!$resumptionPage) {
            throw new Exception('Invalid resumption token');
        }

        // If any of these values do not match the expected values, then this Resumption Token is invalid
        if ($resumptionVerb !== $expectedVerb
            || $resumptionFrom !== $expectedFrom
            || $resumptionUntil !== $expectedUntil
            || $resumptionSet !== $expectedSet
        ) {
            throw new Exception('Invalid resumption token');
        }

        // The Resumption Token is valid, so we can return whatever value we have for page
        return $resumptionPage;
    }

    protected static function getResumptionTokenParts(string $resumptionToken): array
    {
        $decode = base64_decode($resumptionToken, true);

        // We can't do anything with an invalid encoded value
        if (!$decode) {
            throw new Exception('Invalid resumption token');
        }

        $resumptionParts = json_decode($decode, true);

        // We expect all Resumption Tokens to decode to an array
        if (!is_array($resumptionParts)) {
            throw new Exception('Invalid resumption token');
        }

        return $resumptionParts;
    }

}
