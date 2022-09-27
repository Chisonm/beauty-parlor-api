<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->nullable()->unique();
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->enum('role', [User::ROLE_ADMIN, User::ROLE_VENDOR, User::ROLE_CLIENT])->default(User::ROLE_VENDOR);
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])->default(User::STATUS_ACTIVE);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
