<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCategoriesToCategoriesTable extends Migration
{
    public function up()
    {
        // Insert the categories into the categories table
        DB::table('categories')->insert([
            ['name' => 'Food'],
            ['name' => 'Transportation'],
            ['name' => 'Utilities'],
            ['name' => 'Entertainment'],
            ['name' => 'Health'],
        ]);
    }

    public function down()
    {
        // Optionally, remove the inserted categories during rollback
        DB::table('categories')->whereIn('name', [
            'Food', 'Transportation', 'Utilities', 'Entertainment', 'Health'
        ])->delete();
    }
}
