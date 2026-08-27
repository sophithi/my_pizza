<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salespersons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // Copy users who are currently linked as salespersons to customers
        $linkedUserIds = DB::table('customers')
            ->whereNotNull('salesperson_id')
            ->distinct()
            ->pluck('salesperson_id');

        $users = DB::table('users')->whereIn('id', $linkedUserIds)->get();

        foreach ($users as $user) {
            DB::table('salespersons')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'status' => $user->is_active ? 'active' : 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salespersons');
    }
};
