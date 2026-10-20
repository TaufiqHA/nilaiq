<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LegacySettingsConsolidator
{
    /**
     * Keep the legacy global settings on the oldest teacher account and
     * remove the identical copies that an earlier migration created.
     */
    public function consolidate(): void
    {
        $legacy = DB::table('settings')->whereNull('user_id')->orderBy('id')->first();

        if ($legacy === null) {
            return;
        }

        $oldestUser = DB::table('users')
            ->where('role', '!=', 'admin')
            ->orderBy('id')
            ->first();

        if ($oldestUser === null) {
            return;
        }

        $ignoredColumns = ['id', 'user_id', 'created_at', 'updated_at'];
        $legacyData = $this->comparableData((array) $legacy, $ignoredColumns);

        $oldestSetting = DB::table('settings')->where('user_id', $oldestUser->id)->first();

        if ($oldestSetting === null) {
            DB::table('settings')->where('id', $legacy->id)->update([
                'user_id' => $oldestUser->id,
                'updated_at' => now(),
            ]);
        } else {
            // The oldest account already has a (possibly edited) row.
            DB::table('settings')->where('id', $legacy->id)->delete();
        }

        $copies = DB::table('settings')
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $oldestUser->id)
            ->get();

        foreach ($copies as $copy) {
            if ($this->comparableData((array) $copy, $ignoredColumns) === $legacyData) {
                DB::table('settings')->where('id', $copy->id)->delete();
            }
        }
    }

    /**
     * Extract the data columns used to detect duplicated settings.
     *
     * @param  array<string, mixed>  $row
     * @param  array<int, string>  $ignoredColumns
     * @return array<string, mixed>
     */
    private function comparableData(array $row, array $ignoredColumns): array
    {
        $data = array_diff_key($row, array_flip($ignoredColumns));
        ksort($data);

        return $data;
    }
}
