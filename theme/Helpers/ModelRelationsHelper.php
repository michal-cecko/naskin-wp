<?php

namespace Theme\Helpers;

class ModelRelationsHelper
{
    public static function deleteNotSubmittedRelatedRecords($parent, $relation, $submittedRelatedRecordsIDs): void
    {
        $relationName = $relation;
        $relation = $parent->$relationName();
        $relatedTableName = $relation->getRelated()->getTable();
        $recordsToDelete = $parent->$relationName()->whereNotIn($relatedTableName . '.id', $submittedRelatedRecordsIDs)->get();
        if (!empty($recordsToDelete)) {
            $recordsToDelete->each->delete();
        }
    }
}