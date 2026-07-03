<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // unique index on license_plate does not exist at DB level;
        // uniqueness for non-deleted rows is enforced via Laravel validation only.
    }

    public function down(): void {}
};
