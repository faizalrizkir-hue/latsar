<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait BootstrapsCoreTables
{
    protected function bootstrapCoreTables(): void
    {
        $this->createAccountsTable();
        $this->createElementTeamAssignmentsTable();
        $this->createNotificationsTables();
        $this->createDmsTables();
        $this->createElement1Tables();
    }

    protected function resetCoreTables(): void
    {
        foreach ([
            'element1_kegiatan_asurans_edit_logs',
            'element1_kegiatan_asurans',
            'notification_reads',
            'notifications',
            'dms_files',
            'dms_documents',
            'element_team_assignments',
            'accounts',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }
    }

    private function createAccountsTable(): void
    {
        if (Schema::hasTable('accounts')) {
            return;
        }

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('display_name', 150)->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->string('role', 50)->default('administrator');
            $table->boolean('active')->default(true);
            $table->string('last_login_ip', 64)->nullable();
            $table->string('last_login_device', 255)->nullable();
            $table->timestamps();
        });
    }

    private function createElementTeamAssignmentsTable(): void
    {
        if (Schema::hasTable('element_team_assignments')) {
            return;
        }

        Schema::create('element_team_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('element_slug', 50);
            $table->string('coordinator_username', 100)->nullable();
            $table->text('member_usernames')->nullable();
            $table->timestamps();
        });
    }

    private function createNotificationsTables(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('subtopic_title', 200);
                $table->text('statement')->nullable();
                $table->unsignedBigInteger('row_id')->nullable();
                $table->string('element_slug', 50)->nullable();
                $table->string('subtopic_slug', 100)->nullable();
                $table->string('coordinator_name', 150);
                $table->string('coordinator_username', 100);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (Schema::hasTable('notification_reads')) {
            return;
        }

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->string('username', 100);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['notification_id', 'username'], 'notification_reads_notification_user_unique');
        });
    }

    private function createDmsTables(): void
    {
        if (!Schema::hasTable('dms_documents')) {
            Schema::create('dms_documents', function (Blueprint $table) {
                $table->id();
                $table->integer('year');
                $table->string('type', 150);
                $table->string('doc_no', 100)->unique();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('tag', 150)->nullable();
                $table->string('status', 20)->default('Aktif');
                $table->string('uploader', 150)->nullable();
                $table->string('updated_by', 150)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('dms_files')) {
            return;
        }

        Schema::create('dms_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('doc_no', 100)->nullable();
            $table->string('doc_name', 200)->nullable();
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('storage_driver', 50)->default('public');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->index('doc_no');
        });
    }

    private function createElement1Tables(): void
    {
        if (!Schema::hasTable('element1_kegiatan_asurans')) {
            Schema::create('element1_kegiatan_asurans', function (Blueprint $table) {
                $table->unsignedInteger('id')->primary();
                $table->string('pernyataan', 255);
                $table->string('level', 50)->nullable();
                $table->decimal('skor', 10, 2)->nullable();
                $table->text('analisis_bukti')->nullable();
                $table->text('analisis_nilai')->nullable();
                $table->text('grad_l1_catatan')->nullable();
                $table->text('grad_l2_catatan')->nullable();
                $table->text('grad_l3_catatan')->nullable();
                $table->text('grad_l4_catatan')->nullable();
                $table->text('grad_l5_catatan')->nullable();
                $table->text('evidence')->nullable();
                $table->boolean('verified')->default(false);
                $table->string('dokumen_path', 255)->nullable();
                $table->longText('doc_file_ids')->nullable();
                $table->longText('level_validation_state')->nullable();
                $table->text('verify_note')->nullable();
                $table->boolean('qa_verified')->default(false);
                $table->string('qa_verified_by', 100)->nullable();
                $table->timestamp('qa_verified_at')->nullable();
                $table->text('qa_verify_note')->nullable();
                $table->longText('qa_level_validation_state')->nullable();
                $table->text('qa_follow_up_recommendation')->nullable();
            });
        }

        if (Schema::hasTable('element1_kegiatan_asurans_edit_logs')) {
            return;
        }

        Schema::create('element1_kegiatan_asurans_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_id');
            $table->string('pernyataan', 255)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('action', 50)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
