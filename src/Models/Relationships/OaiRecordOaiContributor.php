<?php

namespace Terraformers\OpenArchive\Models\Relationships;

use SilverStripe\ORM\DataObject;
use Terraformers\OpenArchive\Models\OaiMember;
use Terraformers\OpenArchive\Models\OaiRecord;

/**
 * @property int $ParentID
 * @property int $ContributorID
 * @method OaiRecord Parent()
 * @method OaiMember Contributor()
 */
class OaiRecordOaiContributor extends DataObject
{

    private static string $table_name = 'OaiRecordOaiContributor';

    private static array $has_one = [
        'Parent' => OaiRecord::class,
        'Contributor' => OaiMember::class,
    ];

    private static array $owned_by = [
        'Parent',
    ];

}