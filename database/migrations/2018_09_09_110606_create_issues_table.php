<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->string('issue_name');
            $table->text('issue_desc')->nullable();
            $table->string('issue_status');
            $table->dateTime('started_date')->nullable();
            $table->dateTime('closed_date')->nullable();
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
        Schema::dropIfExists('issues');
    }
}
