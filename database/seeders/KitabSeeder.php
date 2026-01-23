<?php

namespace Database\Seeders;

use App\Models\Kitab;
use App\Models\KitabChapter;
use App\Models\KitabMaqolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KitabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the default Kitab
        $kitab = Kitab::firstOrCreate(
            ['slug' => 'nashaihul-ibad'],
            [
                'name' => 'Nashaihul Ibad',
                'author' => 'Syekh Nawawi Al-Bantani',
                'description' => 'Kitab Nashaihul Ibad merupakan karya Syekh Nawawi al-Bantani yang berisi kumpulan nasihat-nasihat berharga untuk umat Islam.',
                'is_active' => true,
                'urutan' => 1,
            ]
        );

        // Data 10 Bab Kitab Nashaihul Ibad
        $chapters = [
            [
                "nomor_bab" => 1,
                "judul_bab" => "Nasihat yang Berisi Dua Perkara",
                "deskripsi" => "Bab ini mengandung 30 nasihat, di mana setiap nasihat terdiri dari dua poin.",
                "maqolahs" => [
                    [
                        "judul" => "Iman dan Kepedulian Sosial",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                اَلْمَقَالَةُ الْأُوْلَى (قَالَ رَسُوْلُ اللهِ ﷺ: لَا يُؤْمِنُ أَحَدُكُمْ حَتَّى يُحِبَّ لِأَخِيْهِ مَا يُحِبُّ لِنَفْسِهِ)
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Maqolah yang pertama</strong> (Telah bersabda Rasulullah ﷺ: <span class="text-primary font-medium">Hendaklah kalian bersiwak</span>) Maksudnya biasekanlah bersiwak pada setiap waktu dan pada setiap keadaan (<span class="text-primary font-medium">Karena sesungguhnya dalam bersiwak itu ada sepuluh sifat</span>) Yang terpuji.
                            </p>
                        ',
                    ],
                    [
                        "judul" => "Dekat dengan Ulama",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                اَلْمَقَالَةُ الثَّانِيَةُ (لَازِمُوا الْعُلَمَاءَ وَالصَّالِحِيْنَ)
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Maqolah yang kedua</strong>: Senantiasa dekati para ulama dan orang-orang shalih. Karena dengan mendekati mereka, hatimu akan menjadi hidup. Sebagaimana bumi yang mati dihidupkan dengan air hujan.
                            </p>
                        ',
                    ],
                    [
                        "judul" => "Ilmu dan Maksiat",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                اَلْمَقَالَةُ الثَّالِثَةُ (شَيْئَانِ يُهْلِكَانِ الْإِنْسَانَ)
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Maqolah yang ketiga</strong>: Dua perkara yang menghancurkan manusia: <span class="text-primary font-medium">sedikit ilmu namun banyak bicara</span>, dan <span class="text-primary font-medium">banyak harta namun sedikit beramal</span>.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 2,
                "judul_bab" => "Nasihat yang Terdiri dari Tiga Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang masing-masing terdiri dari tiga poin atau perkara.",
                "maqolahs" => [
                    [
                        "judul" => "Tiga Perkara Penghancur",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                ثَلَاثٌ مُهْلِكَاتٌ: شُحٌّ مُطَاعٌ، وَهَوًى مُتَّبَعٌ، وَإِعْجَابُ الْمَرْءِ بِنَفْسِهِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Tiga perkara yang menghancurkan</strong>: <span class="text-primary font-medium">kikir yang ditaati</span>, <span class="text-primary font-medium">hawa nafsu yang diikuti</span>, dan <span class="text-primary font-medium">kekaguman seseorang terhadap dirinya sendiri</span>.
                            </p>
                        ',
                    ],
                    [
                        "judul" => "Tiga Perkara Menyelamatkan",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                ثَلَاثٌ مُنْجِيَاتٌ: خَشْيَةُ اللهِ فِي السِّرِّ وَالْعَلَانِيَةِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Tiga perkara yang menyelamatkan</strong>: takut kepada Allah dalam keadaan sepi maupun ramai, berlaku adil dalam keadaan ridha maupun marah, dan hemat dalam keadaan kaya maupun miskin.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 3,
                "judul_bab" => "Nasihat yang Terdiri dari Empat Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang masing-masing terdiri dari empat poin.",
                "maqolahs" => [
                    [
                        "judul" => "Empat Tanda Kebahagiaan",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                أَرْبَعَةٌ مِنْ عَلَامَاتِ السَّعَادَةِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Empat tanda kebahagiaan</strong>: istri yang shalihah, anak-anak yang berbakti, teman-teman yang baik, dan rezeki yang halal di negeri sendiri.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 4,
                "judul_bab" => "Nasihat yang Terdiri dari Lima Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang berisi lima poin.",
                "maqolahs" => [
                    [
                        "judul" => "Lima Wasiat Nabi Ibrahim",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                خَمْسُ وَصَايَا إِبْرَاهِيْمَ عَلَيْهِ السَّلَامُ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Nabi Ibrahim AS berwasiat kepada putranya</strong>: Wahai anakku, janganlah engkau berharap kepada makhluk, janganlah engkau iri dengan rezeki orang lain, jauhilah sifat tamak, hendaklah engkau selalu ridha dengan ketentuan Allah, dan seringkanlah mengingat kematian.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 5,
                "judul_bab" => "Nasihat yang Terdiri dari Enam Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang berisi enam poin.",
                "maqolahs" => [
                    [
                        "judul" => "Enam Akhlak Terpuji",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                سِتَّةُ أَخْلَاقٍ مَحْمُوْدَةٌ لِلْمُؤْمِنِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Enam akhlak yang harus dimiliki seorang mukmin</strong>: sabar ketika musibah, ridha dengan ketentuan Allah, ikhlas dalam beramal, zuhud terhadap dunia, dermawan kepada sesama, dan tawadhu kepada semua orang.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 6,
                "judul_bab" => "Nasihat yang Terdiri dari Tujuh Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang berisi tujuh poin.",
                "maqolahs" => [
                    [
                        "judul" => "Tujuh Golongan dalam Naungan Allah",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                سَبْعَةٌ يُظِلُّهُمُ اللهُ فِي ظِلِّهِ يَوْمَ لَا ظِلَّ إِلَّا ظِلُّهُ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Tujuh golongan yang dinaungi Allah pada hari tiada naungan kecuali naungan-Nya</strong>: imam yang adil, pemuda yang tumbuh dalam ibadah kepada Allah, orang yang hatinya terpaut ke masjid, dua orang yang saling mencintai karena Allah, orang yang diajak wanita cantik namun menolak karena takut Allah, orang yang bersedekah secara sembunyi, dan orang yang berdzikir dalam kesendirian hingga meneteskan air mata.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 7,
                "judul_bab" => "Nasihat tentang Delapan Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang berisi delapan poin.",
                "maqolahs" => [
                    [
                        "judul" => "Delapan Perkara yang Tidak Pernah Kenyang",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                ثَمَانِيَةٌ لَا تَشْبَعُ مِنْ ثَمَانِيَةٍ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Delapan perkara yang tidak pernah kenyang</strong>: mata tidak pernah kenyang memandang, bumi tidak pernah kenyang dari air hujan, api tidak pernah kenyang dari kayu bakar, alim tidak pernah kenyang dari ilmu, orang tamak tidak pernah kenyang dari kekayaan, laut tidak pernah kenyang dari air sungai, wanita tidak pernah kenyang dari perhiasan, dan orang zuhud tidak pernah kenyang dari ibadah.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 8,
                "judul_bab" => "Nasihat tentang Sembilan Perkara",
                "deskripsi" => "Bab ini membahas nasihat-nasihat yang berisi sembilan poin.",
                "maqolahs" => [
                    [
                        "judul" => "Sembilan Pintu Neraka",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                إِحْذَرُوا تِسْعَةَ أَشْيَاءَ تُؤَدِّي إِلَى النَّارِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Waspadalah terhadap sembilan perkara yang membawa ke neraka</strong>: menyekutukan Allah, sihir, membunuh jiwa yang diharamkan, memakan riba, memakan harta anak yatim, lari dari perang, menuduh wanita baik-baik berzina, durhaka kepada orang tua, dan meninggalkan shalat.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 9,
                "judul_bab" => "Nasihat tentang Sepuluh Perkara",
                "deskripsi" => "Bab ini memuat nasihat-nasihat yang terdiri dari sepuluh poin.",
                "maqolahs" => [
                    [
                        "judul" => "Sepuluh Nasihat Luqman kepada Anaknya",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                عَشْرُ نَصَائِحِ لُقْمَانَ لِابْنِهِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Luqman berkata kepada anaknya</strong>: Wahai anakku, jangan tidur ketika matahari terbit, jangan berjalan di pasar tanpa keperluan, jangan berdebat dengan orang bodoh, jangan kikir dengan kekayaanmu, jangan mensia-siakan waktu luangmu, jangan bergaul dengan orang fasik, jangan putus silaturahmi, jangan malas mencari ilmu, jangan menyia-nyiakan shalat, dan jangan lupa akan kematian.
                            </p>
                        ',
                    ],
                ],
            ],
            [
                "nomor_bab" => 10,
                "judul_bab" => "Nasihat-nasihat Umum",
                "deskripsi" => "Bab penutup berisi kumpulan nasihat umum dari para ulama salaf.",
                "maqolahs" => [
                    [
                        "judul" => "Wasiat Akhir",
                        "konten" => '
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4" dir="rtl" style="font-family: \'Amiri\', serif;">
                                اِعْلَمْ أَنَّ الدُّنْيَا كَالظِّلِّ الزَّائِلِ
                            </p>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <strong>Ketahuilah</strong> bahwa dunia ini ibarat bayangan yang akan segera berlalu. Maka berbekallah dengan taqwa sebelum datang kematian. Perbanyaklah istighfar dan selalu ingat akhirat yang kekal.
                            </p>
                        ',
                    ],
                ],
            ],
        ];

        foreach ($chapters as $chapterIndex => $chapterData) {
            $chapter = KitabChapter::create([
                "kitab_id" => $kitab->id, // Assign to kitab
                "nomor_bab" => $chapterData["nomor_bab"],
                "judul_bab" => $chapterData["judul_bab"],
                "deskripsi" => $chapterData["deskripsi"],
                "slug" => Str::slug("bab-" . $chapterData["nomor_bab"] . "-" . $chapterData["judul_bab"]),
                "urutan" => $chapterIndex + 1,
            ]);

            foreach ($chapterData["maqolahs"] as $maqolahIndex => $maqolahData) {
                KitabMaqolah::create([
                    "chapter_id" => $chapter->id,
                    "nomor_maqolah" => $maqolahIndex + 1,
                    "judul" => $maqolahData["judul"],
                    "konten" => $maqolahData["konten"],
                    "urutan" => $maqolahIndex + 1,
                ]);
            }
        }

        // Also assign any existing chapters without kitab_id to this kitab
        KitabChapter::whereNull('kitab_id')->update(['kitab_id' => $kitab->id]);

        $this->command->info("Kitab 'Nashaihul Ibad' seeded successfully!");
    }
}
