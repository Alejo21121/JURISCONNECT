<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 👇 TRIGGER MEJORADO: NO eliminar notificación de bienvenida
        DB::unprepared("
            CREATE OR REPLACE FUNCTION delete_read_notifications()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.read_at IS NOT NULL AND NEW.type != 'App\\Notifications\\BienvenidaCambioPassword' THEN
                    DELETE FROM notifications WHERE id = NEW.id;
                    RETURN NULL;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER auto_delete_read_notifications
            AFTER UPDATE ON notifications
            FOR EACH ROW
            EXECUTE FUNCTION delete_read_notifications();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS auto_delete_read_notifications ON notifications");
        DB::unprepared("DROP FUNCTION IF EXISTS delete_read_notifications()");

        Schema::dropIfExists('notifications');
    }
};
