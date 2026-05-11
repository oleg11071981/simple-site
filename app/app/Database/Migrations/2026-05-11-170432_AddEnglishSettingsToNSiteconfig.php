<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnglishSettingsToNSiteconfig extends Migration
{
    public function up()
    {
        // Добавляем английские версии настроек
        $settings = [
            ['parameter' => 'SiteName_en', 'value' => ''],
            ['parameter' => 'Slogan_en', 'value' => ''],
            ['parameter' => 'MainText_en', 'value' => ''],
            ['parameter' => 'Keywords_en', 'value' => ''],
            ['parameter' => 'Description_en', 'value' => ''],
        ];

        $builder = $this->db->table('n_siteconfig');
        foreach ($settings as $setting) {
            $exists = $builder->where('parameter', $setting['parameter'])->get()->getRow();
            if (!$exists) {
                $builder->insert($setting);
            }
        }
    }

    public function down()
    {
        $parameters = ['SiteName_en', 'Slogan_en', 'MainText_en', 'Keywords_en', 'Description_en'];
        $this->db->table('n_siteconfig')->whereIn('parameter', $parameters)->delete();
    }
}