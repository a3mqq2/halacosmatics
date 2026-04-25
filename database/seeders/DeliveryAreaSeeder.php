<?php

namespace Database\Seeders;

use App\Models\DeliveryArea;
use Illuminate\Database\Seeder;

class DeliveryAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'السلماني الشرقي',  'price' => 15],
            ['name' => 'السلماني الغربي',  'price' => 15],
            ['name' => 'راس اعبيده',       'price' => 15],
            ['name' => 'سيدي حسين',        'price' => 15],
            ['name' => 'الصابري',          'price' => 15],
            ['name' => 'اللثامه',          'price' => 15],
            ['name' => 'ارض أزواوه',       'price' => 15],
            ['name' => 'سيدي يونس',        'price' => 15],
            ['name' => 'الوحيشي',          'price' => 15],
            ['name' => 'حي السلام',        'price' => 15],
            ['name' => 'بودزيره',          'price' => 15],
            ['name' => 'المساكن',          'price' => 15],
            ['name' => 'حي قطر',           'price' => 15],
            ['name' => 'الليثي',           'price' => 15],
            ['name' => 'حي دولار',         'price' => 15],
            ['name' => 'شبنه',             'price' => 15],
            ['name' => 'شارع سوريا',       'price' => 15],
            ['name' => 'فينيسيا',          'price' => 15],
            ['name' => 'طابلينو',          'price' => 15],
            ['name' => 'الكيش',            'price' => 15],
            ['name' => 'الماجوري',         'price' => 15],
            ['name' => 'السرتي',           'price' => 15],
            ['name' => 'ارض لملوم',        'price' => 15],
            ['name' => 'الحي الجامعي',     'price' => 15],
            ['name' => 'بوزغيبه',          'price' => 15],
            ['name' => 'الرحبه',           'price' => 15],
            ['name' => 'بلعون',            'price' => 15],
            ['name' => 'بن يونس',          'price' => 15],
            ['name' => 'حدائق',            'price' => 15],
            ['name' => 'الفويهات',         'price' => 15],
            ['name' => 'الكويفيه',         'price' => 20],
            ['name' => 'سيدي خليفه',       'price' => 20],
            ['name' => 'بنينا',            'price' => 20],
            ['name' => 'بوعطني',           'price' => 20],
            ['name' => 'الهواري',          'price' => 20],
            ['name' => 'قوارشه',           'price' => 20],
            ['name' => 'قنفوده',           'price' => 20],
            ['name' => 'قاريونس',          'price' => 20],
            ['name' => 'السيده عائشه',     'price' => 20],
            ['name' => 'بوهادي',           'price' => 25],
            ['name' => 'سي منصور',         'price' => 25],
            ['name' => 'الفعكات',          'price' => 25],
            ['name' => 'النواقيه',         'price' => 25],
            ['name' => 'الطلحيه',          'price' => 25],
            ['name' => 'سي علي',           'price' => 30],
        ];

        DeliveryArea::insert($areas);
    }
}
