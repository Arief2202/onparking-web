<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MallList;
use App\Models\spot_parkir;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        MallList::insert([
            'namaMall' => 'Royal Plaza Surabaya', 'alamatMall' => 'Jl. A Yani No. 16-18, Wonokromo, Kota Surabaya', 'openTimeMall' => '11:00 - 22:00', 'fotoMall' => 'https://live.staticflickr.com/65535/52364704472_1067879698_b.jpg'
        ]);
        MallList::insert([
            ['namaMall' => 'Grand City Mall Surabaya', 'alamatMall' => 'Jl. Walikota Mustajab No.1, Ketabang, Kec. Genteng, Kota Surabaya', 'openTimeMall' => '11:00 - 21:00', 'fotoMall' => 'https://pict.sindonews.net/dyn/850/pena/news/2020/12/08/156/260586/grand-city-surabaya-miliki-fasilitas-lengkap-untuk-wisatawan-jpz.jpg'],
            ['namaMall' => 'Galaxy Mall Surabaya', 'alamatMall' => 'Jl. Dharmahusada Indah Timur No. 35-37 (Jl. Dr. Ir. H. Soekarno), Kota Surabaya', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Galaxy_Mall%2C_Surabaya.jpg'],
            ['namaMall' => 'Ciputra World Surabaya', 'alamatMall' => 'Jl. Mayjen Sungkono No.89, Gn. Sari, Kec. Dukuhpakis, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://dbijapkm3o6fj.cloudfront.net/resources/2680,1004,1,6,4,0,600,450/-4601-/20150331195708/ciputra-world-mall.jpeg'],
            ['namaMall' => 'Marvell City Mall', 'alamatMall' => 'Jl. Ngagel No.123, Ngagel, Kec. Wonokromo, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://uploads.tapatalk-cdn.com/20160326/09f14ee81b8e41f863e6e590b8abfd01.jpg'],
            ['namaMall' => 'Lenmarc Mall', 'alamatMall' => 'Jl. Mayjend. Jonosewojo No.9, Pradahkalikendal, Kec. Dukuhpakis, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://rentalmotorsurabaya.id/wp-content/uploads/2020/10/rental-motor-surabaya-lenmarc.jpg'],
            ['namaMall' => 'Marvell City Mall', 'alamatMall' => 'Jl. Ngagel No.123, Ngagel, Kec. Wonokromo, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://uploads.tapatalk-cdn.com/20160326/09f14ee81b8e41f863e6e590b8abfd01.jpg'],
            
            ['namaMall' => 'East Coast Center Mal', 'alamatMall' => 'Pakuwon City, Jl. Raya Laguna KJW Putih Tambak No.2, Kejawaan Putih Tamba, Kec. Mulyorejo, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://imgsrv2.voi.id/QBzCFI30KjV1SQe3QHW-tWfvKiYQy7RKy8Nmm0vQJ0Y/auto/1280/853/sm/1/bG9jYWw6Ly8vcHVibGlzaGVycy8yMTYzOC8yMDIwMTIwMjA2MTktbWFpbi5jcm9wcGVkXzE2MDY4NjQ3NzguanBn.jpg'],
            ['namaMall' => 'WTC Surabaya', 'alamatMall' => 'Jl. Pemuda No.27-31, Embong Kaliasin, Kec. Genteng, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://www.intiland.com/wp-content/uploads/2019/08/WTC-Crop-2.png'],
            ['namaMall' => 'Pasar Atom', 'alamatMall' => 'Jl. Bunguran No.45, Bongkaran, Kec. Pabean Cantikan, Kota SBY', 'openTimeMall' => '11:00 - 20:00', 'fotoMall' => 'https://cdn1-production-images-kly.akamaized.net/3KpAPNDTwFSoKKyoVDuwgAZpWX0=/640x360/smart/filters:quality(75):strip_icc():format(jpeg)/kly-media-production/medias/2878429/original/026278000_1565435884-Pasar_Atum_Mall-ok.jpg'],
        ]);
        User::insert([
            'name' => 'Admin', 'email' => 'admin', 'password' => '$2y$10$Gd2efjIgucmJI1/2TSYG3eH9y2HWbfRx.tSfdHEF8wz/mC0EIDZxy', 'role' => '1', 'card_id' => '000001'
        ]);
        User::insert([
            'name' => 'Arief', 'email' => 'arief.d2202@gmail.com', 'password' => '$2y$10$Gd2efjIgucmJI1/2TSYG3eH9y2HWbfRx.tSfdHEF8wz/mC0EIDZxy', 'role' => '0', 'card_id' => 'B3:41:DC:AA'
        ]);

        
        spot_parkir::insert([
            ['mall_id' => '1', 'lantai' => '1', 'blok' => 'A1', 'harga' => '1000',],
            ['mall_id' => '1', 'lantai' => '1', 'blok' => 'A2', 'harga' => '1000',],
            ['mall_id' => '1', 'lantai' => '2', 'blok' => 'A1', 'harga' => '1000',],
            ['mall_id' => '2', 'lantai' => '1', 'blok' => 'A1', 'harga' => '1000',],
            ['mall_id' => '2', 'lantai' => '1', 'blok' => 'A2', 'harga' => '1000',],
            ['mall_id' => '2', 'lantai' => '2', 'blok' => 'A1', 'harga' => '1000',],
            ['mall_id' => '3', 'lantai' => '1', 'blok' => 'A1', 'harga' => '1000',],
        ]);

        
        \App\Models\User::factory(50)->create();
    }
}
