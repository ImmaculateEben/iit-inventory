<?php

namespace App\Support\Audit;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(
        string $actionCode,
        string $targetType,
        ?int $targetId = null,
        mixed $oldOrSummary = null,
        ?array $newData = null
    ): AuditLog {
        // Determine summary and metadata based on argument types
        if (is_string($oldOrSummary)) {
            $summary = $oldOrSummary;
            $metadata = $newData;
        } elseif (is_array($oldOrSummary)) {
            $summary = str_replace('_', ' ', $actionCode);
            $metadata = array_filter(['old' => $oldOrSummary, 'new' => $newData]);
        } else {
            $summary = str_replace('_', ' ', $actionCode);
            $metadata = $newData;
        }

        return AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action_code' => $actionCode,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'summary' => $summary,
            'metadata_json' => $metadata ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
